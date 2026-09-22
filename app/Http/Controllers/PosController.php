<?php

namespace App\Http\Controllers;

use App\Http\Requests\DispensePrescriptionRequest;
use App\Http\Requests\OtcSaleRequest;
use App\Models\Medicine;
use App\Models\PatientBill;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Services\DispensingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class PosController extends Controller
{
    public function __construct(private readonly DispensingService $dispensingService) {}

    public function index()
    {
        Gate::authorize('viewAny', PosTransaction::class);

        $prescriptions = Prescription::with(['patient.user', 'encodedBy', 'items.medicine.stockBatches'])
            ->whereIn('status', ['routed', 'prepared'])
            ->orderBy('created_at', 'asc')
            ->get();

        $patientBills = PatientBill::with(['patient.user', 'prescription', 'posTransaction.items.medicine', 'billedBy'])
            ->where('status', 'billed_to_account')
            ->latest()
            ->get();

        $medicines = Medicine::with(['stockBatches' => function ($query) {
            $query->where('quantity_remaining', '>', 0)
                ->where('expiry_date', '>=', now())
                ->orderBy('expiry_date', 'asc');
        }])->get();

        $transactions = PosTransaction::with(['cashier', 'prescription.patient.user', 'items.medicine', 'items.batch'])
            ->latest()
            ->limit(50)
            ->get();

        $mapPrescriptionToData = function ($rx) {
            $items = $rx->items->map(function ($item) {
                $batches = $this->dispensingService->suggestFefoBatches($item->medicine, $item->quantity);

                return [
                    'id' => $item->id,
                    'medicine_id' => $item->medicine_id,
                    'code' => $item->medicine->item_code,
                    'barcode' => $item->medicine->barcode,
                    'medicine' => $item->medicine->generic_name.' ('.$item->medicine->name.')',
                    'raw_name' => $item->medicine->name,
                    'dosage' => $item->dosage_instructions ?? 'As directed',
                    'qty' => $item->quantity,
                    'unit_price' => (float) $item->medicine->unit_price,
                    'stockout_risk_score' => $item->medicine->stockout_risk_score,
                    'stockout_risk_category' => $item->medicine->stockout_risk_category,
                    'batches' => array_map(function ($b) use ($item) {
                        $batchModel = $item->medicine->stockBatches->firstWhere('id', $b['batch_id']);

                        return [
                            'batch_id' => $b['batch_id'],
                            'batchNo' => $b['batch_no'],
                            'expiry' => $b['expiry_date'],
                            'unitPrice' => (float) $item->medicine->unit_price,
                            'qtyAvail' => $b['available'],
                            'qtyAllocated' => $b['quantity'],
                            'expiry_risk_score' => $batchModel?->expiry_risk_score,
                            'expiry_risk_category' => $batchModel?->expiry_risk_category,
                        ];
                    }, $batches),
                ];
            });

            return [
                'id' => 'RX-'.str_pad($rx->id, 5, '0', STR_PAD_LEFT),
                'raw_id' => $rx->id,
                'patient' => [
                    'name' => $rx->patient->name,
                    'id' => $rx->patient->id_number ?? 'PAT-'.str_pad($rx->patient->id, 4, '0', STR_PAD_LEFT),
                    'type' => $rx->patient->patient_type ?? 'Walk-in',
                ],
                'doctor' => $rx->doctor_name,
                'nurse' => $rx->encodedBy?->name ?? 'Staff Nurse',
                'order_type' => $rx->order_type ?? 'inpatient',
                'room_bed_number' => $rx->room_bed_number,
                'status' => $rx->status,
                'billing_status' => $rx->billing_status,
                'routedAt' => $rx->created_at->format('Y-m-d H:i'),
                'notes' => $rx->patient->medical_notes ?? null,
                'items' => $items,
            ];
        };

        $queueData = $prescriptions->map($mapPrescriptionToData);
        $inpatientQueueData = [];

        $patientBillsData = $patientBills->map(function ($bill) {
            return [
                'id' => $bill->id,
                'bill_no' => 'BILL-'.str_pad($bill->id, 6, '0', STR_PAD_LEFT),
                'patient_id' => $bill->patient_id,
                'patient_name' => $bill->patient->name,
                'patient_id_number' => $bill->patient->id_number,
                'patient_type' => $bill->patient->patient_type,
                'room_bed_number' => $bill->room_bed_number ?? 'N/A',
                'doctor_name' => $bill->doctor_name ?? 'Attending Doctor',
                'rx_number' => $bill->prescription ? 'RX-'.str_pad($bill->prescription->id, 5, '0', STR_PAD_LEFT) : 'N/A',
                'gross_amount' => (float) $bill->gross_amount,
                'discount_amount' => (float) $bill->discount_amount,
                'net_amount' => (float) $bill->net_amount,
                'status' => $bill->status,
                'billed_by' => $bill->billedBy?->name ?? 'Pharmacist',
                'billed_at' => $bill->created_at->format('Y-m-d H:i'),
                'items' => $bill->posTransaction?->items->map(function ($i) {
                    return [
                        'medicine' => $i->medicine?->generic_name ?? 'Medicine',
                        'quantity' => $i->quantity,
                        'unit_price' => (float) $i->unit_price,
                        'subtotal' => (float) $i->subtotal,
                    ];
                })->values()->all() ?? [],
            ];
        });

        $medicinesData = $medicines->map(function ($med) {
            return [
                'id' => $med->id,
                'code' => $med->item_code,
                'barcode' => $med->barcode,
                'name' => $med->generic_name.' ('.$med->name.')',
                'unitPrice' => (float) $med->unit_price,
                'available_stock' => (int) $med->stockBatches->sum('quantity_remaining'),
                'stockout_risk_score' => $med->stockout_risk_score,
                'stockout_risk_category' => $med->stockout_risk_category,
                'batches' => $med->stockBatches->map(function ($b) use ($med) {
                    return [
                        'batch_id' => $b->id,
                        'batchNo' => $b->batch_no,
                        'expiry' => $b->expiry_date,
                        'unitPrice' => (float) $med->unit_price,
                        'qtyAvail' => $b->quantity_remaining,
                        'expiry_risk_score' => $b->expiry_risk_score,
                        'expiry_risk_category' => $b->expiry_risk_category,
                    ];
                })->values()->all(),
            ];
        });

        $transactionsData = $transactions->map(function ($tx) {
            return [
                'id' => '#'.str_pad($tx->id, 8, '0', STR_PAD_LEFT),
                'raw_id' => $tx->id,
                'rxId' => $tx->prescription_id ? 'RX-'.str_pad($tx->prescription_id, 5, '0', STR_PAD_LEFT) : null,
                'patient' => $tx->prescription?->patient?->name ?? 'Walk-in Customer',
                'patientType' => $tx->prescription?->patient?->patient_type ?? 'Walk-in',
                'cashier' => $tx->cashier?->name ?? 'Pharmacist',
                'subtotal' => (float) ($tx->subtotal ?? $tx->total_amount),
                'discount_type' => $tx->discount_type ?? 'regular',
                'discount_id_number' => $tx->discount_id_number,
                'vat_exempt_amount' => (float) ($tx->vat_exempt_amount ?? 0),
                'discount_amount' => (float) ($tx->discount_amount ?? 0),
                'net_amount' => (float) ($tx->net_amount ?? $tx->total_amount),
                'total' => (float) $tx->total_amount,
                'method' => ucfirst(str_replace('_', ' ', $tx->payment_method)),
                'order_type' => $tx->order_type ?? 'outpatient',
                'room_bed_number' => $tx->room_bed_number,
                'billing_status' => $tx->billing_status ?? 'paid',
                'datetime' => $tx->created_at->format('Y-m-d H:i'),
                'items' => $tx->items->map(function ($item) {
                    return [
                        'medicine' => $item->medicine?->generic_name ?? 'Medicine',
                        'code' => $item->medicine?->item_code ?? '',
                        'barcode' => $item->medicine?->barcode ?? '',
                        'batchNo' => $item->batch?->batch_no ?? 'N/A',
                        'expiry' => $item->batch?->expiry_date ?? 'N/A',
                        'unitPrice' => (float) $item->unit_price,
                        'qty' => $item->quantity,
                    ];
                })->values()->all(),
            ];
        });

        return view('pos.index', compact(
            'prescriptions',
            'medicines',
            'transactions',
            'queueData',
            'inpatientQueueData',
            'patientBillsData',
            'medicinesData',
            'transactionsData'
        ));
    }

    public function process(Prescription $prescription)
    {
        Gate::authorize('create', PosTransaction::class);

        if (! in_array($prescription->status, ['routed', 'prepared'], true)) {
            return redirect()->route('pos.index')->with('error', 'Prescription is not ready for dispensing.');
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
                $request->validated('payment_method'),
                $request->validated('discount_type', 'regular') ?? 'regular',
                $request->validated('discount_id_number')
            );

            if ($request->wantsJson() || $request->ajax()) {
                $patientBillData = null;
                $bill = $transaction->patientBill;
                if ($bill) {
                    $bill->loadMissing(['patient', 'billedBy']);
                    $patientBillData = [
                        'id' => $bill->id,
                        'bill_no' => 'BILL-'.str_pad($bill->id, 6, '0', STR_PAD_LEFT),
                        'patient_id' => $bill->patient_id,
                        'patient_name' => $bill->patient->name,
                        'patient_id_number' => $bill->patient->id_number,
                        'patient_type' => $bill->patient->patient_type,
                        'room_bed_number' => $bill->room_bed_number ?? 'N/A',
                        'doctor_name' => $bill->doctor_name ?? 'Attending Doctor',
                        'rx_number' => $transaction->prescription ? 'RX-'.str_pad($transaction->prescription->id, 5, '0', STR_PAD_LEFT) : 'N/A',
                        'gross_amount' => (float) $bill->gross_amount,
                        'discount_amount' => (float) $bill->discount_amount,
                        'net_amount' => (float) $bill->net_amount,
                        'status' => $bill->status,
                        'billed_by' => $bill->billedBy?->name ?? 'Pharmacist',
                        'billed_at' => $bill->created_at->format('Y-m-d H:i'),
                        'items' => $transaction->items->map(function ($i) {
                            return [
                                'medicine' => $i->medicine?->generic_name ?? 'Medicine',
                                'quantity' => $i->quantity,
                                'unit_price' => (float) $i->unit_price,
                                'subtotal' => (float) $i->subtotal,
                            ];
                        })->values()->all(),
                    ];
                }

                return response()->json([
                    'success' => true,
                    'message' => $transaction->order_type === 'inpatient'
                        ? 'In-Patient order dispensed and charged to patient hospital bill successfully.'
                        : 'Prescription dispensed successfully.',
                    'transaction' => $this->formatTransactionForJson($transaction),
                    'patient_bill' => $patientBillData,
                ]);
            }

            return redirect()->route('pos.index', ['receipt' => $transaction->id])
                ->with('success', $transaction->order_type === 'inpatient'
                    ? 'In-Patient order dispensed and charged to patient hospital bill.'
                    : 'Prescription dispensed successfully.');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function markPrepared(Request $request, Prescription $prescription)
    {
        Gate::authorize('create', PosTransaction::class);

        if ($prescription->status !== 'routed') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Only routed orders can be marked as prepared.'], 422);
            }

            return back()->with('error', 'Only routed orders can be marked as prepared.');
        }

        $prescription->update(['status' => 'prepared']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Order {$prescription->prescription_number} prepared safely.",
                'rx_id' => $prescription->id,
            ]);
        }

        return back()->with('success', "Order {$prescription->prescription_number} prepared safely.");
    }

    public function settleBill(Request $request, PatientBill $bill)
    {
        Gate::authorize('create', PosTransaction::class);

        if ($bill->status === 'settled') {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Bill is already settled.'], 422);
            }

            return back()->with('error', 'Bill is already settled.');
        }

        $bill->update([
            'status' => 'settled',
            'settled_at' => now(),
            'settled_by' => $request->user()->id,
        ]);

        if ($bill->prescription) {
            $bill->prescription->update(['billing_status' => 'settled']);
        }
        if ($bill->posTransaction) {
            $bill->posTransaction->update(['billing_status' => 'settled']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Hospital Bill #{$bill->id} for {$bill->patient->name} settled successfully upon discharge.",
                'bill_id' => $bill->id,
            ]);
        }

        return back()->with('success', "Hospital Bill #{$bill->id} settled successfully.");
    }

    public function otcCreate()
    {
        Gate::authorize('create', PosTransaction::class);

        $medicines = Medicine::all();

        return view('pos.otc', compact('medicines'));
    }

    public function otcStore(OtcSaleRequest $request)
    {
        try {
            $transaction = $this->dispensingService->processOtcSale(
                $request->validated('items'),
                $request->user(),
                $request->validated('payment_method'),
                $request->validated('discount_type', 'regular') ?? 'regular',
                $request->validated('discount_id_number')
            );

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTC Sale completed successfully.',
                    'transaction' => $this->formatTransactionForJson($transaction),
                ]);
            }

            return redirect()->route('pos.index', ['receipt' => $transaction->id])->with('success', 'OTC Sale completed successfully.');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    private function formatTransactionForJson(PosTransaction $transaction): array
    {
        $transaction->loadMissing(['items.medicine', 'items.batch', 'cashier', 'prescription.patient']);

        return [
            'id' => '#'.str_pad($transaction->id, 8, '0', STR_PAD_LEFT),
            'raw_id' => $transaction->id,
            'rxId' => $transaction->prescription_id ? 'RX-'.str_pad($transaction->prescription_id, 5, '0', STR_PAD_LEFT) : null,
            'patient' => $transaction->prescription?->patient?->name ?? 'Walk-in Customer',
            'patientType' => $transaction->prescription?->patient?->patient_type ?? 'Walk-in',
            'cashier' => $transaction->cashier?->name ?? 'Pharmacist',
            'subtotal' => (float) ($transaction->subtotal ?? $transaction->total_amount),
            'discount_type' => $transaction->discount_type ?? 'regular',
            'discount_id_number' => $transaction->discount_id_number,
            'vat_exempt_amount' => (float) ($transaction->vat_exempt_amount ?? 0),
            'discount_amount' => (float) ($transaction->discount_amount ?? 0),
            'net_amount' => (float) ($transaction->net_amount ?? $transaction->total_amount),
            'total' => (float) $transaction->total_amount,
            'method' => ucfirst(str_replace('_', ' ', $transaction->payment_method)),
            'order_type' => $transaction->order_type ?? 'outpatient',
            'room_bed_number' => $transaction->room_bed_number,
            'billing_status' => $transaction->billing_status ?? 'paid',
            'datetime' => $transaction->created_at->format('Y-m-d H:i'),
            'items' => $transaction->items->map(function ($item) {
                return [
                    'medicine' => $item->medicine?->generic_name ?? 'Medicine',
                    'batchNo' => $item->batch?->batch_no ?? 'N/A',
                    'expiry' => $item->batch?->expiry_date ? Carbon::parse($item->batch->expiry_date)->format('Y-m-d') : 'N/A',
                    'unitPrice' => (float) $item->unit_price,
                    'qty' => $item->quantity,
                ];
            })->values()->all(),
        ];
    }

    public function receipt(PosTransaction $transaction)
    {
        Gate::authorize('view', $transaction);

        $transaction->load(['items.medicine', 'items.batch', 'cashier', 'prescription.patient']);

        return view('pos.receipt', compact('transaction'));
    }

    /**
     * Display executive reports and sales analytics generated from the POS.
     */
    public function reports(Request $request)
    {
        Gate::authorize('viewAny', PosTransaction::class);

        $period = $request->get('period', 'month');
        $startDate = match ($period) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            '7days' => now()->subDays(7)->startOfDay(),
            'month' => now()->startOfMonth(),
            'all' => now()->subYears(10),
            default => now()->startOfMonth(),
        };
        $endDate = match ($period) {
            'yesterday' => now()->subDay()->endOfDay(),
            default => now()->endOfDay(),
        };

        $query = PosTransaction::with(['cashier', 'prescription.patient.user', 'items.medicine', 'items.batch'])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $transactions = (clone $query)->latest()->get();

        $totalRevenue = (float) $transactions->sum('total_amount');
        $totalCount = $transactions->count();
        $prescriptionCount = $transactions->filter(fn ($t) => ! $t->isOtc())->count();
        $otcCount = $transactions->filter(fn ($t) => $t->isOtc())->count();
        $prescriptionRevenue = (float) $transactions->filter(fn ($t) => ! $t->isOtc())->sum('total_amount');
        $otcRevenue = (float) $transactions->filter(fn ($t) => $t->isOtc())->sum('total_amount');

        // Payment method breakdown
        $paymentMethods = [
            'Cash' => (float) $transactions->where('payment_method', 'cash')->sum('total_amount'),
            'Card' => (float) $transactions->where('payment_method', 'card')->sum('total_amount'),
            'PhilHealth / Insurance' => (float) $transactions->whereIn('payment_method', ['insurance', 'philhealth'])->sum('total_amount'),
            'HMO' => (float) $transactions->where('payment_method', 'hmo')->sum('total_amount'),
            'Institutional' => (float) $transactions->where('payment_method', 'institutional')->sum('total_amount'),
        ];

        // Top moving medicines during selected period
        $topMedicines = DB::table('pos_transaction_items')
            ->join('pos_transactions', 'pos_transaction_items.pos_transaction_id', '=', 'pos_transactions.id')
            ->join('medicines', 'pos_transaction_items.medicine_id', '=', 'medicines.id')
            ->whereBetween('pos_transactions.created_at', [$startDate, $endDate])
            ->select(
                'medicines.id',
                'medicines.name',
                'medicines.generic_name',
                'medicines.category',
                DB::raw('SUM(pos_transaction_items.quantity) as total_qty'),
                DB::raw('SUM(pos_transaction_items.subtotal) as total_revenue')
            )
            ->groupBy('medicines.id', 'medicines.name', 'medicines.generic_name', 'medicines.category')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        return view('pos.reports', compact(
            'transactions',
            'period',
            'totalRevenue',
            'totalCount',
            'prescriptionCount',
            'otcCount',
            'prescriptionRevenue',
            'otcRevenue',
            'paymentMethods',
            'topMedicines'
        ));
    }
}
