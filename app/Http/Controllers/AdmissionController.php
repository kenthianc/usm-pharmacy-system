<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientEndorsement;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdmissionController extends Controller
{
    /**
     * Display Admission Command Center & Operational Telemetry.
     */
    public function index(Request $request): View
    {
        $totalBeds = Bed::count();
        $availableBeds = Bed::where('occupancy_status', 'available')->count();
        $occupiedBeds = Bed::where('occupancy_status', 'occupied')->count();
        $maintenanceBeds = Bed::where('occupancy_status', 'maintenance')->count();

        $activeAdmissions = Admission::with(['patient.user', 'bed.ward', 'admittedBy', 'latestEndorsement'])
            ->where('status', 'admitted')
            ->latest('admission_date')
            ->get();

        $pendingEndorsementsCount = PatientEndorsement::where('acknowledgment_status', 'pending')->count();

        $todayAdmissionsCount = Admission::whereDate('admission_date', Carbon::today())->count();

        $wards = Ward::with(['beds'])->where('status', 'active')->get();

        return view('admission.index', compact(
            'totalBeds',
            'availableBeds',
            'occupiedBeds',
            'maintenanceBeds',
            'activeAdmissions',
            'pendingEndorsementsCount',
            'todayAdmissionsCount',
            'wards'
        ));
    }

    /**
     * Display the Real-Time Ward & Bed Availability Visual Board.
     */
    public function beds(Request $request): View
    {
        $statusFilter = $request->query('status');
        $wardFilter = $request->query('ward_id');
        $typeFilter = $request->query('ward_type');

        $wardsQuery = Ward::with(['beds' => function ($q) use ($statusFilter) {
            if ($statusFilter && in_array($statusFilter, ['available', 'occupied', 'maintenance'])) {
                $q->where('occupancy_status', $statusFilter);
            }
            $q->with(['currentAdmission.patient.user']);
        }])->where('status', 'active');

        if ($wardFilter) {
            $wardsQuery->where('id', $wardFilter);
        }

        if ($typeFilter) {
            $wardsQuery->where('ward_type', $typeFilter);
        }

        $wards = $wardsQuery->get();
        $allWards = Ward::where('status', 'active')->get();
        $wardTypes = Ward::where('status', 'active')
            ->distinct()
            ->pluck('ward_type')
            ->filter()
            ->values();

        $summary = [
            'total' => Bed::count(),
            'available' => Bed::where('occupancy_status', 'available')->count(),
            'occupied' => Bed::where('occupancy_status', 'occupied')->count(),
            'maintenance' => Bed::where('occupancy_status', 'maintenance')->count(),
        ];

        return view('admission.beds', compact('wards', 'allWards', 'wardTypes', 'summary', 'statusFilter', 'wardFilter', 'typeFilter'));
    }

    /**
     * Display Patient Registry & Intake Directory.
     */
    public function patients(Request $request): View
    {
        $search = $request->query('search');

        $query = Patient::with(['user', 'currentAdmission.bed.ward'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id_number', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $patients = $query->paginate(15)->withQueryString();
        $availableBeds = Bed::with('ward')->where('occupancy_status', 'available')->get();

        return view('admission.patients', compact('patients', 'availableBeds', 'search'));
    }

    /**
     * Store a newly registered patient.
     */
    public function storePatient(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'patient_type' => ['required', 'string', 'in:student,resident'],
            'id_number' => ['required', 'string', 'max:50', 'unique:patients,id_number'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date'],
            'sex' => ['nullable', 'string', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $patient = Patient::create([
            'patient_type' => $validated['patient_type'],
            'id_number' => $validated['id_number'],
            'contact_number' => $validated['contact_number'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'sex' => $validated['sex'] ?? null,
            'address' => $validated['address'] ?? null,
            'allergies' => $validated['allergies'] ?? 'None recorded',
            'medical_notes' => $validated['medical_notes'] ?? null,
        ]);

        AuditLog::record(
            'patient_registered',
            'admission',
            "Patient {$validated['name']} ({$validated['id_number']}) registered via Admission Portal.",
            ['patient_id' => $patient->id, 'id_number' => $patient->id_number]
        );

        return redirect()->route('admission.patients')
            ->with('success', "Patient {$validated['name']} registered successfully. Ready for clinical admission.");
    }

    /**
     * Show form to admit a specific patient to a bed.
     */
    public function createAdmission(Patient $patient): View
    {
        if ($patient->currentAdmission) {
            return redirect()->route('admission.index')
                ->with('error', "Patient {$patient->id_number} is already actively admitted in {$patient->currentAdmission->bed?->full_location}.");
        }

        $availableBeds = Bed::with('ward')->where('occupancy_status', 'available')->get();
        $wards = Ward::where('status', 'active')->get();

        return view('admission.create', compact('patient', 'availableBeds', 'wards'));
    }

    /**
     * Store new Admission, allocate Bed, and dispatch digital Patient Endorsement.
     */
    public function storeAdmission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => ['required', 'exists:patients,id'],
            'bed_id' => ['required', 'exists:beds,id'],
            'patient_classification' => ['required', 'string', 'max:50'],
            'respiratory_status' => ['required', 'string', 'max:50'],
            'chief_complaint' => ['required', 'string'],
            'bp' => ['required', 'string', 'max:20'],
            'temp' => ['required', 'string', 'max:10'],
            'pulse' => ['required', 'string', 'max:10'],
            'rr' => ['required', 'string', 'max:10'],
            'spo2' => ['required', 'string', 'max:10'],
            'weight' => ['nullable', 'string', 'max:10'],
            'endorsement_notes' => ['required', 'string'],
        ]);

        $bed = Bed::with('ward')->findOrFail($validated['bed_id']);

        if ($bed->occupancy_status !== 'available') {
            return back()->withInput()->with('error', "Selected {$bed->full_location} is no longer available. Please select another bed.");
        }

        $patient = Patient::findOrFail($validated['patient_id']);

        if ($patient->currentAdmission) {
            return redirect()->route('admission.index')
                ->with('error', "Patient is already actively admitted in {$patient->currentAdmission->bed?->full_location}.");
        }

        $vitals = [
            'bp' => $validated['bp'] ?? '120/80',
            'temp' => $validated['temp'] ?? '36.8',
            'pulse' => $validated['pulse'] ?? '75',
            'rr' => $validated['rr'] ?? '18',
            'spo2' => $validated['spo2'] ?? '98',
            'weight' => $validated['weight'] ?? null,
        ];

        DB::transaction(function () use ($validated, $bed, $patient, $vitals, $request) {
            $admission = Admission::create([
                'patient_id' => $patient->id,
                'bed_id' => $bed->id,
                'admitted_by' => $request->user()->id,
                'patient_classification' => $validated['patient_classification'],
                'respiratory_status' => $validated['respiratory_status'],
                'chief_complaint' => $validated['chief_complaint'],
                'baseline_vitals' => $vitals,
                'admission_date' => Carbon::now(),
                'status' => 'admitted',
            ]);

            // Update Bed status to occupied
            $bed->update(['occupancy_status' => 'occupied']);

            // Create patient endorsement routed to assigned nurse station
            PatientEndorsement::create([
                'admission_id' => $admission->id,
                'endorsed_by' => $request->user()->id,
                'nurse_station' => $bed->ward->nurse_station_assigned,
                'endorsement_notes' => $validated['endorsement_notes'],
                'acknowledgment_status' => 'pending',
            ]);

            AuditLog::record(
                'patient_admitted',
                'admission',
                "Patient {$patient->id_number} admitted to {$bed->full_location}. Endorsement dispatched to {$bed->ward->nurse_station_assigned}.",
                ['admission_id' => $admission->id, 'bed_id' => $bed->id, 'patient_id' => $patient->id]
            );
        });

        return redirect()->route('admission.index')
            ->with('success', "Patient successfully admitted to {$bed->full_location} and digitally endorsed to {$bed->ward->nurse_station_assigned}.");
    }

    /**
     * Discharge an admitted patient and release bed.
     */
    public function discharge(Request $request, Admission $admission): RedirectResponse
    {
        DB::transaction(function () use ($admission) {
            $admission->update([
                'status' => 'discharged',
                'discharge_date' => Carbon::now(),
            ]);

            if ($admission->bed) {
                $admission->bed->update(['occupancy_status' => 'available']);
            }

            AuditLog::record(
                'patient_discharged',
                'admission',
                "Admission #{$admission->id} for Patient {$admission->patient->id_number} discharged. Bed released.",
                ['admission_id' => $admission->id, 'bed_id' => $admission->bed_id]
            );
        });

        return redirect()->route('admission.index')
            ->with('success', "Patient {$admission->patient->id_number} successfully discharged. Bed is now available.");
    }
}
