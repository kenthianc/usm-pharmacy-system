<?php

use App\Models\Medicine;
use App\Models\StockBatch;
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

    $this->nurse = User::factory()->create();
    $nurseRole = Role::findByName('nurse');
    $this->nurse->update(['role_id' => $nurseRole->id]);
    $this->nurse->assignRole($nurseRole);

    $this->pharmacist = User::factory()->create();
    $pharmacistRole = Role::findByName('pharmacist');
    $this->pharmacist->update(['role_id' => $pharmacistRole->id]);
    $this->pharmacist->assignRole($pharmacistRole);

    $this->patient = User::factory()->create();
    $patientRole = Role::findByName('patient');
    $this->patient->update(['role_id' => $patientRole->id]);
    $this->patient->assignRole($patientRole);
});

test('unauthenticated users cannot access inventory module', function () {
    $this->get(route('inventory.index'))->assertRedirect('/login');
    $this->get(route('inventory.movements'))->assertRedirect('/login');
});

test('stock manager and admin can access inventory index and delivery logs', function () {
    actingAs($this->stockManager)
        ->get(route('inventory.index'))
        ->assertOk()
        ->assertSee('Hospital Inventory & Stock Management');

    actingAs($this->admin)
        ->get(route('inventory.index'))
        ->assertOk();

    actingAs($this->stockManager)
        ->get(route('inventory.movements'))
        ->assertOk()
        ->assertSee('Stock Movements & Delivery Logs');
});

test('nurse, pharmacist, and patient are forbidden from inventory management', function () {
    actingAs($this->nurse)
        ->get(route('inventory.index'))
        ->assertForbidden();

    actingAs($this->pharmacist)
        ->get(route('inventory.index'))
        ->assertForbidden();

    actingAs($this->patient)
        ->get(route('inventory.index'))
        ->assertForbidden();
});

test('stock manager can add a new medicine to formulary', function () {
    actingAs($this->stockManager)
        ->post(route('inventory.medicines.store'), [
            'name' => 'Cetirizine 10mg',
            'generic_name' => 'Cetirizine DiHCL',
            'category' => 'Antihistamine',
            'unit' => 'tablet',
            'unit_price' => 8.50,
            'reorder_level' => 30,
            'is_active' => true,
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('medicines', [
        'name' => 'Cetirizine 10mg',
        'generic_name' => 'Cetirizine DiHCL',
        'category' => 'Antihistamine',
        'unit' => 'tablet',
        'unit_price' => 8.50,
        'reorder_level' => 30,
        'is_active' => true,
    ]);
});

test('stock manager can edit an existing medicine', function () {
    $medicine = Medicine::factory()->create([
        'name' => 'Old Name 500mg',
        'unit_price' => 10.00,
    ]);

    actingAs($this->stockManager)
        ->patch(route('inventory.medicines.update', $medicine), [
            'name' => 'Updated Name 500mg',
            'generic_name' => $medicine->generic_name,
            'category' => $medicine->category,
            'unit' => $medicine->unit,
            'unit_price' => 12.00,
            'reorder_level' => 25,
            'is_active' => true,
        ])
        ->assertRedirect(route('inventory.medicines.show', $medicine));

    $medicine->refresh();
    expect($medicine->name)->toBe('Updated Name 500mg')
        ->and((float) $medicine->unit_price)->toBe(12.00);
});

test('stock manager can receive a stock batch which creates batch and in movement', function () {
    $medicine = Medicine::factory()->create();

    actingAs($this->stockManager)
        ->post(route('inventory.medicines.receive.store', $medicine), [
            'batch_no' => 'BATCH-TEST-900',
            'quantity_received' => 200,
            'expiry_date' => now()->addMonths(12)->toDateString(),
            'received_date' => now()->toDateString(),
            'supplier' => 'Metro Drug Corp.',
            'notes' => 'PO-4482 delivered via refrigerated truck',
        ])
        ->assertRedirect(route('inventory.medicines.show', $medicine));

    $batch = StockBatch::where('batch_no', 'BATCH-TEST-900')->first();
    expect($batch)->not->toBeNull()
        ->and($batch->quantity_received)->toBe(200)
        ->and($batch->quantity_remaining)->toBe(200)
        ->and($batch->supplier)->toBe('Metro Drug Corp.');

    $movement = StockMovement::where('batch_id', $batch->id)->first();
    expect($movement)->not->toBeNull()
        ->and($movement->type)->toBe('in')
        ->and($movement->quantity)->toBe(200)
        ->and($movement->reference_type)->toBe('delivery')
        ->and($movement->notes)->toBe('PO-4482 delivered via refrigerated truck')
        ->and($movement->created_by)->toBe($this->stockManager->id);

    $medicine->refresh();
    expect($medicine->available_stock)->toBe(200);
});

test('stock manager can dispose of a batch', function () {
    $medicine = Medicine::factory()->create();
    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 45,
    ]);

    actingAs($this->stockManager)
        ->post(route('inventory.batches.dispose', $batch), [
            'reason' => 'Compromised packaging during routine storage inspection',
        ])
        ->assertRedirect(route('inventory.medicines.show', $medicine));

    $batch->refresh();
    expect($batch->quantity_remaining)->toBe(0);

    $movement = StockMovement::where('batch_id', $batch->id)
        ->where('type', 'disposal')
        ->first();

    expect($movement)->not->toBeNull()
        ->and($movement->quantity)->toBe(45)
        ->and($movement->notes)->toBe('Compromised packaging during routine storage inspection')
        ->and($movement->created_by)->toBe($this->stockManager->id);
});

