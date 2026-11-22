<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClinicSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingController extends Controller
{
    /**
     * Display clinic operational settings.
     */
    public function index(): View
    {
        $settings = [
            'clinic_name' => ClinicSetting::get('clinic_name', 'University of Southern Mindanao - Health Services & Pharmacy'),
            'low_stock_threshold' => ClinicSetting::get('low_stock_threshold', '15'),
            'expiry_warning_days' => ClinicSetting::get('expiry_warning_days', '30'),
            'contact_number' => ClinicSetting::get('contact_number', '(064) 248-2138 / 0917-000-0000'),
            'campus_location' => ClinicSetting::get('campus_location', 'USM Main Campus, Kabacan, Cotabato'),
            'receipt_footer_note' => ClinicSetting::get('receipt_footer_note', 'Thank you for using USM Health Services. Follow dosage instructions carefully.'),
        ];

        return view('admin.settings', compact('settings'));
    }

    /**
     * Update clinic operational settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'clinic_name' => ['required', 'string', 'max:255'],
            'low_stock_threshold' => ['required', 'integer', 'min:1', 'max:1000'],
            'expiry_warning_days' => ['required', 'integer', 'min:7', 'max:365'],
            'contact_number' => ['nullable', 'string', 'max:100'],
            'campus_location' => ['nullable', 'string', 'max:255'],
            'receipt_footer_note' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            ClinicSetting::set($key, (string) $value);
        }

        AuditLog::record(
            'settings_updated',
            'settings',
            'Updated clinic operational settings and thresholds',
            $validated
        );

        return redirect()->route('admin.settings.index')
            ->with('status', 'Clinic operational settings have been updated successfully.');
    }
}
