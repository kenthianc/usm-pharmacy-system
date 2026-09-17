<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    // Setup helper roles
    $this->nurseRole = Role::findByName('nurse');
    $this->pharmacistRole = Role::findByName('pharmacist');
    $this->adminRole = Role::findByName('admin');

    // Create a nurse user
    $this->nurse = User::factory()->create(['role_id' => $this->nurseRole->id]);
    $this->nurse->assignRole($this->nurseRole);

    // Create a patient
    $patientUser = User::factory()->create();
    $this->patient = Patient::create([
        'user_id' => $patientUser->id,
        'patient_type' => 'student',
        'id_number' => 'USM-2026-TEST1',
        'contact_number' => '09123456789',
    ]);

    // Create a test medicine with a batch
    $this->medicine = Medicine::create([
        'name' => 'Amoxicillin 500mg Test',
        'generic_name' => 'Amoxicillin',
        'category' => 'Antibiotic',
        'unit' => 'capsule',
        'unit_price' => 12.00,
        'reorder_level' => 20,
    ]);

    $this->batch = StockBatch::create([
        'medicine_id' => $this->medicine->id,
        'batch_no' => 'TEST-BATCH-001',
        'quantity_received' => 100,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6)->toDateString(),
        'received_date' => now()->subMonth()->toDateString(),
        'supplier' => 'Test Supplier',
    ]);
});

test('nurse can view prescription creation page with patient and medicine options', function () {
    $this->actingAs($this->nurse)
        ->get('/prescriptions/create')
        ->assertOk()
        ->assertSee($this->patient->id_number)
        ->assertSee($this->medicine->name);
});

test('nurse can create prescription with valid items and stock and saves as pending', function () {
    $payload = [
        'patient_id' => $this->patient->id,
        'doctor_name' => 'Maria Santos, MD',
        'items' => [
            [
                'medicine_id' => $this->medicine->id,
                'quantity' => 10,
                'dosage_instructions' => '1 cap 3x daily for 7 days',
            ],
        ],
    ];

    $response = $this->actingAs($this->nurse)
        ->post('/prescriptions', $payload);

    $prescription = Prescription::first();
    expect($prescription)->not->toBeNull()
        ->and($prescription->status)->toBe('pending')
        ->and($prescription->doctor_name)->toBe('Maria Santos, MD')
        ->and($prescription->patient_id)->toBe($this->patient->id)
        ->and($prescription->encoded_by)->toBe($this->nurse->id);

    expect($prescription->items)->toHaveCount(1)
        ->and($prescription->items->first()->quantity)->toBe(10)
        ->and($prescription->items->first()->medicine_id)->toBe($this->medicine->id);

    $response->assertRedirect(route('prescriptions.show', $prescription));

    // Stock must NOT be decremented at prescription creation
    expect($this->batch->fresh()->quantity_remaining)->toBe(50);
});

test('availability check blocks submission when requested quantity exceeds available stock', function () {
    $payload = [
        'patient_id' => $this->patient->id,
        'doctor_name' => 'Maria Santos, MD',
        'items' => [
            [
                'medicine_id' => $this->medicine->id,
                'quantity' => 75, // Only 50 available in batch
                'dosage_instructions' => '1 cap 3x daily',
            ],
        ],
    ];

    $response = $this->actingAs($this->nurse)
        ->from('/prescriptions/create')
        ->post('/prescriptions', $payload);

    $response->assertRedirect('/prescriptions/create');
    $response->assertSessionHasErrors('items.0.quantity');

    expect(Prescription::count())->toBe(0);
});

test('prescription validation fails when patient or items are missing', function () {
    // Missing patient
    $this->actingAs($this->nurse)
        ->post('/prescriptions', [
            'doctor_name' => 'Dr. Smith',
            'items' => [
                ['medicine_id' => $this->medicine->id, 'quantity' => 5, 'dosage_instructions' => 'Take 1'],
            ],
        ])
        ->assertSessionHasErrors('patient_id');

    // Empty items
    $this->actingAs($this->nurse)
        ->post('/prescriptions', [
            'patient_id' => $this->patient->id,
            'doctor_name' => 'Dr. Smith',
            'items' => [],
        ])
        ->assertSessionHasErrors('items');

    // Quantity <= 0
    $this->actingAs($this->nurse)
        ->post('/prescriptions', [
            'patient_id' => $this->patient->id,
            'doctor_name' => 'Dr. Smith',
            'items' => [
                ['medicine_id' => $this->medicine->id, 'quantity' => 0, 'dosage_instructions' => 'Take 1'],
            ],
        ])
        ->assertSessionHasErrors('items.0.quantity');
});

