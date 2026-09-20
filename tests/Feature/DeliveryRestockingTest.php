<?php

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Medicine;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->stockManager = User::factory()->create();
    $stockManagerRole = Role::findByName('stock_manager');
    $this->stockManager->update(['role_id' => $stockManagerRole->id]);
    $this->stockManager->assignRole($stockManagerRole);

    $this->admin = User::factory()->create();
    $adminRole = Role::findByName('admin');
    $this->admin->update(['role_id' => $adminRole->id]);
    $this->admin->assignRole($adminRole);

    $this->pharmacist = User::factory()->create();
    $pharmacistRole = Role::findByName('pharmacist');
    $this->pharmacist->update(['role_id' => $pharmacistRole->id]);
    $this->pharmacist->assignRole($pharmacistRole);

    $this->nurse = User::factory()->create();
    $nurseRole = Role::findByName('nurse');
    $this->nurse->update(['role_id' => $nurseRole->id]);
    $this->nurse->assignRole($nurseRole);

    $this->patient = User::factory()->create();
    $patientRole = Role::findByName('patient');
    $this->patient->update(['role_id' => $patientRole->id]);
    $this->patient->assignRole($patientRole);
});

test('stock manager and admin can access create delivery request form', function () {
    actingAs($this->stockManager)
        ->get(route('inventory.deliveries.create'))
        ->assertOk()
        ->assertSee('Request Delivery / Restock')
        ->assertSee('+ Add Another Medicine');

    actingAs($this->admin)
        ->get(route('inventory.deliveries.create'))
        ->assertOk();
});

test('can create a delivery request with multiple medicines and distinct quantities with status pending and no stock change', function () {
    $paracetamol = Medicine::factory()->create([
        'name' => 'Paracetamol 500mg',
        'generic_name' => 'Paracetamol',
        'unit' => 'pcs',
    ]);
    $amoxicillin = Medicine::factory()->create([
        'name' => 'Amoxicillin 500mg',
        'generic_name' => 'Amoxicillin',
        'unit' => 'pcs',
    ]);
    $cetirizine = Medicine::factory()->create([
        'name' => 'Cetirizine 10mg',
        'generic_name' => 'Cetirizine DiHCL',
        'unit' => 'pcs',
    ]);

    expect($paracetamol->available_stock)->toBe(0)
        ->and($amoxicillin->available_stock)->toBe(0)
        ->and($cetirizine->available_stock)->toBe(0);

    $response = actingAs($this->stockManager)
        ->post(route('inventory.deliveries.store'), [
            'reference_no' => 'PO-2026-MULTI-01',
            'supplier' => 'Metro Drug Corp.',
            'delivery_date' => now()->toDateString(),
            'notes' => 'Bulk restocking shipment',
            'items' => [
                [
                    'medicine_id' => $paracetamol->id,
                    'quantity' => 100,
                    'batch_no' => 'BATCH-PARA-100',
                    'expiry_date' => now()->addMonths(12)->toDateString(),
                ],
                [
                    'medicine_id' => $amoxicillin->id,
                    'quantity' => 50,
                    'batch_no' => 'BATCH-AMOX-50',
                    'expiry_date' => now()->addMonths(18)->toDateString(),
                ],
                [
                    'medicine_id' => $cetirizine->id,
                    'quantity' => 30,
                    'batch_no' => 'BATCH-CETI-30',
                    'expiry_date' => now()->addMonths(24)->toDateString(),
                ],
            ],
        ]);

    $response->assertRedirect(route('inventory.movements'));

    // Check delivery in database
    $delivery = Delivery::where('reference_no', 'PO-2026-MULTI-01')->first();
    expect($delivery)->not->toBeNull()
        ->and($delivery->status)->toBe('pending')
        ->and($delivery->supplier)->toBe('Metro Drug Corp.')
        ->and($delivery->items)->toHaveCount(3);

    // Verify stock has NOT changed (workflow: Create Request -> Pending -> No stock change)
    $paracetamol->refresh();
    $amoxicillin->refresh();
    $cetirizine->refresh();

    expect($paracetamol->available_stock)->toBe(0)
        ->and($amoxicillin->available_stock)->toBe(0)
        ->and($cetirizine->available_stock)->toBe(0);

    // Pending delivery stock should reflect the requested quantities
    expect($paracetamol->pending_delivery_stock)->toBe(100)
        ->and($amoxicillin->pending_delivery_stock)->toBe(50)
        ->and($cetirizine->pending_delivery_stock)->toBe(30);

    // No stock movements should exist yet
    expect(StockMovement::count())->toBe(0);
});

