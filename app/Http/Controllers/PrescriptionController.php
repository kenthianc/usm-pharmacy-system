<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Prescription::class);

        $status = $request->query('status');
        $search = $request->query('search');
        $scope = $request->query('scope', 'all');

        $query = Prescription::with(['patient.user', 'encodedBy', 'items.medicine'])
            ->latest();

        // Scope to user's encoded prescriptions if requested and not admin
        if ($scope === 'mine' && ! $request->user()->hasRole('admin')) {
            $query->where('encoded_by', $request->user()->id);
        }

        if ($status && in_array($status, ['pending', 'routed', 'dispensed', 'cancelled'], true)) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('id_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $prescriptions = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => Prescription::count(),
            'pending' => Prescription::pending()->count(),
            'routed' => Prescription::routed()->count(),
            'dispensed' => Prescription::dispensed()->count(),
            'cancelled' => Prescription::cancelled()->count(),
        ];

        return view('prescriptions.index', compact('prescriptions', 'status', 'search', 'scope', 'counts'));
    }

    /**
     * Show the form for creating a new prescription.
     */
    public function create(): View
    {
        Gate::authorize('create', Prescription::class);

        $patients = Patient::with('user')->get()->sortBy('name');
        $medicines = Medicine::with('stockBatches')->get()->map(function ($medicine) {
            return [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'generic_name' => $medicine->generic_name,
                'unit' => $medicine->unit,
                'unit_price' => (float) $medicine->unit_price,
                'available_stock' => $medicine->available_stock,
            ];
        });

        return view('prescriptions.create', compact('patients', 'medicines'));
    }

    /**
     * Store a newly created prescription in storage.
     */
    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $prescription = DB::transaction(function () use ($request, $validated) {
            $patientId = $validated['patient_id'] ?? null;

            // Handle inline patient registration if enabled
            if ($request->boolean('register_new_patient')) {
                $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);

                // Create associated user account
                $user = User::create([
                    'name' => $validated['new_patient_name'],
                    'email' => Str::slug($validated['new_id_number']).'@patient.usm.edu.ph',
                    'password' => Hash::make(Str::random(16)),
                    'role_id' => $patientRole->id,
                ]);
                $user->assignRole($patientRole);

                $patient = Patient::create([
                    'user_id' => $user->id,
                    'patient_type' => $validated['new_patient_type'],
                    'id_number' => $validated['new_id_number'],
                    'contact_number' => $validated['new_contact_number'] ?? null,
                ]);

                $patientId = $patient->id;
            }

            $prescription = Prescription::create([
                'patient_id' => $patientId,
                'encoded_by' => $request->user()->id,
                'doctor_name' => $validated['doctor_name'],
                'status' => 'pending',
            ]);

            foreach ($validated['items'] as $item) {
                $prescription->items()->create([
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'dosage_instructions' => $item['dosage_instructions'],
                ]);
            }

            return $prescription;
        });

        return redirect()->route('prescriptions.show', $prescription)
            ->with('status', "Prescription #{$prescription->id} created successfully as Pending.");
    }

    /**
     * Display the specified prescription.
     */
    public function show(Prescription $prescription): View
    {
        Gate::authorize('view', $prescription);

        $prescription->load(['patient.user', 'encodedBy', 'items.medicine.stockBatches']);

        return view('prescriptions.show', compact('prescription'));
    }

    /**
     * Route the prescription to the Pharmacy queue.
     */
    public function routeToPharmacy(Request $request, Prescription $prescription): RedirectResponse
    {
        Gate::authorize('route', $prescription);

        if ($prescription->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending prescriptions can be routed to the pharmacy.']);
        }

        // Re-check medicine stock before routing
        $prescription->load('items.medicine.stockBatches');
        foreach ($prescription->items as $item) {
            $available = $item->medicine->available_stock;
            if ($item->quantity > $available) {
                return back()->withErrors([
                    'error' => "Cannot route to pharmacy: Insufficient stock for {$item->medicine->name}. Required: {$item->quantity}, Available: {$available}.",
                ]);
            }
        }

        $prescription->update(['status' => 'routed']);

        return redirect()->route('prescriptions.show', $prescription)
            ->with('status', "Prescription #{$prescription->id} has been routed to the Pharmacy Queue.");
    }

    /**
     * Cancel a pending prescription.
     */
    public function cancel(Request $request, Prescription $prescription): RedirectResponse
    {
        Gate::authorize('cancel', $prescription);

        if ($prescription->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending prescriptions can be cancelled.']);
        }

        $prescription->update(['status' => 'cancelled']);

        return redirect()->route('prescriptions.show', $prescription)
            ->with('status', "Prescription #{$prescription->id} has been cancelled.");
    }
}