test('stock manager can adjust batch stock quantity', function () {
    $medicine = Medicine::factory()->create();
    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 80,
    ]);

    actingAs($this->stockManager)
        ->post(route('inventory.batches.adjust', $batch), [
            'new_quantity' => 75,
            'reason' => 'Physical inventory count difference of 5 units',
        ])
        ->assertRedirect(route('inventory.medicines.show', $medicine));

    $batch->refresh();
    expect($batch->quantity_remaining)->toBe(75);

    $movement = StockMovement::where('batch_id', $batch->id)
        ->where('type', 'adjustment')
        ->first();

    expect($movement)->not->toBeNull()
        ->and($movement->quantity)->toBe(5)
        ->and($movement->notes)->toBe('Physical inventory count difference of 5 units');
});

test('delivery logs page lists and filters movements by type', function () {
    $medicine = Medicine::factory()->create();
    $batch = StockBatch::factory()->create(['medicine_id' => $medicine->id]);

    StockMovement::create([
        'medicine_id' => $medicine->id,
        'batch_id' => $batch->id,
        'type' => 'in',
        'quantity' => 100,
        'reference_type' => 'delivery',
        'created_by' => $this->stockManager->id,
    ]);

    StockMovement::create([
        'medicine_id' => $medicine->id,
        'batch_id' => $batch->id,
        'type' => 'disposal',
        'quantity' => 10,
        'reference_type' => 'disposal',
        'notes' => 'Disposed broken vials',
        'created_by' => $this->stockManager->id,
    ]);

    actingAs($this->stockManager)
        ->get(route('inventory.movements', ['type' => 'disposal']))
        ->assertOk()
        ->assertSee('Disposed broken vials');
});

test('inventory index displays dynamic valuation cards (Total Stock Value, Total Sale Value, Expected Profit)', function () {
    $med1 = Medicine::factory()->create([
        'unit_price' => 20.00,
        'purchase_price' => 12.00,
    ]);
    StockBatch::factory()->create([
        'medicine_id' => $med1->id,
        'status' => 'received',
        'quantity_remaining' => 50,
        'expiry_date' => now()->addYear(),
    ]);

    $med2 = Medicine::factory()->create([
        'unit_price' => 10.00,
        'purchase_price' => null, // fallback to 70% = 7.00
    ]);
    StockBatch::factory()->create([
        'medicine_id' => $med2->id,
        'status' => 'received',
        'quantity_remaining' => 30,
        'expiry_date' => now()->addYear(),
    ]);

    // Expected Stock Value: (50 * 12) + (30 * 7) = 600 + 210 = 810.00
    // Expected Sale Value:  (50 * 20) + (30 * 10) = 1000 + 300 = 1300.00
    // Expected Profit:      1300 - 810 = 490.00

    actingAs($this->stockManager)
        ->get(route('inventory.index'))
        ->assertOk()
        ->assertSee('Total Stock Value')
        ->assertSee('Total Sale Value')
        ->assertSee('Expected Profit')
        ->assertSee('Export Report')
        ->assertSee('810.00')
        ->assertSee('1,300.00')
        ->assertSee('490.00');
});

test('export pdf report downloads valid pdf with inventory calculations and headers', function () {
    $med = Medicine::factory()->create([
        'name' => 'Biogesic Paracetamol',
        'generic_name' => 'Paracetamol',
        'unit_price' => 15.00,
        'purchase_price' => 10.00,
    ]);
    StockBatch::factory()->create([
        'medicine_id' => $med->id,
        'status' => 'received',
        'quantity_remaining' => 100,
        'expiry_date' => now()->addMonths(6),
    ]);

    $response = actingAs($this->stockManager)
        ->get(route('inventory.export-pdf'));

    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
    $pdfContent = $response->getContent();

    // Decompress content streams to verify no font fallback question marks are generated
    preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $pdfContent, $matches);
    $allDecompressed = '';
    foreach ($matches[1] as $stream) {
        $decompressed = @gzuncompress($stream);
        $allDecompressed .= ($decompressed !== false ? $decompressed : $stream);
    }

    // Ensure Times fallback font (/F3) is not used for text and no ? is rendered for amounts
    $hasFallbackQuestionMarks = preg_match('/\[\(\?[0-9]/', $allDecompressed);

    expect($response->headers->get('content-disposition'))->toContain('USM_Pharmacy_Inventory_Report_')
        ->and(str_starts_with($pdfContent, '%PDF'))->toBeTrue()
        ->and($pdfContent)->toContain('DejaVuSans')
        ->and($hasFallbackQuestionMarks)->toBe(0);
});

test('unauthorized roles cannot export inventory pdf report', function () {
    $this->get(route('inventory.export-pdf'))
        ->assertRedirect('/login');

    actingAs($this->nurse)
        ->get(route('inventory.export-pdf'))
        ->assertForbidden();

    actingAs($this->patient)
        ->get(route('inventory.export-pdf'))
        ->assertForbidden();

    actingAs($this->admin)
        ->get(route('inventory.export-pdf'))
        ->assertOk();
});
