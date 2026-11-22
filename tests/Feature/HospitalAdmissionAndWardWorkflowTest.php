<?php

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientEndorsement;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\Ward;
use App\Services\DispensingService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Create admission staff
    $this->admissionStaff = User::factory()->create();
    $admissionRole = Role::findByName('admission');
    $this->admissionStaff->update(['role_id' => $admissionRole->id]);
    $this->admissionStaff->assignRole($admissionRole);

    // Create nurse
    $this->nurse = User::factory()->create();
    $nurseRole = Role::findByName('nurse');
    $this->nurse->update(['role_id' => $nurseRole->id]);
    $this->nurse->assignRole($nurseRole);

    // Create admin
    $this->admin = User::factory()->create();
    $adminRole = Role::findByName('admin');
    $this->admin->update(['role_id' => $adminRole->id]);
    $this->admin->assignRole($adminRole);

    // Create a sample ward and bed
    $this->ward = Ward::create([
        'ward_name' => 'Male Medical Ward',
        'ward_type' => 'inpatient',
        'nurse_station_assigned' => 'Station 1 - Central Ward',
        'status' => 'active',
    ]);

    $this->bed = Bed::create([
        'ward_id' => $this->ward->id,
        'room_number' => 'Room 101',
        'bed_number' => 'Bed A',
        'occupancy_status' => 'available',
    ]);
});

test('admission staff can view admission dashboard and bed availability board', function () {
    actingAs($this->admissionStaff);

    get(route('admission.index'))
        ->assertOk()
        ->assertSee('Admission Command Center')
        ->assertSee('Male Medical Ward');

    get(route('admission.beds'))
        ->assertOk()
        ->assertSee('Bed Occupancy Telemetry')
        ->assertSee('Room 101')
        ->assertSee('Bed A');
});

test('admission staff can register a new patient', function () {
    actingAs($this->admissionStaff);

    $response = post(route('admission.patients.store'), [
        'name' => 'Maria Santos',
        'patient_type' => 'student',
        'id_number' => 'USM-2026-9999',
        'contact_number' => '09171234567',
        'sex' => 'female',
        'address' => 'Kabacan, Cotabato',
        'allergies' => 'Penicillin',
        'medical_notes' => 'History of allergic rhinitis',
    ]);

    $response->assertRedirect(route('admission.patients'));

    $newPatient = Patient::where('id_number', 'USM-2026-9999')->first();
    expect($newPatient)->not->toBeNull()
        ->and($newPatient->name)->toBe('Maria Santos')
        ->and($newPatient->patient_type)->toBe('student');

    // Register employee with emergency contact
    $empResponse = post(route('admission.patients.store'), [
        'name' => 'Prof. Juan Santos',
        'patient_type' => 'employee',
        'id_number' => 'EMP-2026-101',
        'contact_number' => '09181112233',
        'emergency_contact' => 'Maria Santos (Spouse) - 09171234567',
        'sex' => 'male',
        'address' => 'USM Faculty Housing',
        'allergies' => 'None known',
        'medical_notes' => 'Routine faculty medical checkup',
    ]);

    $empResponse->assertRedirect(route('admission.patients'));
    $empPatient = Patient::where('id_number', 'EMP-2026-101')->first();
    expect($empPatient)->not->toBeNull()
        ->and($empPatient->name)->toBe('Prof. Juan Santos')
        ->and($empPatient->patient_type)->toBe('employee')
        ->and($empPatient->emergency_contact)->toContain('Maria Santos');
});

