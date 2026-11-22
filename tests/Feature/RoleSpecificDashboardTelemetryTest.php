<?php

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\PatientEndorsement;
use App\Models\User;
use Database\Seeders\HospitalWardAndBedSeeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
    $this->seed(HospitalWardAndBedSeeder::class);
});

test('nurse station command hub displays immediate above the fold clinical telemetry', function () {
    $nurseRole = Role::findByName('nurse');
    $nurse = User::factory()->create(['role_id' => $nurseRole->id]);
    $nurse->assignRole($nurseRole);

    $patient = Patient::factory()->create(['patient_type' => 'student']);
    $bed = Bed::where('occupancy_status', 'available')->first();

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'admitted_by' => $nurse->id,
        'patient_classification' => 'Inpatient',
        'chief_complaint' => 'Acute bacterial pharyngitis with fever',
        'blood_pressure' => '120/80',
        'heart_rate' => 78,
        'respiratory_rate' => 18,
        'temperature' => 38.2,
        'oxygen_saturation' => 98.0,
        'respiratory_status' => 'Room Air',
        'status' => 'admitted',
        'admission_date' => now(),
    ]);

    $bed->update(['occupancy_status' => 'occupied']);

    PatientEndorsement::create([
        'admission_id' => $admission->id,
        'endorsed_by' => $nurse->id,
        'nurse_station' => 'Station 1 - Emergency & Triage',
        'endorsement_notes' => 'Patient endorsed for observation and hydration.',
        'acknowledgment_status' => 'pending',
    ]);

    $this->actingAs($nurse)
        ->get(route('nurse.dashboard'))
        ->assertOk()
        ->assertSee('Nurse Station Command Hub')
        ->assertSee('Station 1')
        ->assertSee('Active Floor Shift')
        ->assertSee('Current Inpatients Under Floor Monitoring')
        ->assertSee('Prescription Dispatch')
        ->assertSee('Hospital Ward Beds')
        ->assertSee($patient->name)
        ->assertSee('Room Air')
        ->assertSee('Urgent: Incoming Endorsement Awaiting Floor Reception')
        ->assertDontSee('Laravel');
});

test('admission command center displays immediate live ward capacity and active roster without deep scrolling', function () {
    $admissionRole = Role::findByName('admission');
    $admissionUser = User::factory()->create(['role_id' => $admissionRole->id]);
    $admissionUser->assignRole($admissionRole);

    $patient = Patient::factory()->create(['patient_type' => 'employee']);
    $bed = Bed::where('occupancy_status', 'available')->first();

    Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $bed->id,
        'admitted_by' => $admissionUser->id,
        'patient_classification' => 'Emergency',
        'chief_complaint' => 'Hypertensive urgency',
        'blood_pressure' => '160/100',
        'heart_rate' => 92,
        'respiratory_rate' => 20,
        'temperature' => 36.8,
        'oxygen_saturation' => 97.0,
        'respiratory_status' => 'Room Air',
        'status' => 'admitted',
        'admission_date' => now(),
    ]);

    $bed->update(['occupancy_status' => 'occupied']);

    $this->actingAs($admissionUser)
        ->get(route('admission.index'))
        ->assertOk()
        ->assertSee('Admission Command Center')
        ->assertSee('Clinical Intake Live')
        ->assertSee('Live Ward Capacity')
        ->assertSee('Current Inpatients Roster')
        ->assertSee('Patient Registry')
        ->assertSee('Available Beds')
        ->assertSee('Bed Occupancy');
});

test('pharmacist dashboard surfaces dispensary queue and 1-click pos terminal launcher', function () {
    $pharmRole = Role::findByName('pharmacist');
    $pharmacist = User::factory()->create(['role_id' => $pharmRole->id]);
    $pharmacist->assignRole($pharmRole);

    $this->actingAs($pharmacist)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Hospital Dispensary Operations')
        ->assertSee('Prescription Fulfillment')
        ->assertSee('Point of Sale Terminal')
        ->assertSee('Open POS Terminal');
});

test('stock manager dashboard displays decision support action banner and real-time sync pill', function () {
    $stockRole = Role::findByName('stock_manager');
    $stockManager = User::factory()->create(['role_id' => $stockRole->id]);
    $stockManager->assignRole($stockRole);

    $this->actingAs($stockManager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('DECISION SUPPORT')
        ->assertSee('Sync: Real-time (Stock Movement)')
        ->assertSee('Priority Actions: Restock')
        ->assertSee('+ Restock Order');
});

test('admin dashboard displays executive clinical bed telemetry alongside governance metrics', function () {
    $adminRole = Role::findByName('admin');
    $admin = User::factory()->create(['role_id' => $adminRole->id]);
    $admin->assignRole($adminRole);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('USM Clinic Admin Center')
        ->assertSee('Hospital Clinical Ward')
        ->assertSee('Beds Available')
        ->assertSee('Occupancy Rate');
});
