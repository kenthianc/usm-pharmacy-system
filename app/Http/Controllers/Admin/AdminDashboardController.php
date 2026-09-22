<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the Executive Admin Dashboard.
     */
    public function index(): View
    {
        // Staff & User counts
        $staffCounts = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'admins' => User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count(),
            'nurses' => User::whereHas('roles', fn ($q) => $q->where('name', 'nurse'))->count(),
            'pharmacists' => User::whereHas('roles', fn ($q) => $q->where('name', 'pharmacist'))->count(),
            'stock_managers' => User::whereHas('roles', fn ($q) => $q->where('name', 'stock_manager'))->count(),
            'patients' => Patient::count(),
        ];

        // Inventory & Batches
        $medicines = Medicine::with(['stockBatches' => function ($query) {
            $query->where('status', 'received')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('quantity_remaining', '>', 0);
        }])->get();

        $inventoryValue = 0.0;
        foreach ($medicines as $medicine) {
            $cost = (float) ($medicine->purchase_price ?? ($medicine->unit_price * 0.7));
            $inventoryValue += $medicine->available_stock * $cost;
        }

        $inventoryCounts = [
            'total_medicines' => $medicines->count(),
            'valuation' => $inventoryValue,
            'in_stock' => $medicines->filter(fn ($m) => $m->available_stock > $m->reorder_level)->count(),
            'low_stock' => $medicines->filter(fn ($m) => $m->available_stock > 0 && $m->available_stock <= $m->reorder_level)->count(),
            'out_of_stock' => $medicines->filter(fn ($m) => $m->available_stock === 0)->count(),
            'expiring_soon' => StockBatch::expiringSoon(30)->count(),
        ];

        // Prescriptions metrics
        $rxCounts = [
            'total' => Prescription::count(),
            'pending' => Prescription::pending()->count(),
            'routed' => Prescription::routed()->count(),
            'dispensed' => Prescription::dispensed()->count(),
            'cancelled' => Prescription::cancelled()->count(),
        ];

        // POS OTC metrics
        $posStats = [
            'total_sales' => (float) PosTransaction::sum('total_amount'),
            'transaction_count' => PosTransaction::count(),
            'today_sales' => (float) PosTransaction::whereDate('created_at', now()->toDateString())->sum('total_amount'),
        ];

        // Disposed losses
        $disposedMovements = StockMovement::where('type', 'disposed')->with('medicine')->get();
        $totalLoss = 0.0;
        foreach ($disposedMovements as $mv) {
            $price = (float) ($mv->medicine?->purchase_price ?? ($mv->medicine?->unit_price * 0.7));
            $totalLoss += abs($mv->quantity) * $price;
        }

        // Recent Audit logs
        $recentAuditLogs = AuditLog::with('user')
            ->latest()
            ->take(8)
            ->get();

        // Recent Critical movements
        $recentMovements = StockMovement::with(['medicine', 'batch', 'createdBy'])
            ->latest()
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'staffCounts',
            'inventoryCounts',
            'rxCounts',
            'posStats',
            'totalLoss',
            'recentAuditLogs',
            'recentMovements'
        ));
    }
}
