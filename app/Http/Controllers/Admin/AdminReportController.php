<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    /**
     * Display executive clinic and pharmacy reports.
     */
    public function index(): View
    {
        // 1. Inventory Valuation Summary
        $medicines = Medicine::with(['stockBatches' => function ($q) {
            $q->where('status', 'received')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('quantity_remaining', '>', 0);
        }])->get();

        $totalValuation = 0.0;
        $categoryBreakdown = [];

        foreach ($medicines as $med) {
            $cost = (float) ($med->purchase_price ?? ($med->unit_price * 0.7));
            $medVal = $med->available_stock * $cost;
            $totalValuation += $medVal;

            $unitKey = strtoupper($med->unit ?: 'OTHER');
            if (! isset($categoryBreakdown[$unitKey])) {
                $categoryBreakdown[$unitKey] = ['count' => 0, 'stock' => 0, 'value' => 0.0];
            }
            $categoryBreakdown[$unitKey]['count']++;
            $categoryBreakdown[$unitKey]['stock'] += $med->available_stock;
            $categoryBreakdown[$unitKey]['value'] += $medVal;
        }

        // 2. Spoilage / Loss Report (Disposed Batches)
        $disposedMovements = StockMovement::where('type', 'disposed')
            ->with(['medicine', 'batch', 'createdBy'])
            ->latest()
            ->get();

        $totalDisposedUnits = 0;
        $totalLossValue = 0.0;
        foreach ($disposedMovements as $mv) {
            $qty = abs($mv->quantity);
            $price = (float) ($mv->medicine?->purchase_price ?? ($mv->medicine?->unit_price * 0.7));
            $totalDisposedUnits += $qty;
            $totalLossValue += $qty * $price;
        }

        // 3. Top Dispensed Medicines (Combining RX & OTC)
        $topRxMedicines = PrescriptionItem::select('medicine_id', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('prescription', fn ($q) => $q->where('status', 'dispensed'))
            ->groupBy('medicine_id')
            ->orderByDesc('total_qty')
            ->with('medicine')
            ->take(8)
            ->get();

        $topOtcMedicines = PosTransactionItem::select('medicine_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('medicine_id')
            ->orderByDesc('total_qty')
            ->with('medicine')
            ->take(8)
            ->get();

        // 4. Financial Dispensary Overview
        $financials = [
            'otc_revenue' => (float) PosTransaction::sum('total_amount'),
            'otc_count' => PosTransaction::count(),
            'rx_dispensed_count' => Prescription::dispensed()->count(),
            'total_medicines' => $medicines->count(),
            'total_valuation' => $totalValuation,
            'total_loss_value' => $totalLossValue,
            'total_disposed_units' => $totalDisposedUnits,
        ];

        // 5. Patient Demographics
        $patientTypes = Patient::select('patient_type', DB::raw('count(*) as count'))
            ->groupBy('patient_type')
            ->pluck('count', 'patient_type')
            ->toArray();

        return view('admin.reports', compact(
            'totalValuation',
            'categoryBreakdown',
            'disposedMovements',
            'topRxMedicines',
            'topOtcMedicines',
            'financials',
            'patientTypes'
        ));
    }
}
