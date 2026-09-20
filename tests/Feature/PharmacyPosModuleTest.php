<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'pharmacist']);
    Role::firstOrCreate(['name' => 'nurse']);

    $this->pharmacist = User::factory()->create();
    $this->pharmacist->assignRole('pharmacist');

    $this->nurse = User::factory()->create();
    $this->nurse->assignRole('nurse');

    $this->medicine = Medicine::factory()->create(['unit_price' => 10.00]);
    $this->patient = Patient::factory()->create();
});

it('allows pharmacist to view routed prescriptions queue', function () {
    $routedRx = Prescription::factory()->create(['status' => 'routed', 'patient_id' => $this->patient->id]);
    $pendingRx = Prescription::factory()->create(['status' => 'pending', 'patient_id' => $this->patient->id]);

    actingAs($this->pharmacist)
        ->get(route('pos.index'))
        ->assertStatus(200)
        ->assertSee($routedRx->prescription_number)
        ->assertDontSee($pendingRx->prescription_number);
});

it('dispenses prescription via FEFO and decrements stock', function () {
    $prescription = Prescription::factory()->create(['status' => 'routed', 'patient_id' => $this->patient->id]);
    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 15,
        'dosage_instructions' => 'Take 1',
    ]);

    // Create 2 batches: one expires sooner, one later
    $batch1 = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 10,
        'expiry_date' => now()->addDays(30),
    ]);

    $batch2 = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 20,
        'expiry_date' => now()->addDays(60),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.dispense', $prescription), [
            'payment_method' => 'cash',
            'allocations' => [
                $this->medicine->id => [
                    $batch1->id => 10,
                    $batch2->id => 5,
                ],
            ],
        ])
        ->assertRedirect();

    $prescription->refresh();
    expect($prescription->status)->toBe('dispensed');

    $batch1->refresh();
    $batch2->refresh();
    expect($batch1->quantity_remaining)->toBe(0)
        ->and($batch2->quantity_remaining)->toBe(15);

    // Assert pos transaction created
    $transaction = PosTransaction::where('prescription_id', $prescription->id)->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->total_amount)->toBe(150.00); // 15 qty * 10 price

    // Assert stock movements
    expect(StockMovement::where('batch_id', $batch1->id)->where('type', 'out')->first()->quantity)->toBe(10);
    expect(StockMovement::where('batch_id', $batch2->id)->where('type', 'out')->first()->quantity)->toBe(5);
});

it('executes OTC sale without prescription using FEFO', function () {
    $batch1 = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 10,
        'expiry_date' => now()->addDays(30),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 5,
                ],
            ],
        ])
        ->assertRedirect();

    $batch1->refresh();
    expect($batch1->quantity_remaining)->toBe(5);

    $transaction = PosTransaction::whereNull('prescription_id')->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->total_amount)->toBe(50.00);

    expect(StockMovement::where('batch_id', $batch1->id)->where('reference_type', 'otc_sale')->first()->quantity)->toBe(5);
});

it('aborts dispensing if stock becomes insufficient', function () {
    $prescription = Prescription::factory()->create(['status' => 'routed', 'patient_id' => $this->patient->id]);
    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 15,
        'dosage_instructions' => 'Take 1',
    ]);

    $batch1 = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 10, // Only 10 available, asking for 15
        'expiry_date' => now()->addDays(30),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.dispense', $prescription), [
            'payment_method' => 'cash',
            'allocations' => [
                $this->medicine->id => [
                    $batch1->id => 15, // Try to allocate 15
                ],
            ],
        ])
        ->assertSessionHas('error'); // Exception caught

    $prescription->refresh();
    expect($prescription->status)->toBe('routed'); // Unchanged

    $batch1->refresh();
    expect($batch1->quantity_remaining)->toBe(10); // Unchanged

    expect(PosTransaction::count())->toBe(0);
});

it('dispenses prescription via JSON for in-page receipt rendering without page reload', function () {
    $prescription = Prescription::factory()->create(['status' => 'routed', 'patient_id' => $this->patient->id]);
    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 2,
        'dosage_instructions' => 'Take 1 twice daily',
    ]);

    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 10,
        'expiry_date' => now()->addDays(90),
    ]);

    $response = actingAs($this->pharmacist)
        ->postJson(route('pos.dispense', $prescription), [
            'payment_method' => 'cash',
            'allocations' => [
                $this->medicine->id => [
                    $batch->id => 2,
                ],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'Prescription dispensed successfully.',
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'transaction' => [
                'id',
                'raw_id',
                'rxId',
                'patient',
                'cashier',
                'total',
                'method',
                'datetime',
                'items',
            ],
        ]);

    expect($response->json('transaction.id'))->toMatch('/^#\d{8}$/');
});

it('processes OTC sale via JSON for immediate in-page receipt display', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 20,
        'expiry_date' => now()->addDays(90),
    ]);

    $response = actingAs($this->pharmacist)
        ->postJson(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 3,
                ],
            ],
        ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'message' => 'OTC Sale completed successfully.',
        ])
        ->assertJsonStructure([
            'transaction' => [
                'id',
                'total',
                'items',
            ],
        ]);

    expect($response->json('transaction.id'))->toMatch('/^#\d{8}$/');
});

it('forbids unauthorized access', function () {
    actingAs($this->nurse)
        ->get(route('pos.index'))
        ->assertStatus(403);
});
