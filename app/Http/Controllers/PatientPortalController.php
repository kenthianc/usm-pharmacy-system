<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientPortalController extends Controller
{
    /**
     * Show the patient's personal dashboard with a summary of their activity.
     */
    public function dashboard(Request $request): View
    {
        $patient = $request->user()->patient()->with([
            'prescriptions.items.medicine',
            'prescriptions.encodedBy',
        ])->firstOrFail();

        $recentPrescriptions = $patient->prescriptions()
            ->with(['items.medicine', 'encodedBy'])
            ->latest()
            ->take(5)
            ->get();

        $counts = [
            'total' => $patient->prescriptions()->count(),
            'pending' => $patient->prescriptions()->where('status', 'pending')->count(),
            'dispensed' => $patient->prescriptions()->where('status', 'dispensed')->count(),
        ];

        return view('patient.dashboard', compact('patient', 'recentPrescriptions', 'counts'));
    }

    /**
     * Show the full prescription history for the logged-in patient.
     */
    public function prescriptions(Request $request): View
    {
        $patient = $request->user()->patient()->firstOrFail();

        $status = $request->query('status');

        $query = $patient->prescriptions()
            ->with(['items.medicine', 'encodedBy'])
            ->latest();

        if ($status && in_array($status, ['pending', 'routed', 'dispensed', 'cancelled'], true)) {
            $query->where('status', $status);
        }

        $prescriptions = $query->paginate(10)->withQueryString();

        $counts = [
            'all' => $patient->prescriptions()->count(),
            'pending' => $patient->prescriptions()->where('status', 'pending')->count(),
            'routed' => $patient->prescriptions()->where('status', 'routed')->count(),
            'dispensed' => $patient->prescriptions()->where('status', 'dispensed')->count(),
            'cancelled' => $patient->prescriptions()->where('status', 'cancelled')->count(),
        ];

        return view('patient.prescriptions', compact('patient', 'prescriptions', 'status', 'counts'));
    }

    /**
     * Show a single prescription belonging to the patient.
     */
    public function showPrescription(Request $request, int $prescriptionId): View
    {
        $patient = $request->user()->patient()->firstOrFail();

        $prescription = $patient->prescriptions()
            ->with(['items.medicine', 'encodedBy'])
            ->findOrFail($prescriptionId);

        return view('patient.prescription-show', compact('patient', 'prescription'));
    }

    /**
     * Show and update the patient's medical profile.
     */
    public function profile(Request $request): View
    {
        $patient = $request->user()->patient()->firstOrFail();

        return view('patient.profile', compact('patient'));
    }

    /**
     * Update the patient's medical profile (allergies, contact, medical notes).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $patient = $request->user()->patient()->firstOrFail();

        $validated = $request->validate([
            'contact_number' => ['nullable', 'string', 'max:20'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medical_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $patient->update($validated);

        return back()->with('status', 'Profile updated successfully.');
    }
}
