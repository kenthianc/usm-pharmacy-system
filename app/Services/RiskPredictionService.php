<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiskPredictionService
{
    /**
     * Default supplier lead time in days.
     */
    public const DEFAULT_LEAD_TIME_DAYS = 7;

    /**
     * Default consumption analysis window in days.
     */
    public const DEFAULT_WINDOW_DAYS = 30;

    /**
     * Triage risk thresholds.
     */
    public const THRESHOLD_LOW = 25.0;

    public const THRESHOLD_HIGH = 70.0;

    /**
     * Calculate average daily consumption rate (Dc) over a given window in days.
     */
    public function calculateDailyConsumptionRate(Medicine $medicine, int $windowDays = self::DEFAULT_WINDOW_DAYS): float
    {
        $since = Carbon::now()->subDays($windowDays);

        $dispensedUnits = (int) StockMovement::query()
            ->where('medicine_id', $medicine->id)
            ->where('type', 'out')
            ->where('created_at', '>=', $since)
            ->sum('quantity');

        return round($dispensedUnits / max(1, $windowDays), 4);
    }

    /**
     * Calculate Stockout Risk Score (Sr).
     *
     * Formula:
     * Sr = max(0, min(100, (1 - Ic / (Dc * Lt + Bs)) * 100))
     *
     * @return array{
     *     score: float,
     *     category: string,
     *     label: string,
     *     badge_class: string,
     *     action: string,
     *     current_stock: int,
     *     daily_consumption: float,
     *     lead_time_days: int,
     *     buffer_stock: int,
     *     reorder_point: float
     * }
     */
    public function calculateStockoutRisk(
        Medicine $medicine,
        ?int $leadTimeDays = null,
        ?float $dailyConsumption = null
    ): array {
        $leadTime = $leadTimeDays ?? self::DEFAULT_LEAD_TIME_DAYS;
        $dc = $dailyConsumption ?? $this->calculateDailyConsumptionRate($medicine);
        $ic = (int) $medicine->available_stock;
        $bs = (int) $medicine->reorder_level;

        $targetThreshold = ($dc * $leadTime) + $bs;

        if ($targetThreshold <= 0) {
            $score = $ic > 0 ? 0.0 : 100.0;
        } elseif ($ic >= $targetThreshold) {
            $score = 0.0;
        } else {
            $rawScore = (1.0 - ($ic / $targetThreshold)) * 100.0;
            $score = round(max(0.0, min(100.0, $rawScore)), 2);
        }

        $triage = $this->categorizeScore($score);

        return [
            'score' => $score,
            'category' => $triage['category'],
            'label' => $triage['label'],
            'badge_class' => $triage['badge_class'],
            'action' => $triage['action'],
            'current_stock' => $ic,
            'daily_consumption' => round($dc, 2),
            'lead_time_days' => $leadTime,
            'buffer_stock' => $bs,
            'reorder_point' => round($targetThreshold, 2),
        ];
    }

    /**
     * Calculate Expiry Risk Score (Er) for an individual stock batch.
     *
     * Formula:
     * Er = max(0, min(100, (1 - (Dc * Te) / Ic) * 100))
     *
     * @return array{
     *     score: float,
     *     category: string,
     *     label: string,
     *     badge_class: string,
     *     action: string,
     *     remaining_stock: int,
     *     days_until_expiry: int,
     *     daily_consumption: float,
     *     projected_consumption: float,
     *     projected_loss_units: float
     * }
     */
    public function calculateExpiryRisk(StockBatch $batch, ?float $dailyConsumption = null): array
    {
        $ic = (int) $batch->quantity_remaining;
        $today = Carbon::today();
        $te = (int) $today->diffInDays($batch->expiry_date, false);

        $dc = $dailyConsumption ?? ($batch->medicine ? $this->calculateDailyConsumptionRate($batch->medicine) : 0.0);

        if ($ic <= 0) {
            // No stock left in this batch, zero risk of loss
            $score = 0.0;
        } elseif ($te <= 0) {
            // Already expired with remaining inventory
            $score = 100.0;
        } elseif ($dc <= 0.0) {
            // Zero consumption velocity with active stock -> all units will expire
            $score = 100.0;
        } else {
            $projectedDemand = $dc * $te;
            if ($projectedDemand >= $ic) {
                $score = 0.0;
            } else {
                $rawScore = (1.0 - ($projectedDemand / $ic)) * 100.0;
                $score = round(max(0.0, min(100.0, $rawScore)), 2);
            }
        }

        $triage = $this->categorizeScore($score);
        $projectedConsumption = round($dc * max(0, $te), 2);
        $projectedLossUnits = max(0.0, round($ic - $projectedConsumption, 2));

        return [
            'score' => $score,
            'category' => $triage['category'],
            'label' => $triage['label'],
            'badge_class' => $triage['badge_class'],
            'action' => $triage['action'],
            'remaining_stock' => $ic,
            'days_until_expiry' => $te,
            'daily_consumption' => round($dc, 2),
            'projected_consumption' => $projectedConsumption,
            'projected_loss_units' => $projectedLossUnits,
        ];
    }

    /**
     * Map a numerical risk score (0-100) to clinical triage metadata.
     *
     * @return array{
     *     category: string,
     *     label: string,
     *     badge_class: string,
     *     action: string
     * }
     */
    public function categorizeScore(float $score): array
    {
        if ($score < self::THRESHOLD_LOW) {
            return [
                'category' => 'low',
                'label' => 'Low Risk',
                'badge_class' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
                'action' => 'Normal FEFO rotation',
            ];
        }

        if ($score <= self::THRESHOLD_HIGH) {
            return [
                'category' => 'moderate',
                'label' => 'Moderate Risk',
                'badge_class' => 'bg-amber-100 text-amber-900 border border-amber-300',
                'action' => 'Monitor, plan PO restock or flag near-expiry batches',
            ];
        }

        return [
            'category' => 'high',
            'label' => 'High Risk',
            'badge_class' => 'bg-rose-100 text-rose-800 border border-rose-300',
            'action' => 'Urgent restock PO trigger or batch return/disposal action',
        ];
    }

    /**
     * Recalculate and persist risk metrics for all medicines and stock batches.
     *
     * @return array{
     *     medicines_processed: int,
     *     batches_processed: int,
     *     high_stockout_count: int,
     *     high_expiry_count: int
     * }
     */
    public function recalculateAllRisks(int $leadTimeDays = self::DEFAULT_LEAD_TIME_DAYS): array
    {
        $medicinesProcessed = 0;
        $batchesProcessed = 0;
        $highStockoutCount = 0;
        $highExpiryCount = 0;

        $medicines = Medicine::with(['stockBatches'])->get();

        DB::transaction(function () use (
            $medicines,
            $leadTimeDays,
            &$medicinesProcessed,
            &$batchesProcessed,
            &$highStockoutCount,
            &$highExpiryCount
        ) {
            foreach ($medicines as $medicine) {
                $dailyConsumption = $this->calculateDailyConsumptionRate($medicine);
                $stockoutRisk = $this->calculateStockoutRisk($medicine, $leadTimeDays, $dailyConsumption);

                $medicine->update([
                    'stockout_risk_score' => $stockoutRisk['score'],
                    'stockout_risk_category' => $stockoutRisk['category'],
                    'daily_consumption_rate' => $dailyConsumption,
                ]);

                $medicinesProcessed++;
                if ($stockoutRisk['category'] === 'high') {
                    $highStockoutCount++;
                }

                // Update batches associated with this medicine
                foreach ($medicine->stockBatches as $batch) {
                    $expiryRisk = $this->calculateExpiryRisk($batch, $dailyConsumption);

                    $batch->update([
                        'expiry_risk_score' => $expiryRisk['score'],
                        'expiry_risk_category' => $expiryRisk['category'],
                    ]);

                    $batchesProcessed++;
                    if ($expiryRisk['category'] === 'high') {
                        $highExpiryCount++;
                    }
                }
            }
        });

        return [
            'medicines_processed' => $medicinesProcessed,
            'batches_processed' => $batchesProcessed,
            'high_stockout_count' => $highStockoutCount,
            'high_expiry_count' => $highExpiryCount,
        ];
    }

    /**
     * Generate comprehensive dual-engine telemetry, diagnostic narratives, and financial risk forecasts.
     *
     * @return array{
     *     telemetry: array<string, mixed>,
     *     stockout_insights: array<int, array<string, mixed>>,
     *     expiry_insights: array<int, array<string, mixed>>,
     *     paired_insights: array<int, array<string, mixed>>,
     *     total_alerts_count: int
     * }
     */
    public function getDualEngineInsights(int $leadTimeDays = self::DEFAULT_LEAD_TIME_DAYS): array
    {
        $medicines = Medicine::with(['stockBatches'])->get();

        $stockoutInsights = [];
        $expiryInsights = [];
        $totalLossAtRisk = 0.0;
        $highStockout = 0;
        $modStockout = 0;
        $highExpiry = 0;
        $modExpiry = 0;
        $totalBatches = 0;

        foreach ($medicines as $med) {
            $dc = (float) ($med->daily_consumption_rate ?? $this->calculateDailyConsumptionRate($med));
            $stockout = $this->calculateStockoutRisk($med, $leadTimeDays, $dc);

            $availableStock = (int) $med->available_stock;
            $unit = $med->unit ?? 'units';

            // Calculate days of supply horizon and deficit
            $daysUntilDepleted = $dc > 0 ? round($availableStock / $dc, 1) : ($availableStock === 0 ? 0.0 : null);
            $supplyDeficitDays = ($daysUntilDepleted !== null && $daysUntilDepleted < $leadTimeDays)
                ? round($leadTimeDays - $daysUntilDepleted, 1)
                : 0.0;

            // Generate concise clinical narrative for Stockout Risk
            if ($availableStock === 0) {
                $stockoutNarrative = "Stock depleted (0 {$unit}). Urgent reorder required.";
            } elseif ($supplyDeficitDays > 0) {
                $stockoutNarrative = "Depletes in ~{$daysUntilDepleted} days. Forecasted {$supplyDeficitDays}d supply deficit before delivery.";
            } elseif ($stockout['category'] === 'moderate') {
                $stockoutNarrative = "Approaching reorder buffer ({$med->reorder_level} {$unit}). Prepare restock order.";
            } else {
                $stockoutNarrative = 'Stock level stable (~'.($daysUntilDepleted ?? '∞').'d supply).';
            }

            if ($stockout['category'] === 'high') {
                $highStockout++;
            } elseif ($stockout['category'] === 'moderate') {
                $modStockout++;
            }

            if (in_array($stockout['category'], ['high', 'moderate'])) {
                $stockoutInsights[] = [
                    'medicine_id' => $med->id,
                    'code' => $med->item_code,
                    'barcode' => $med->barcode,
                    'medicine_name' => trim(str_replace('(Out of Stock Demo)', '', $med->name)),
                    'generic_name' => $med->generic_name,
                    'category' => $med->category,
                    'unit' => $unit,
                    'current_stock' => $availableStock,
                    'reorder_level' => $med->reorder_level,
                    'daily_consumption' => $dc,
                    'lead_time_days' => $leadTimeDays,
                    'days_until_depleted' => $daysUntilDepleted,
                    'supply_deficit_days' => $supplyDeficitDays,
                    'score' => $stockout['score'],
                    'risk_category' => $stockout['category'],
                    'label' => $stockout['label'],
                    'badge_class' => $stockout['badge_class'],
                    'action' => $stockout['action'],
                    'narrative' => $stockoutNarrative,
                ];
            }

            // Analyze batches for Expiry Risk
            foreach ($med->stockBatches as $batch) {
                $totalBatches++;
                $expiry = $this->calculateExpiryRisk($batch, $dc);
                $remaining = (int) $batch->quantity_remaining;
                $te = (int) Carbon::today()->diffInDays($batch->expiry_date, false);
                $costPrice = (float) $med->cost_price;
                $financialLoss = round($expiry['projected_loss_units'] * $costPrice, 2);

                if ($expiry['category'] === 'high') {
                    $highExpiry++;
                } elseif ($expiry['category'] === 'moderate') {
                    $modExpiry++;
                }

                if ($remaining > 0 && in_array($expiry['category'], ['high', 'moderate'])) {
                    $totalLossAtRisk += $financialLoss;

                    if ($te <= 0) {
                        $expiryNarrative = "Batch expired ({$remaining} {$unit} on shelf, ₱".number_format($financialLoss, 2).' loss). Quarantine for disposal.';
                    } elseif ($expiry['projected_loss_units'] > 0) {
                        $expiryNarrative = "{$expiry['projected_loss_units']} {$unit} (₱".number_format($financialLoss, 2).") projected to expire unused in {$te} days.";
                    } else {
                        $expiryNarrative = "Zero dispensing velocity. Active batch ({$remaining} {$unit}) risks expiration.";
                    }

                    $expiryInsights[] = [
                        'batch_id' => $batch->id,
                        'batch_no' => $batch->batch_no,
                        'medicine_id' => $med->id,
                        'code' => $med->item_code,
                        'medicine_name' => trim(str_replace('(Out of Stock Demo)', '', $med->name)),
                        'supplier' => $batch->supplier ?? 'Primary Supplier',
                        'unit' => $unit,
                        'remaining_stock' => $remaining,
                        'expiry_date' => $batch->expiry_date->format('M d, Y'),
                        'days_until_expiry' => $te,
                        'score' => $expiry['score'],
                        'risk_category' => $expiry['category'],
                        'label' => $expiry['label'],
                        'badge_class' => $expiry['badge_class'],
                        'action' => $expiry['action'],
                        'projected_loss_units' => $expiry['projected_loss_units'],
                        'financial_loss' => $financialLoss,
                        'narrative' => $expiryNarrative,
                    ];
                }
            }
        }

        // Sort by risk score descending
        usort($stockoutInsights, fn ($a, $b) => $b['score'] <=> $a['score']);
        usort($expiryInsights, fn ($a, $b) => $b['score'] <=> $a['score']);

        $totalAlertsCount = count($stockoutInsights) + count($expiryInsights);

        return [
            'telemetry' => [
                'status' => 'ONLINE',
                'mode' => 'DUAL_PREDICTIVE_CORE',
                'analyzed_medicines_count' => $medicines->count(),
                'analyzed_batches_count' => $totalBatches,
                'high_stockout_count' => $highStockout,
                'moderate_stockout_count' => $modStockout,
                'high_expiry_count' => $highExpiry,
                'moderate_expiry_count' => $modExpiry,
                'total_financial_loss_at_risk' => round($totalLossAtRisk, 2),
                'lead_time_days' => $leadTimeDays,
                'velocity_window_days' => self::DEFAULT_WINDOW_DAYS,
                'evaluated_at' => Carbon::now()->format('M d, Y h:i A'),
            ],
            'stockout_insights' => $stockoutInsights,
            'expiry_insights' => $expiryInsights,
            'total_alerts_count' => $totalAlertsCount,
        ];
    }
}
