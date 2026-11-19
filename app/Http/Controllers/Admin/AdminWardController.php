<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminWardController extends Controller
{
    /**
     * Display listing of hospital wards and beds.
     */
    public function index(Request $request): View
    {
        $wards = Ward::with(['beds.currentAdmission.patient.user'])->latest()->get();

        $stats = [
            'total_wards' => Ward::count(),
            'total_beds' => Bed::count(),
            'available_beds' => Bed::where('occupancy_status', 'available')->count(),
            'occupied_beds' => Bed::where('occupancy_status', 'occupied')->count(),
            'maintenance_beds' => Bed::where('occupancy_status', 'maintenance')->count(),
        ];

        return view('admin.wards.index', compact('wards', 'stats'));
    }

    /**
     * Store a newly created ward.
     */
    public function storeWard(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ward_name' => ['required', 'string', 'max:255', 'unique:wards,ward_name'],
            'ward_type' => ['required', 'string', 'max:100'],
            'nurse_station_assigned' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,maintenance'],
        ]);

        $ward = Ward::create($validated);

        AuditLog::record(
            'ward_created',
            'wards',
            "Hospital Ward {$ward->ward_name} ({$ward->ward_type}) provisioned by Administrator.",
            ['ward_id' => $ward->id]
        );

        return back()->with('success', "Ward {$ward->ward_name} created successfully.");
    }

    /**
     * Update an existing ward.
     */
    public function updateWard(Request $request, Ward $ward): RedirectResponse
    {
        $validated = $request->validate([
            'ward_name' => ['required', 'string', 'max:255', 'unique:wards,ward_name,'.$ward->id],
            'ward_type' => ['required', 'string', 'max:100'],
            'nurse_station_assigned' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive,maintenance'],
        ]);

        $ward->update($validated);

        AuditLog::record(
            'ward_updated',
            'wards',
            "Hospital Ward {$ward->ward_name} updated by Administrator.",
            ['ward_id' => $ward->id]
        );

        return back()->with('success', "Ward {$ward->ward_name} updated successfully.");
    }

    /**
     * Add a new bed to a specific ward.
     */
    public function storeBed(Request $request, Ward $ward): RedirectResponse
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:50'],
            'bed_number' => ['required', 'string', 'max:50'],
            'occupancy_status' => ['required', 'in:available,occupied,maintenance'],
        ]);

        $bed = $ward->beds()->create($validated);

        AuditLog::record(
            'bed_created',
            'wards',
            "Bed {$bed->room_number} - {$bed->bed_number} added to Ward {$ward->ward_name}.",
            ['bed_id' => $bed->id, 'ward_id' => $ward->id]
        );

        return back()->with('success', "Bed added to {$ward->ward_name} successfully.");
    }

    /**
     * Toggle a bed's status between Available and Maintenance.
     */
    public function toggleBedStatus(Request $request, Bed $bed): RedirectResponse
    {
        if ($bed->occupancy_status === 'occupied') {
            return back()->with('error', "Cannot toggle maintenance on occupied bed {$bed->room_number} - {$bed->bed_number}. Please discharge or transfer the admitted patient first.");
        }

        $newStatus = $bed->occupancy_status === 'maintenance' ? 'available' : 'maintenance';
        $bed->update(['occupancy_status' => $newStatus]);

        AuditLog::record(
            'bed_status_toggled',
            'wards',
            "Bed {$bed->room_number} - {$bed->bed_number} status set to {$newStatus}.",
            ['bed_id' => $bed->id, 'status' => $newStatus]
        );

        return back()->with('success', "Bed {$bed->room_number} - {$bed->bed_number} status changed to ".ucfirst($newStatus).'.');
    }
}
