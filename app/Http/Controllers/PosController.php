<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispensePrescriptionRequest;
use App\Http\Requests\OtcSaleRequest;
use App\Models\Medicine;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Services\DispensingService;
use Exception;
use Illuminate\Support\Facades\Gate;

class PosController extends Controller
{
    public function __construct(private readonly DispensingService $dispensingService) {}

    public function index()
    {
        Gate::authorize('viewAny', PosTransaction::class);

        $prescriptions = Prescription::with(['patient'])
            ->where('status', 'routed')
            ->orderBy('created_at', 'asc')
            ->get();

        $transactions = PosTransaction::with(['cashier', 'prescription'])
            ->latest()
            ->limit(20)
            ->get();

        return view('pos.index', compact('prescriptions', 'transactions'));
    }

    public function process(Prescription $prescription)
    {
        Gate::authorize('create', PosTransaction::class);

        if ($prescription->status !== 'routed') {
            return redirect()->route('pos.index')->with('error', 'Prescription is not routed for dispensing.');
        }

        $prescription->load(['patient', 'items.medicine.stockBatches']);

        $suggestions = [];
        foreach ($prescription->items as $item) {
            $suggestions[$item->medicine_id] = $this->dispensingService->suggestFefoBatches($item->medicine, $item->quantity);
        }

        return view('pos.process', compact('prescription', 'suggestions'));
    }

    public function dispense(DispensePrescriptionRequest $request, Prescription $prescription)
    {
        try {
            $transaction = $this->dispensingService->dispensePrescription(
                $prescription,
                $request->validated('allocations'),
                $request->user(),
                $request->validated('payment_method')
            );

            return redirect()->route('pos.receipt', $transaction)->with('success', 'Prescription dispensed successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function otcCreate()
    {
        Gate::authorize('create', PosTransaction::class);

        $medicines = Medicine::where('is_active', true)->get();

        return view('pos.otc', compact('medicines'));
    }

    public function otcStore(OtcSaleRequest $request)
    {
        try {
            $transaction = $this->dispensingService->processOtcSale(
                $request->validated('items'),
                $request->user(),
                $request->validated('payment_method')
            );

            return redirect()->route('pos.receipt', $transaction)->with('success', 'OTC Sale completed successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receipt(PosTransaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['items.medicine', 'items.batch', 'cashier', 'prescription.patient']);

        return view('pos.receipt', compact('transaction'));
    }
}