test('nurse can route a pending prescription to the pharmacy queue', function () {
    $prescription = Prescription::create([
        'patient_id' => $this->patient->id,
        'encoded_by' => $this->nurse->id,
        'doctor_name' => 'Dr. Gomez',
        'status' => 'pending',
    ]);

    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 5,
        'dosage_instructions' => '1 tab daily',
    ]);

    $response = $this->actingAs($this->nurse)
        ->post("/prescriptions/{$prescription->id}/route");

    $response->assertRedirect(route('prescriptions.show', $prescription));
    expect($prescription->fresh()->status)->toBe('routed');
});

test('routing fails if stock became unavailable before routing', function () {
    $prescription = Prescription::create([
        'patient_id' => $this->patient->id,
        'encoded_by' => $this->nurse->id,
        'doctor_name' => 'Dr. Gomez',
        'status' => 'pending',
    ]);

    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 10,
        'dosage_instructions' => '1 tab daily',
    ]);

    // Simulate stock depleted elsewhere
    $this->batch->update(['quantity_remaining' => 2]);

    $response = $this->actingAs($this->nurse)
        ->post("/prescriptions/{$prescription->id}/route");

    $response->assertSessionHasErrors('error');
    expect($prescription->fresh()->status)->toBe('pending');
});

test('nurse can cancel a pending prescription', function () {
    $prescription = Prescription::create([
        'patient_id' => $this->patient->id,
        'encoded_by' => $this->nurse->id,
        'doctor_name' => 'Dr. Gomez',
        'status' => 'pending',
    ]);

    $this->actingAs($this->nurse)
        ->delete("/prescriptions/{$prescription->id}")
        ->assertRedirect(route('prescriptions.show', $prescription));

    expect($prescription->fresh()->status)->toBe('cancelled');
});

test('pharmacist cannot encode or route prescriptions directly', function () {
    $pharmacist = User::factory()->create(['role_id' => $this->pharmacistRole->id]);
    $pharmacist->assignRole($this->pharmacistRole);

    $this->actingAs($pharmacist)
        ->get('/prescriptions/create')
        ->assertForbidden();

    $this->actingAs($pharmacist)
        ->post('/prescriptions', [
            'patient_id' => $this->patient->id,
            'doctor_name' => 'Dr. Unauthorized',
            'items' => [
                ['medicine_id' => $this->medicine->id, 'quantity' => 5, 'dosage_instructions' => 'Take 1'],
            ],
        ])
        ->assertForbidden();
});

test('nurse can register a new patient inline during prescription creation', function () {
    $payload = [
        'register_new_patient' => '1',
        'new_patient_name' => 'Carlos Mendoza',
        'new_patient_type' => 'student',
        'new_id_number' => 'USM-2026-INLINE',
        'new_contact_number' => '09181122334',
        'doctor_name' => 'Dr. Gomez',
        'items' => [
            [
                'medicine_id' => $this->medicine->id,
                'quantity' => 5,
                'dosage_instructions' => '1 tab daily',
            ],
        ],
    ];

    $response = $this->actingAs($this->nurse)
        ->post('/prescriptions', $payload);

    $createdPatient = Patient::where('id_number', 'USM-2026-INLINE')->first();
    expect($createdPatient)->not->toBeNull()
        ->and($createdPatient->patient_type)->toBe('student')
        ->and($createdPatient->user)->not->toBeNull()
        ->and($createdPatient->user->name)->toBe('Carlos Mendoza')
        ->and($createdPatient->user->hasRole('patient'))->toBeTrue();

    $prescription = Prescription::where('patient_id', $createdPatient->id)->first();
    expect($prescription)->not->toBeNull()
        ->and($prescription->status)->toBe('pending');
});