test('admission staff can allocate an available bed and dispatch digital endorsement to nurse station', function () {
    actingAs($this->admissionStaff);

    $patient = Patient::create([
        'patient_type' => 'student',
        'id_number' => 'USM-2026-1111',
        'allergies' => 'None',
    ]);

    $response = post(route('admission.store'), [
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'patient_classification' => 'Inpatient',
        'respiratory_status' => 'Room Air',
        'chief_complaint' => 'Moderate dehydration secondary to acute gastroenteritis.',
        'bp' => '110/70',
        'temp' => '37.8',
        'pulse' => '82',
        'rr' => '18',
        'spo2' => '98',
        'endorsement_notes' => 'IV fluids initiated. Please monitor vitals every 4 hours.',
    ]);

    $response->assertRedirect(route('admission.index'));

    // Verify bed is occupied
    expect($this->bed->fresh()->occupancy_status)->toBe('occupied');

    // Verify admission was created
    $admission = Admission::where('patient_id', $patient->id)->first();
    expect($admission)->not->toBeNull()
        ->and($admission->status)->toBe('admitted')
        ->and($admission->respiratory_status)->toBe('Room Air');

    // Verify patient endorsement was dispatched to nurse station
    $endorsement = PatientEndorsement::where('admission_id', $admission->id)->first();
    expect($endorsement)->not->toBeNull()
        ->and($endorsement->acknowledgment_status)->toBe('pending')
        ->and($endorsement->nurse_station)->toBe('Station 1 - Central Ward');
});

test('floor nurse can view incoming endorsements and execute 1-click acknowledgment', function () {
    $patient = Patient::create([
        'patient_type' => 'resident',
        'id_number' => 'RES-2026-2222',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'admitted_by' => $this->admissionStaff->id,
        'patient_classification' => 'Emergency',
        'respiratory_status' => 'Nasal Cannula (3 LPM)',
        'chief_complaint' => 'Severe asthma exacerbation',
        'baseline_vitals' => ['bp' => '120/80', 'spo2' => '93'],
        'admission_date' => Carbon::now(),
        'status' => 'admitted',
    ]);

    $endorsement = PatientEndorsement::create([
        'admission_id' => $admission->id,
        'endorsed_by' => $this->admissionStaff->id,
        'nurse_station' => 'Station 1 - Central Ward',
        'endorsement_notes' => 'Immediate nebulization ordered by Dr. Perez.',
        'acknowledgment_status' => 'pending',
    ]);

    actingAs($this->nurse);

    // Nurse views endorsement queue
    get(route('nurse.endorsements'))
        ->assertOk()
        ->assertSee('Incoming Patient Endorsement Queue')
        ->assertSee('RES-2026-2222')
        ->assertSee('Immediate nebulization');

    // Nurse acknowledges endorsement
    post(route('nurse.endorsements.acknowledge', $endorsement))
        ->assertRedirect();

    $freshEndorsement = $endorsement->fresh();
    expect($freshEndorsement->acknowledgment_status)->toBe('acknowledged')
        ->and($freshEndorsement->receiving_nurse_id)->toBe($this->nurse->id)
        ->and($freshEndorsement->acknowledged_at)->not->toBeNull();
});

test('nurse can view inpatient clinical chart and update vitals', function () {
    $patient = Patient::create([
        'patient_type' => 'student',
        'id_number' => 'USM-2026-3333',
        'medical_notes' => 'Baseline medical history',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'admitted_by' => $this->admissionStaff->id,
        'patient_classification' => 'Observation',
        'respiratory_status' => 'Room Air',
        'chief_complaint' => 'Mild food poisoning',
        'baseline_vitals' => ['bp' => '120/80', 'spo2' => '99'],
        'admission_date' => Carbon::now(),
        'status' => 'admitted',
    ]);

    actingAs($this->nurse);

    get(route('nurse.chart', $admission))
        ->assertOk()
        ->assertSee('Clinical Chart')
        ->assertSee('USM-2026-3333');

    // Update vitals
    post(route('nurse.chart.vitals', $admission), [
        'bp' => '118/75',
        'temp' => '36.6',
        'pulse' => '72',
        'spo2' => '99',
        'respiratory_status' => 'Room Air',
        'clinical_note' => 'Patient resting comfortably. Nausea subsided.',
    ])->assertRedirect();

    $freshAdmission = $admission->fresh();
    expect($freshAdmission->baseline_vitals['bp'])->toBe('118/75')
        ->and($freshAdmission->baseline_vitals['temp'])->toBe('36.6');
});

