<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\PatientEndorsement;
use App\Models\Prescription;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NurseStationController extends Controller
{
    /**
     * Display Nurse Station Clinical Hub.
     */
    public function dashboard(Request $request): View
    {
        $pendingEndorsements = PatientEndorsement::with(['admission.patient.user', 'admission.bed.ward', 'endorsedBy'])
            ->where('acknowledgment_status', 'pending')
            ->latest()
            ->get();

        $activeInpatients = Admission::with(['patient.user', 'bed.ward', 'prescriptions'])
            ->where('status', 'admitted')
            ->latest('admission_date')
            ->get();

        $recentPrescriptions = Prescription::with(['patient.user', 'items.medicine'])
            ->where('encoded_by', $request->user()->id)
            ->latest()
            ->take(5)
            ->get();

        $wards = Ward::with(['beds'])->where('status', 'active')->get();

        return view('nurse.dashboard', compact(
            'pendingEndorsements',
            'activeInpatients',
            'recentPrescriptions',
            'wards'
        ));
    }

    /**
     * Display Endorsement Queue.
     */
    public function endorsements(Request $request): View
    {
        $statusFilter = $request->query('status', 'pending');

        $query = PatientEndorsement::with(['admission.patient.user', 'admission.bed.ward', 'endorsedBy', 'receivingNurse']);

        if ($statusFilter === 'pending') {
            $query->where('acknowledgment_status', 'pending');
        } elseif ($statusFilter === 'acknowledged') {
            $query->where('acknowledgment_status', 'acknowledged');
        }

        $endorsements = $query->latest()->paginate(15)->withQueryString();
        $pendingCount = PatientEndorsement::where('acknowledgment_status', 'pending')->count();

        return view('nurse.endorsements', compact('endorsements', 'pendingCount', 'statusFilter'));
    }

    /**
     * One-Click Acknowledge Patient Endorsement.
     */
    public function acknowledgeEndorsement(Request $request, PatientEndorsement $endorsement): RedirectResponse
    {
        if ($endorsement->acknowledgment_status === 'acknowledged') {
            return back()->with('info', 'This endorsement has already been acknowledged.');
        }

        $endorsement->update([
            'receiving_nurse_id' => $request->user()->id,
            'acknowledgment_status' => 'acknowledged',
            'acknowledged_at' => Carbon::now(),
        ]);

        AuditLog::record(
            'endorsement_acknowledged',
            'nurse',
            "Nurse {$request->user()->name} acknowledged endorsement #{$endorsement->id} for Patient {$endorsement->admission->patient->id_number}.",
            ['endorsement_id' => $endorsement->id, 'admission_id' => $endorsement->admission_id]
        );

        return back()->with('success', "Patient {$endorsement->admission->patient->id_number} endorsement successfully acknowledged by Nurse {$request->user()->name}. Clinical charting active.");
    }

    /**
     * Display Admitted Patients & Real-Time Bed Monitoring.
     */
    public function inpatients(Request $request): View
    {
        $wardFilter = $request->query('ward_id');

        $query = Admission::with(['patient.user', 'bed.ward', 'prescriptions.items.medicine', 'latestEndorsement'])
            ->where('status', 'admitted');

        if ($wardFilter) {
            $query->whereHas('bed', fn ($bq) => $bq->where('ward_id', $wardFilter));
        }

        $inpatients = $query->latest('admission_date')->paginate(12)->withQueryString();
        $wards = Ward::where('status', 'active')->get();

        return view('nurse.inpatients', compact('inpatients', 'wards', 'wardFilter'));
    }

    /**
     * Display Patient Clinical Chart & Medication Schedule.
     */
    public function showChart(Admission $admission): View
    {
        $admission->load([
            'patient.user',
            'bed.ward',
            'admittedBy',
            'endorsements.endorsedBy',
            'endorsements.receivingNurse',
            'prescriptions.items.medicine',
            'prescriptions.encodedBy',
            'prescriptions.posTransaction.items.medicine',
            'prescriptions.posTransaction.items.batch',
        ]);

        $availableBeds = Bed::with('ward')
            ->where('occupancy_status', 'available')
            ->get();

        return view('nurse.chart', compact('admission', 'availableBeds'));
    }

    /**
     * Generate printable Patient Medication Summary for billing reference upon discharge.
     */
    public function printMedicationSummary(Admission $admission): View
    {
        $admission->load([
            'patient.user',
            'bed.ward',
            'admittedBy',
            'prescriptions' => function ($q) {
                $q->where('status', 'dispensed')
                    ->with([
                        'items.medicine',
                        'posTransaction.items.medicine',
                        'posTransaction.items.batch',
                        'encodedBy',
                    ]);
            },
        ]);

        return view('nurse.medication-summary', compact('admission'));
    }

    /**
     * Update Clinical Vitals / Progress Notes.
     */
    public function updateVitals(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'bp' => ['nullable', 'string', 'max:20'],
            'temp' => ['nullable', 'string', 'max:10'],
            'pulse' => ['nullable', 'string', 'max:10'],
            'rr' => ['nullable', 'string', 'max:10'],
            'spo2' => ['nullable', 'string', 'max:10'],
            'respiratory_status' => ['nullable', 'string', 'max:50'],
            'clinical_note' => ['nullable', 'string'],
        ]);

        $currentVitals = $admission->baseline_vitals ?? [];
        if (! empty($validated['bp'])) {
            $currentVitals['bp'] = $validated['bp'];
        }
        if (! empty($validated['temp'])) {
            $currentVitals['temp'] = $validated['temp'];
        }
        if (! empty($validated['pulse'])) {
            $currentVitals['pulse'] = $validated['pulse'];
        }
        if (! empty($validated['rr'])) {
            $currentVitals['rr'] = $validated['rr'];
        }
        if (! empty($validated['spo2'])) {
            $currentVitals['spo2'] = $validated['spo2'];
        }

        $updateData = ['baseline_vitals' => $currentVitals];

        if (! empty($validated['respiratory_status'])) {
            $updateData['respiratory_status'] = $validated['respiratory_status'];
        }

        if (! empty($validated['clinical_note'])) {
            $timestamp = Carbon::now()->format('Y-m-d H:i');
            $nurseName = $request->user()->name;
            $existingNotes = $admission->patient->medical_notes ? $admission->patient->medical_notes."\n" : '';
            $admission->patient->update([
                'medical_notes' => $existingNotes."[{$timestamp} by Nurse {$nurseName}]: ".$validated['clinical_note'],
            ]);
        }

        $admission->update($updateData);

        return back()->with('success', 'Clinical vitals and charting notes updated successfully.');
    }

    /**
     * Process a patient bed transfer (subject to respiratory isolation rules).
     */
    public function transferBed(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'new_bed_id' => ['required', 'exists:beds,id'],
            'transfer_reason' => ['required', 'string', 'max:255'],
        ]);

        $newBed = Bed::with('ward')->findOrFail($validated['new_bed_id']);

        if ($newBed->occupancy_status !== 'available') {
            return back()->with('error', "Selected {$newBed->full_location} is no longer available.");
        }

        $oldBed = $admission->bed;

        // Respiratory isolation validation warning
        $isRespiratory = str_contains(strtolower($admission->respiratory_status ?? ''), 'respiratory')
            && ! str_contains(strtolower($admission->respiratory_status ?? ''), 'non-respiratory');

        if ($isRespiratory && strtolower($newBed->ward->ward_type) !== 'isolation' && strtolower($newBed->ward->ward_type) !== 'icu') {
            // Note: still allow clinical override if authorized, but log caution
            AuditLog::record(
                'respiratory_isolation_override',
                'nurse',
                "Caution: Respiratory patient transferred to non-isolation ward ({$newBed->ward->ward_name}) by Nurse {$request->user()->name}.",
                ['admission_id' => $admission->id, 'bed_id' => $newBed->id]
            );
        }

        DB::transaction(function () use ($admission, $oldBed, $newBed, $validated, $request) {
            if ($oldBed) {
                $oldBed->update(['occupancy_status' => 'available']);
            }

            $newBed->update(['occupancy_status' => 'occupied']);
            $admission->update(['bed_id' => $newBed->id]);

            // Append transfer note to patient clinical charting
            $timestamp = Carbon::now()->format('Y-m-d H:i');
            $existingNotes = $admission->patient->medical_notes ? $admission->patient->medical_notes."\n" : '';
            $fromLoc = $oldBed ? $oldBed->full_location : 'Unassigned';
            $admission->patient->update([
                'medical_notes' => $existingNotes."[{$timestamp} Bed Transfer]: Transferred from {$fromLoc} to {$newBed->full_location}. Reason: {$validated['transfer_reason']} (Nurse {$request->user()->name})",
            ]);

            AuditLog::record(
                'patient_bed_transferred',
                'nurse',
                "Patient {$admission->patient->id_number} transferred from {$fromLoc} to {$newBed->full_location}.",
                [
                    'admission_id' => $admission->id,
                    'old_bed_id' => $oldBed?->id,
                    'new_bed_id' => $newBed->id,
                    'reason' => $validated['transfer_reason'],
                ]
            );
        });

        return back()->with('success', "Patient successfully transferred to {$newBed->full_location}. Bed availability updated across hospital in real-time.");
    }

    /**
     * Process clinical discharge of an admitted patient and release bed.
     */
    public function discharge(Request $request, Admission $admission): RedirectResponse
    {
        $validated = $request->validate([
            'discharge_notes' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($admission, $validated, $request) {
            $admission->update([
                'status' => 'discharged',
                'discharge_date' => Carbon::now(),
            ]);

            $bed = $admission->bed;
            if ($bed) {
                $bed->update(['occupancy_status' => 'available']);
            }

            // Append discharge summary to medical notes
            $timestamp = Carbon::now()->format('Y-m-d H:i');
            $existingNotes = $admission->patient->medical_notes ? $admission->patient->medical_notes."\n" : '';
            $notesText = $validated['discharge_notes'] ?? 'Routine clinical discharge approved.';
            $admission->patient->update([
                'medical_notes' => $existingNotes."[{$timestamp} Discharge]: Patient formally discharged by Nurse {$request->user()->name}. Notes: {$notesText}",
            ]);

            AuditLog::record(
                'patient_discharged',
                'nurse',
                "Patient {$admission->patient->id_number} discharged from {$bed?->full_location} by Nurse {$request->user()->name}.",
                [
                    'admission_id' => $admission->id,
                    'bed_id' => $admission->bed_id,
                ]
            );
        });

        return redirect()->route('nurse.inpatients')
            ->with('success', "Patient {$admission->patient->id_number} discharged successfully. Vacated bed has reverted to Available.");
    }
}