test('stock manager can confirm pending delivery which increases inventory stock and logs movements', function () {
    $paracetamol = Medicine::factory()->create(['name' => 'Paracetamol 500mg', 'unit' => 'pcs']);
    $amoxicillin = Medicine::factory()->create(['name' => 'Amoxicillin 500mg', 'unit' => 'pcs']);

    $delivery = Delivery::factory()->create([
        'reference_no' => 'PO-CONFIRM-01',
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $paracetamol->id,
        'quantity' => 100,
        'batch_no' => 'BATCH-PARA-CONFIRM',
        'expiry_date' => now()->addMonths(12)->toDateString(),
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $amoxicillin->id,
        'quantity' => 50,
        'batch_no' => 'BATCH-AMOX-CONFIRM',
        'expiry_date' => now()->addMonths(18)->toDateString(),
    ]);

    expect($paracetamol->available_stock)->toBe(0)
        ->and($amoxicillin->available_stock)->toBe(0);

    // Confirm delivery
    actingAs($this->stockManager)
        ->post(route('inventory.deliveries.confirm', $delivery))
        ->assertRedirect(route('inventory.movements'));

    $delivery->refresh();
    expect($delivery->status)->toBe('delivered')
        ->and($delivery->received_by)->toBe($this->stockManager->id)
        ->and($delivery->confirmed_at)->not->toBeNull();

    // Check stock has increased
    $paracetamol->refresh();
    $amoxicillin->refresh();
    expect($paracetamol->available_stock)->toBe(100)
        ->and($amoxicillin->available_stock)->toBe(50);

    // Check stock movements created
    $movements = StockMovement::where('reference_id', $delivery->id)
        ->where('reference_type', 'delivery')
        ->get();

    expect($movements)->toHaveCount(2)
        ->and($movements->where('medicine_id', $paracetamol->id)->first()->quantity)->toBe(100)
        ->and($movements->where('medicine_id', $amoxicillin->id)->first()->quantity)->toBe(50);
});

test('admin can confirm pending delivery', function () {
    $medicine = Medicine::factory()->create();

    $delivery = Delivery::factory()->create([
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $medicine->id,
        'quantity' => 80,
    ]);

    actingAs($this->admin)
        ->post(route('inventory.deliveries.confirm', $delivery))
        ->assertRedirect(route('inventory.movements'));

    $delivery->refresh();
    expect($delivery->status)->toBe('delivered')
        ->and($delivery->received_by)->toBe($this->admin->id);

    $medicine->refresh();
    expect($medicine->available_stock)->toBe(80);
});

test('canceled delivery does not add anything to stock', function () {
    $medicine = Medicine::factory()->create();

    $delivery = Delivery::factory()->create([
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $medicine->id,
        'quantity' => 150,
    ]);

    actingAs($this->stockManager)
        ->post(route('inventory.deliveries.cancel', $delivery), [
            'reason' => 'Damaged during transit',
        ])
        ->assertRedirect(route('inventory.movements'));

    $delivery->refresh();
    expect($delivery->status)->toBe('cancelled')
        ->and($delivery->notes)->toContain('Damaged during transit');

    $medicine->refresh();
    expect($medicine->available_stock)->toBe(0);
    expect(StockMovement::count())->toBe(0);
});

test('stock is added only once even if confirm is called multiple times (idempotency)', function () {
    $medicine = Medicine::factory()->create();

    $delivery = Delivery::factory()->create([
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $medicine->id,
        'quantity' => 100,
    ]);

    // First confirmation
    actingAs($this->stockManager)
        ->post(route('inventory.deliveries.confirm', $delivery));

    $medicine->refresh();
    expect($medicine->available_stock)->toBe(100);
    expect(StockMovement::count())->toBe(1);

    // Second confirmation attempt (refresh or duplicate request)
    actingAs($this->stockManager)
        ->post(route('inventory.deliveries.confirm', $delivery));

    $medicine->refresh();
    // Must remain 100, NOT 200
    expect($medicine->available_stock)->toBe(100);
    expect(StockMovement::count())->toBe(1);
});

test('pharmacist, nurse, and patient are forbidden from confirming or canceling deliveries', function () {
    $medicine = Medicine::factory()->create();

    $delivery = Delivery::factory()->create([
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $medicine->id,
        'quantity' => 50,
    ]);

    // Nurse
    actingAs($this->nurse)
        ->post(route('inventory.deliveries.confirm', $delivery))
        ->assertForbidden();

    actingAs($this->nurse)
        ->post(route('inventory.deliveries.cancel', $delivery))
        ->assertForbidden();

    // Pharmacist
    actingAs($this->pharmacist)
        ->post(route('inventory.deliveries.confirm', $delivery))
        ->assertForbidden();

    actingAs($this->pharmacist)
        ->post(route('inventory.deliveries.cancel', $delivery))
        ->assertForbidden();

    // Patient
    actingAs($this->patient)
        ->post(route('inventory.deliveries.confirm', $delivery))
        ->assertForbidden();

    actingAs($this->patient)
        ->post(route('inventory.deliveries.cancel', $delivery))
        ->assertForbidden();

    // Verify delivery remains pending and stock untouched
    $delivery->refresh();
    expect($delivery->status)->toBe('pending');
    $medicine->refresh();
    expect($medicine->available_stock)->toBe(0);
});

test('delivery logs page displays deliveries with status and items summary', function () {
    $medicine = Medicine::factory()->create(['name' => 'Paracetamol 500mg', 'unit' => 'pcs']);

    $delivery = Delivery::factory()->create([
        'reference_no' => 'PO-LOGS-DISPLAY-01',
        'status' => 'pending',
        'created_by' => $this->stockManager->id,
    ]);

    DeliveryItem::create([
        'delivery_id' => $delivery->id,
        'medicine_id' => $medicine->id,
        'quantity' => 120,
    ]);

    actingAs($this->stockManager)
        ->get(route('inventory.movements'))
        ->assertOk()
        ->assertSee('PO-LOGS-DISPLAY-01')
        ->assertSee('Paracetamol 500mg')
        ->assertSee('120')
        ->assertSee('Pending')
        ->assertSee('Confirm Delivery')
        ->assertSee('Cancel');
});