test('nurse can view and print patient medication summary for hospital billing reference', function () {
    actingAs($this->nurse);

    $patient = Patient::create([
        'name' => 'Eduardo Mendoza',
        'patient_type' => 'student',
        'id_number' => 'USM-2026-7777',
        'medical_notes' => 'Clinical monitoring',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'admitted_by' => $this->admissionStaff->id,
        'patient_classification' => 'Observation',
        'respiratory_status' => 'Room Air',
        'chief_complaint' => 'Clinical consultation',
        'baseline_vitals' => ['bp' => '120/80'],
        'admission_date' => Carbon::now(),
        'status' => 'admitted',
    ]);

    // Access medication summary view
    get(route('nurse.chart.summary', $admission))
        ->assertOk()
        ->assertSee('Patient Medication Summary (For Billing Reference)', false)
        ->assertSee('Document Reference', false)
        ->assertSee('Fulfilled Inpatient Dispensary Records', false)
        ->assertSee('Eduardo Mendoza');
});

test('admin can provision new wards and toggle bed maintenance', function () {
    actingAs($this->admin);

    // View wards management
    get(route('admin.wards.index'))
        ->assertOk()
        ->assertSee('Hospital Ward & Bed Management', false)
        ->assertSee('Male Medical Ward');

    // Create new ward
    post(route('admin.wards.store'), [
        'ward_name' => 'Isolation Ward West',
        'ward_type' => 'isolation',
        'nurse_station_assigned' => 'Station 4 - Isolation',
        'status' => 'active',
    ])->assertRedirect();

    $newWard = Ward::where('ward_name', 'Isolation Ward West')->first();
    expect($newWard)->not->toBeNull();

    // Toggle bed maintenance
    post(route('admin.beds.toggle-status', $this->bed))
        ->assertRedirect();

    expect($this->bed->fresh()->occupancy_status)->toBe('maintenance');
});

test('admission routes are guarded against unauthorized roles', function () {
    $patientUser = User::factory()->create();
    $patientRole = Role::findByName('patient');
    $patientUser->update(['role_id' => $patientRole->id]);
    $patientUser->assignRole($patientRole);

    actingAs($patientUser);

    get(route('admission.index'))->assertForbidden();
    get(route('admission.beds'))->assertForbidden();
});

test('admission requires mandatory baseline clinical vitals', function () {
    actingAs($this->admissionStaff);

    $patient = Patient::create([
        'patient_type' => 'student',
        'id_number' => 'USM-2026-VAL',
    ]);

    // Missing bp, temp, pulse, spo2, rr
    $response = post(route('admission.store'), [
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'patient_classification' => 'Inpatient',
        'respiratory_status' => 'Room Air',
        'chief_complaint' => 'Fever and malaise',
        'endorsement_notes' => 'Pending vitals intake.',
    ]);

    $response->assertSessionHasErrors(['bp', 'temp', 'pulse', 'rr', 'spo2']);
});

test('chatbot endpoint answers specific medicine inquiries directly and refuses symptoms with OPD referral', function () {
    $medicine = Medicine::factory()->create([
        'name' => 'Paracetamol 500mg',
        'generic_name' => 'Acetaminophen',
        'unit' => 'tablet',
        'unit_price' => 5.00,
    ]);

    StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 200,
        'quantity_remaining' => 200,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addYear()->toDateString(),
    ]);

    // 1. Direct price inquiry
    $resPrice = post(route('chatbot.message'), ['message' => 'Magkano ang Paracetamol 500mg?']);
    $resPrice->assertOk();
    expect($resPrice->json('reply'))
        ->toContain('Paracetamol 500mg')
        ->toContain('5.00 bawat tablet');

    // 2. Direct stock inquiry
    $resStock = post(route('chatbot.message'), ['message' => 'May stock ba ang Paracetamol?']);
    $resStock->assertOk();
    expect($resStock->json('reply'))
        ->toContain('200 tablets ng Paracetamol 500mg');

    // 3. Clinical symptom inquiry refused with OPD referral
    $resSymptom = post(route('chatbot.message'), ['message' => 'Masakit ang ulo at lagnat ko ano gamot?']);
    $resSymptom->assertOk();
    expect($resSymptom->json('reply'))
        ->toContain('Hindi ako maaaring magbigay ng medical advice')
        ->toContain('USM Hospital & University Infirmary')
        ->toContain('8:00 AM')
        ->toContain('5:00 PM');
});

