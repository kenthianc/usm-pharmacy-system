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
                'level' => 1,
                'level_name' => 'Level 1',
                'level_label' => 'Level 1 (Low)',
                'label' => 'Low Risk',
                'badge_class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                'action' => 'Normal FEFO rotation',
            ];
        }

        if ($score <= self::THRESHOLD_HIGH) {
            return [
                'category' => 'moderate',
                'level' => 2,
                'level_name' => 'Level 2',
                'level_label' => 'Level 2 (Moderate)',
                'label' => 'Moderate Risk',
                'badge_class' => 'bg-amber-50 text-amber-800 border border-amber-200',
                'action' => 'Monitor, plan PO restock or flag near-expiry batches',
            ];
        }

        return [
            'category' => 'high',
            'level' => 3,
            'level_name' => 'Level 3',
            'level_label' => 'Level 3 (High)',
            'label' => 'High Risk',
            'badge_class' => 'bg-orange-50 text-orange-800 border border-orange-200',
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

        // First pass: identify medicines with stockout risks and batches with expiry risks
        $rawStockout = [];
        $rawExpiry = [];
        $medsWithStockout = [];
        $medsWithExpiry = [];

        foreach ($medicines as $med) {
            $dc = (float) ($med->daily_consumption_rate ?? $this->calculateDailyConsumptionRate($med));
            $stockout = $this->calculateStockoutRisk($med, $leadTimeDays, $dc);

            $availableStock = (int) $med->available_stock;
            $unit = $med->unit ?? 'units';

            $daysUntilDepleted = $dc > 0 ? round($availableStock / $dc, 1) : ($availableStock === 0 ? 0.0 : null);
            $supplyDeficitDays = ($daysUntilDepleted !== null && $daysUntilDepleted < $leadTimeDays)
                ? round($leadTimeDays - $daysUntilDepleted, 1)
                : 0.0;

            if ($stockout['category'] === 'high') {
                $highStockout++;
            } elseif ($stockout['category'] === 'moderate') {
                $modStockout++;
            }

            if (in_array($stockout['category'], ['high', 'moderate'])) {
                $medsWithStockout[$med->id] = true;
                $rawStockout[] = [
                    'medicine' => $med,
                    'stockout' => $stockout,
                    'available_stock' => $availableStock,
                    'unit' => $unit,
                    'dc' => $dc,
                    'days_until_depleted' => $daysUntilDepleted,
                    'supply_deficit_days' => $supplyDeficitDays,
                ];
            }

            foreach ($med->stockBatches as $batch) {
                $totalBatches++;
                $expiry = $this->calculateExpiryRisk($batch, $dc);
                $remaining = (int) $batch->quantity_remaining;
                $te = (int) Carbon::today()->diffInDays($batch->expiry_date, false);
                $unitCost = (float) ($med->purchase_price ?? $med->unit_price ?? 0.0);
                $financialLoss = round($expiry['projected_loss_units'] * $unitCost, 2);

                if ($expiry['category'] === 'high') {
                    $highExpiry++;
                } elseif ($expiry['category'] === 'moderate') {
                    $modExpiry++;
                }

                if ($remaining > 0 && in_array($expiry['category'], ['high', 'moderate'])) {
                    $totalLossAtRisk += $financialLoss;
                    $medsWithExpiry[$med->id] = true;
                    $rawExpiry[] = [
                        'medicine' => $med,
                        'batch' => $batch,
                        'expiry' => $expiry,
                        'remaining' => $remaining,
                        'te' => $te,
                        'unit_cost' => $unitCost,
                        'financial_loss' => $financialLoss,
                    ];
                }
            }
        }

        // Second pass: build finalized insights with dual-risk detection and color coding
        foreach ($rawStockout as $entry) {
            $med = $entry['medicine'];
            $stockout = $entry['stockout'];
            $isDualRisk = isset($medsWithExpiry[$med->id]);
            $level = $stockout['level'] ?? ($stockout['category'] === 'high' ? 3 : ($stockout['category'] === 'moderate' ? 2 : 1));
            $scorePercent = (int) round($stockout['score']);

            if ($isDualRisk) {
                $badgeClass = 'bg-rose-50 text-rose-700 border border-rose-300 font-bold';
                $scoreChip = "Dual · L{$level} - {$scorePercent}%";
                $levelDisplay = "Dual Risk · Level {$level}";
                $riskCount = 2;
            } elseif ($level === 3) {
                $badgeClass = 'bg-orange-50 text-orange-700 border border-orange-200 font-semibold';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 3 (High)';
                $riskCount = 1;
            } elseif ($level === 2) {
                $badgeClass = 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 2 (Moderate)';
                $riskCount = 1;
            } else {
                $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 1 (Low)';
                $riskCount = 0;
            }

            if ($entry['available_stock'] === 0) {
                $dosrLabel = '0d (Depleted)';
                $stockoutNarrative = 'Out of stock — reorder needed';
            } elseif ($entry['dc'] <= 0.0) {
                $dosrLabel = 'Stagnant (0 run rate)';
                $stockoutNarrative = "Below buffer ({$med->reorder_level} {$entry['unit']}), no 30d velocity";
            } else {
                $dosrLabel = round($entry['days_until_depleted'], 1).'d';
                if ($entry['supply_deficit_days'] > 0) {
                    $stockoutNarrative = "Depletes in ~{$dosrLabel} ({$entry['supply_deficit_days']}d delivery gap)";
                } elseif ($stockout['category'] === 'moderate') {
                    $stockoutNarrative = "Buffer reached ({$med->reorder_level} {$entry['unit']})";
                } else {
                    $stockoutNarrative = "Adequate stock (~{$dosrLabel} supply)";
                }
            }

            $stockoutInsights[] = [
                'medicine_id' => $med->id,
                'code' => $med->item_code,
                'barcode' => $med->barcode,
                'medicine_name' => trim(str_replace('(Out of Stock Demo)', '', $med->name)),
                'generic_name' => $med->generic_name,
                'category' => $med->category,
                'unit' => $entry['unit'],
                'current_stock' => $entry['available_stock'],
                'buffer_stock' => (int) $med->reorder_level,
                'reorder_level' => (int) $med->reorder_level,
                'reorder_point' => (float) $stockout['reorder_point'],
                'daily_consumption' => round($entry['dc'], 2),
                'lead_time_days' => $leadTimeDays,
                'days_until_depleted' => $entry['days_until_depleted'],
                'dosr_label' => $dosrLabel,
                'supply_deficit_days' => $entry['supply_deficit_days'],
                'score' => $stockout['score'],
                'risk_category' => $stockout['category'],
                'level' => $level,
                'level_name' => "Level {$level}",
                'level_display' => $levelDisplay,
                'score_chip' => $scoreChip,
                'is_dual_risk' => $isDualRisk,
                'risk_count' => $riskCount,
                'label' => $stockout['label'],
                'badge_class' => $badgeClass,
                'action' => $stockout['action'],
                'narrative' => $stockoutNarrative,
            ];
        }

        foreach ($rawExpiry as $entry) {
            $med = $entry['medicine'];
            $batch = $entry['batch'];
            $expiry = $entry['expiry'];
            $isDualRisk = isset($medsWithStockout[$med->id]);
            $level = $expiry['level'] ?? ($expiry['category'] === 'high' ? 3 : ($expiry['category'] === 'moderate' ? 2 : 1));
            $scorePercent = (int) round($expiry['score']);

            if ($isDualRisk) {
                $badgeClass = 'bg-rose-50 text-rose-700 border border-rose-300 font-bold';
                $scoreChip = "Dual · L{$level} - {$scorePercent}%";
                $levelDisplay = "Dual Risk · Level {$level}";
                $riskCount = 2;
            } elseif ($level === 3) {
                $badgeClass = 'bg-orange-50 text-orange-700 border border-orange-200 font-semibold';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 3 (High)';
                $riskCount = 1;
            } elseif ($level === 2) {
                $badgeClass = 'bg-amber-50 text-amber-800 border border-amber-200 font-semibold';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 2 (Moderate)';
                $riskCount = 1;
            } else {
                $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200 font-medium';
                $scoreChip = "L{$level} - {$scorePercent}%";
                $levelDisplay = 'Level 1 (Low)';
                $riskCount = 0;
            }

            $shelfLifeLabel = $entry['te'] <= 0 ? '0d (Expired)' : $entry['te'].'d left';

            if ($entry['te'] <= 0) {
                $expiryNarrative = "Expired ({$entry['remaining']} {$med->unit}) — quarantine";
            } elseif ($expiry['projected_loss_units'] > 0) {
                $expiryNarrative = "{$expiry['projected_loss_units']} {$med->unit} at risk ({$shelfLifeLabel})";
            } else {
                $expiryNarrative = "Zero movement ({$shelfLifeLabel})";
            }

            $expiryInsights[] = [
                'batch_id' => $batch->id,
                'batch_no' => $batch->batch_no,
                'medicine_id' => $med->id,
                'code' => $med->item_code,
                'medicine_name' => trim(str_replace('(Out of Stock Demo)', '', $med->name)),
                'supplier' => $batch->supplier ?? 'Primary Supplier',
                'unit' => $med->unit ?? 'units',
                'remaining_stock' => $entry['remaining'],
                'expiry_date' => $batch->expiry_date->format('M d, Y'),
                'days_until_expiry' => $entry['te'],
                'shelf_life_label' => $shelfLifeLabel,
                'daily_consumption' => round((float) ($med->daily_consumption_rate ?? $this->calculateDailyConsumptionRate($med)), 2),
                'projected_consumption' => $expiry['projected_consumption'],
                'projected_loss_units' => $expiry['projected_loss_units'],
                'unit_cost' => $entry['unit_cost'],
                'financial_loss' => $entry['financial_loss'],
                'score' => $expiry['score'],
                'risk_category' => $expiry['category'],
                'level' => $level,
                'level_name' => "Level {$level}",
                'level_display' => $levelDisplay,
                'score_chip' => $scoreChip,
                'is_dual_risk' => $isDualRisk,
                'risk_count' => $riskCount,
                'label' => $expiry['label'],
                'badge_class' => $badgeClass,
                'action' => $expiry['action'],
                'narrative' => $expiryNarrative,
            ];
        }

        // Sort by risk score descending
        usort($stockoutInsights, fn ($a, $b) => $b['score'] <=> $a['score']);
        usort($expiryInsights, fn ($a, $b) => $b['score'] <=> $a['score']);

        $totalAlertsCount = count($stockoutInsights) + count($expiryInsights);

        // Aggregate Global Risk calculation
        // Requirements:
        // * Danger / Red: IF max(S_r) > 70% OR max(E_r) > 70% OR any DoSR <= Lead Time.
        // * Warning / Amber: IF any S_r or E_r is between 25% and 70%.
        // * Nominal / Green: ONLY IF all items are < 25%.
        $maxSr = ! empty($stockoutInsights) ? max(array_column($stockoutInsights, 'score')) : 0.0;
        $maxEr = ! empty($expiryInsights) ? max(array_column($expiryInsights, 'score')) : 0.0;

        $hasCriticalDosr = false;
        foreach ($stockoutInsights as $si) {
            if ($si['days_until_depleted'] !== null && $si['days_until_depleted'] <= $leadTimeDays) {
                $hasCriticalDosr = true;
                break;
            }
        }

        if ($maxSr > self::THRESHOLD_HIGH || $maxEr > self::THRESHOLD_HIGH || $hasCriticalDosr) {
            $globalRisk = [
                'status' => 'danger',
                'level' => 3,
                'label' => 'Risk Summary · Critical',
                'badge_class' => 'border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100',
                'dot_class' => 'bg-rose-500 animate-pulse',
                'pill_title' => 'Critical risks active: Immediate stock replenishment or batch action needed',
            ];
        } elseif ($maxSr >= self::THRESHOLD_LOW || $maxEr >= self::THRESHOLD_LOW) {
            $globalRisk = [
                'status' => 'warning',
                'level' => 2,
                'label' => 'Risk Summary · Attention',
                'badge_class' => 'border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100',
                'dot_class' => 'bg-amber-500',
                'pill_title' => 'Moderate risks flagged: Inventory monitoring recommended',
            ];
        } else {
            $globalRisk = [
                'status' => 'nominal',
                'level' => 1,
                'label' => 'Risk Summary · Nominal',
                'badge_class' => 'border-emerald-400 bg-emerald-50/70 text-emerald-800 hover:bg-emerald-100',
                'dot_class' => 'bg-emerald-500',
                'pill_title' => 'All inventory levels within safe clinical thresholds',
            ];
        }

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
                'max_stockout_score' => $maxSr,
                'max_expiry_score' => $maxEr,
                'total_financial_loss_at_risk' => round($totalLossAtRisk, 2),
                'lead_time_days' => $leadTimeDays,
                'velocity_window_days' => self::DEFAULT_WINDOW_DAYS,
                'evaluated_at' => Carbon::now()->format('M d, Y h:i A'),
            ],
            'global_risk' => $globalRisk,
            'stockout_insights' => $stockoutInsights,
            'expiry_insights' => $expiryInsights,
            'total_alerts_count' => $totalAlertsCount,
        ];
    }

    /**
     * Get standalone aggregated global risk status for header pills and widgets.
     *
     * @return array{
     *     status: string,
     *     level: int,
     *     label: string,
     *     badge_class: string,
     *     dot_class: string,
     *     pill_title: string
     * }
     */
    public function getGlobalRiskStatus(int $leadTimeDays = self::DEFAULT_LEAD_TIME_DAYS): array
    {
        $insights = $this->getDualEngineInsights($leadTimeDays);

        return $insights['global_risk'];
    }
}
