<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\DisposeBatchRequest;
use App\Http\Requests\ReceiveBatchRequest;
use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Delivery;
use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InventoryController extends Controller
{
    public function __construct(private readonly InventoryService $inventoryService) {}

    /**
     * Stock overview: medicine list with aggregate stock, alerts.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Medicine::class);

        $search = $request->input('search');
        $category = $request->input('category');
        $stockStatus = $request->input('stock_status');

        $medicines = Medicine::with(['stockBatches' => function ($q) {
            $q->where('expiry_date', '>=', now()->toDateString())
                ->where('quantity_remaining', '>', 0);
        }])
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            }))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('name')
            ->get();

        // Apply stock status filter client-side on the collection
        if ($stockStatus === 'low_stock') {
            $medicines = $medicines->filter(fn ($m) => $m->available_stock > 0 && $m->available_stock <= $m->reorder_level);
        } elseif ($stockStatus === 'out_of_stock') {
            $medicines = $medicines->filter(fn ($m) => $m->available_stock === 0);
        } elseif ($stockStatus === 'in_stock') {
            $medicines = $medicines->filter(fn ($m) => $m->available_stock > $m->reorder_level);
        }

        $categories = Medicine::distinct()->orderBy('category')->pluck('category');

        $allFormulary = Medicine::with(['stockBatches' => function ($q) {
            $q->where('status', 'received')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('quantity_remaining', '>', 0);
        }])->get();

        $totalItems = $allFormulary->count();
        $inStockCount = $allFormulary->filter(fn ($m) => $m->available_stock > $m->reorder_level)->count();
        $lowStockCount = $allFormulary->filter(fn ($m) => $m->available_stock > 0 && $m->available_stock <= $m->reorder_level)->count();
        $outOfStockCount = $allFormulary->filter(fn ($m) => $m->available_stock === 0)->count();
        $expiringSoonCount = StockBatch::expiringSoon(30)->count();

        // Calculate dynamic inventory valuation and profit metrics
        $totalStockValue = (float) $allFormulary->sum(fn ($m) => $m->available_stock * $m->cost_price);
        $totalSaleValue = (float) $allFormulary->sum(fn ($m) => $m->available_stock * $m->selling_price);
        $expectedProfit = (float) ($totalSaleValue - $totalStockValue);

        return view('inventory.index', compact(
            'medicines',
            'categories',
            'totalItems',
            'inStockCount',
            'lowStockCount',
            'outOfStockCount',
            'expiringSoonCount',
            'totalStockValue',
            'totalSaleValue',
            'expectedProfit',
            'search',
            'category',
            'stockStatus'
        ));
    }

    /**
     * Show form to create a new medicine.
     */
    public function create()
    {
        Gate::authorize('create', Medicine::class);

        return view('inventory.create');
    }

    /**
     * Save a new medicine to the catalogue.
     */
    public function store(StoreMedicineRequest $request)
    {
        $medicine = Medicine::create([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('inventory.medicines.show', $medicine)
            ->with('success', "Medicine \"{$medicine->name}\" added to the catalogue.");
    }

    /**
     * Display a single medicine with its batches and movement log.
     */
    public function show(Medicine $medicine)
    {
        Gate::authorize('view', $medicine);

        $medicine->load(['stockBatches' => function ($q) {
            $q->orderBy('expiry_date', 'asc');
        }]);

        $movements = StockMovement::with(['batch', 'createdBy'])
            ->where('medicine_id', $medicine->id)
            ->latest()
            ->take(20)
            ->get();

        $expiringSoonBatches = $medicine->stockBatches()
            ->expiringSoon(30)
            ->orderBy('expiry_date')
            ->get();

        return view('inventory.show', compact('medicine', 'movements', 'expiringSoonBatches'));
    }

    /**
     * Show form to edit a medicine.
     */
    public function edit(Medicine $medicine)
    {
        Gate::authorize('update', $medicine);

        return view('inventory.edit', compact('medicine'));
    }

    /**
     * Save medicine edits.
     */
    public function update(UpdateMedicineRequest $request, Medicine $medicine)
    {
        $medicine->update([
            ...$request->validated(),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('inventory.medicines.show', $medicine)
            ->with('success', "Medicine \"{$medicine->name}\" updated successfully.");
    }

    /**
     * Show the receive-batch form for a medicine (backward compatibility).
     */
    public function receiveBatch(Medicine $medicine)
    {
        Gate::authorize('receiveBatch', $medicine);

        return view('inventory.receive-batch', compact('medicine'));
    }

    /**
     * Process an incoming batch receipt (backward compatibility).
     */
    public function storeReceivedBatch(ReceiveBatchRequest $request, Medicine $medicine)
    {
        Gate::authorize('receiveBatch', $medicine);

        try {
            $batch = $this->inventoryService->receiveBatch(
                $medicine,
                $request->validated(),
                $request->user(),
                $request->has('auto_confirm') ? $request->boolean('auto_confirm') : true
            );

            $message = $batch->isReceived()
                ? "Batch {$batch->batch_no} received — {$batch->quantity_received} units added to stock."
                : "Batch {$batch->batch_no} logged as Pending Inspection. No stock added yet.";

            return redirect()
                ->route('inventory.medicines.show', $medicine)
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show form to create a multi-medicine delivery restock request.
     */
    public function createDelivery(Request $request)
    {
        Gate::authorize('create', Delivery::class);

        $medicines = Medicine::active()->orderBy('name')->get();
        $preselectedMedicineId = $request->query('medicine_id');

        return view('inventory.deliveries.create', compact('medicines', 'preselectedMedicineId'));
    }

    /**
     * Store a multi-medicine delivery request with status 'pending' (zero stock change).
     */
    public function storeDelivery(StoreDeliveryRequest $request)
    {
        Gate::authorize('create', Delivery::class);

        try {
            $delivery = $this->inventoryService->createDeliveryRequest(
                $request->only(['reference_no', 'supplier', 'delivery_date', 'notes']),
                $request->validated('items'),
                $request->user()
            );

            return redirect()
                ->route('inventory.movements')
                ->with('success', "Delivery request {$delivery->reference_no} created ({$delivery->items->count()} medicine(s)). Status: Pending Inspection. Usable stock will not change until confirmed.");
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm a pending delivery request and add requested quantities to stock.
     * Only Admin and Stock Manager are authorized.
     */
    public function confirmDeliveryRequest(Delivery $delivery, Request $request)
    {
        Gate::authorize('confirm', $delivery);

        try {
            $confirmed = $this->inventoryService->confirmDeliveryRequest($delivery, $request->user());

            return redirect()
                ->route('inventory.movements')
                ->with('success', "Delivery {$confirmed->reference_no} marked as Delivered / Received. All requested quantities have been added to inventory stock.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a pending delivery request without altering inventory stock.
     * Only Admin and Stock Manager are authorized.
     */
    public function cancelDeliveryRequest(Delivery $delivery, Request $request)
    {
        Gate::authorize('cancel', $delivery);

        try {
            $cancelled = $this->inventoryService->cancelDeliveryRequest(
                $delivery,
                $request->user(),
                $request->input('reason')
            );

            return redirect()
                ->route('inventory.movements')
                ->with('success', "Delivery {$cancelled->reference_no} has been canceled. No stock was added.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm a single pending batch into active inventory stock.
     */
    public function confirmDelivery(StockBatch $batch, Request $request)
    {
        Gate::authorize('receiveBatch', $batch->medicine);

        try {
            $batch = $this->inventoryService->confirmDelivery($batch, $request->user());

            return back()->with('success', "Batch {$batch->batch_no} confirmed — {$batch->quantity_received} units added to usable stock.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a single pending delivery batch.
     */
    public function rejectDelivery(StockBatch $batch, Request $request)
    {
        Gate::authorize('receiveBatch', $batch->medicine);

        try {
            $batch = $this->inventoryService->rejectDelivery($batch, $request->user(), $request->input('reason'));

            return back()->with('success', "Batch {$batch->batch_no} delivery has been rejected.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Dispose of a stock batch entirely.
     */
    public function disposeBatch(DisposeBatchRequest $request, StockBatch $batch)
    {
        Gate::authorize('disposeBatch', [$batch]);
        Gate::authorize('disposeBatch', $batch);

        try {
            $this->inventoryService->disposeBatch(
                $batch,
                $request->validated('reason'),
                $request->user()
            );

            return redirect()
                ->route('inventory.medicines.show', $batch->medicine_id)
                ->with('success', "Batch {$batch->batch_no} has been marked as disposed.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Adjust the quantity of a stock batch.
     */
    public function adjustStock(AdjustStockRequest $request, StockBatch $batch)
    {
        Gate::authorize('adjustStock', [$batch]);
        Gate::authorize('adjustStock', $batch);

        try {
            $this->inventoryService->adjustStock(
                $batch,
                $request->validated('new_quantity'),
                $request->validated('reason'),
                $request->user()
            );

            return redirect()
                ->route('inventory.medicines.show', $batch->medicine_id)
                ->with('success', "Stock for batch {$batch->batch_no} adjusted successfully.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Full delivery / movement log with delivery requests and type filter tabs.
     */
    public function movements(Request $request)
    {
        Gate::authorize('viewAny', Medicine::class);

        $type = $request->input('type');
        $search = $request->input('search');
        $deliveryStatus = $request->input('delivery_status');

        $deliveries = Delivery::with(['items.medicine', 'createdBy', 'receivedBy'])
            ->when($deliveryStatus, fn ($q) => $q->where('status', $deliveryStatus))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('reference_no', 'like', "%{$search}%")
                        ->orWhere('supplier', 'like', "%{$search}%")
                        ->orWhereHas('items.medicine', function ($medQ) use ($search) {
                            $medQ->where('name', 'like', "%{$search}%")
                                ->orWhere('generic_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15, ['*'], 'deliveries_page')
            ->withQueryString();

        $deliveryCounts = [
            'all' => Delivery::count(),
            'pending' => Delivery::pending()->count(),
            'delivered' => Delivery::delivered()->count(),
            'cancelled' => Delivery::cancelled()->count(),
        ];

        $movements = StockMovement::with(['medicine', 'batch', 'createdBy'])
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->whereHas('medicine', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('generic_name', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(25, ['*'], 'movements_page')
            ->withQueryString();

        return view('inventory.movements', compact(
            'deliveries',
            'deliveryCounts',
            'deliveryStatus',
            'movements',
            'type',
            'search'
        ));
    }

    /**
     * Export a clean, professional inventory valuation and stock report as PDF.
     */
    public function exportPdf(Request $request)
    {
        Gate::authorize('viewAny', Medicine::class);

        $medicines = Medicine::with(['stockBatches' => function ($q) {
            $q->where('status', 'received')
                ->where('expiry_date', '>=', now()->toDateString())
                ->where('quantity_remaining', '>', 0)
                ->orderBy('expiry_date', 'asc');
        }])
            ->orderBy('name')
            ->get();

        $totalItems = $medicines->count();
        $inStockCount = $medicines->filter(fn ($m) => $m->available_stock > $m->reorder_level)->count();
        $lowStockCount = $medicines->filter(fn ($m) => $m->available_stock > 0 && $m->available_stock <= $m->reorder_level)->count();
        $outOfStockCount = $medicines->filter(fn ($m) => $m->available_stock === 0)->count();
        $expiringSoonCount = StockBatch::expiringSoon(30)->count();

        $totalStockValue = (float) $medicines->sum(fn ($m) => $m->available_stock * $m->cost_price);
        $totalSaleValue = (float) $medicines->sum(fn ($m) => $m->available_stock * $m->selling_price);
        $expectedProfit = (float) ($totalSaleValue - $totalStockValue);

        $generatedAt = now();
        $generatedBy = $request->user()?->name ?? 'Pharmacy Staff';

        $pdf = Pdf::loadView('inventory.report-pdf', compact(
            'medicines',
            'totalItems',
            'inStockCount',
            'lowStockCount',
            'outOfStockCount',
            'expiringSoonCount',
            'totalStockValue',
            'totalSaleValue',
            'expectedProfit',
            'generatedAt',
            'generatedBy'
        ))->setPaper('a4', 'landscape')
            ->setOption('defaultFont', 'DejaVu Sans');

        $filename = 'USM_Pharmacy_Inventory_Report_'.now()->format('Ymd_His').'.pdf';

        return $request->boolean('stream')
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