test('dispensing service blocks dispensing of expired batches', function () {
    $medicine = Medicine::factory()->create(['unit_price' => 50.00]);
    $expiredBatch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 10,
        'quantity_remaining' => 10,
        'status' => 'received',
        'expiry_date' => Carbon::now()->subDays(5)->toDateString(),
    ]);

    $pharmacist = User::factory()->create();
    $pharmacist->assignRole(Role::findByName('pharmacist'));

    $service = app(DispensingService::class);

    $patient = Patient::create([
        'patient_type' => 'student',
        'id_number' => 'USM-2026-EXP',
    ]);

    $prescription = Prescription::create([
        'patient_id' => $patient->id,
        'encoded_by' => $this->nurse->id,
        'doctor_name' => 'Dr. Garcia',
        'status' => 'routed',
        'order_type' => 'outpatient',
    ]);

    $prescription->items()->create([
        'medicine_id' => $medicine->id,
        'quantity' => 2,
        'dosage_instructions' => 'Take 1 tablet every 12 hours.',
    ]);

    expect(function () use ($service, $prescription, $expiredBatch, $pharmacist, $medicine) {
        $service->dispensePrescription(
            $prescription,
            [$medicine->id => [$expiredBatch->id => 2]],
            $pharmacist,
            'cash'
        );
    })->toThrow(Exception::class);
});

test('nurse can transfer patient bed and discharge patient with immediate bed status reversion', function () {
    actingAs($this->nurse);

    $patient = Patient::create([
        'patient_type' => 'student',
        'id_number' => 'USM-2026-XFER',
    ]);

    $admission = Admission::create([
        'patient_id' => $patient->id,
        'bed_id' => $this->bed->id,
        'admitted_by' => $this->admissionStaff->id,
        'patient_classification' => 'Inpatient',
        'respiratory_status' => 'Room Air',
        'chief_complaint' => 'Observation',
        'admission_date' => Carbon::now(),
        'status' => 'admitted',
    ]);
    $this->bed->update(['occupancy_status' => 'occupied']);

    // Create a target bed
    $newBed = Bed::create([
        'ward_id' => $this->ward->id,
        'room_number' => 'Room 202',
        'bed_number' => 'Bed B',
        'occupancy_status' => 'available',
    ]);

    // Process bed transfer
    post(route('nurse.admissions.transfer', $admission), [
        'new_bed_id' => $newBed->id,
        'transfer_reason' => 'Patient requested window bed for ventilation',
    ])->assertRedirect();

    expect($this->bed->fresh()->occupancy_status)->toBe('available')
        ->and($newBed->fresh()->occupancy_status)->toBe('occupied')
        ->and($admission->fresh()->bed_id)->toBe($newBed->id);

    // Process clinical discharge
    post(route('nurse.admissions.discharge', $admission), [
        'discharge_notes' => 'Patient fully recovered and cleared by attending physician.',
    ])->assertRedirect();

    expect($newBed->fresh()->occupancy_status)->toBe('available')
        ->and($admission->fresh()->status)->toBe('discharged');
});

test('admin can delete a staff user account', function () {
    actingAs($this->admin);

    $testUser = User::factory()->create();
    $nurseRole = Role::findByName('nurse');
    $testUser->assignRole($nurseRole);

    $this->delete(route('admin.users.destroy', $testUser))
        ->assertRedirect();

    expect(User::find($testUser->id))->toBeNull();
});
