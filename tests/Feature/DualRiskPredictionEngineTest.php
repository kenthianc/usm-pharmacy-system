<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\RiskPredictionService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->stockManager = User::factory()->create();
    $stockManagerRole = Role::findByName('stock_manager');
    $this->stockManager->update(['role_id' => $stockManagerRole->id]);
    $this->stockManager->assignRole($stockManagerRole);

    $this->riskService = app(RiskPredictionService::class);
});

test('it calculates stockout risk correctly using mathematical formula', function () {
    // Ic = 100, Dc = 10, Lt = 7, Bs = 50
    // Target threshold = (10 * 7) + 50 = 120
    // Sr = (1 - 100 / 120) * 100 = (1 - 0.83333) * 100 = 16.67%
    $medicine = Medicine::factory()->create([
        'reorder_level' => 50,
    ]);

    StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 100,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
    ]);

    $result = $this->riskService->calculateStockoutRisk($medicine, leadTimeDays: 7, dailyConsumption: 10.0);

    expect($result['score'])->toEqual(16.67)
        ->and($result['category'])->toBe('low')
        ->and($result['label'])->toBe('Low Risk')
        ->and($result['current_stock'])->toBe(100)
        ->and($result['daily_consumption'])->toEqual(10.0)
        ->and($result['buffer_stock'])->toBe(50)
        ->and($result['reorder_point'])->toEqual(120.0);
});

test('it clamps stockout risk between 0 and 100 and handles edge cases', function () {
    $medicine = Medicine::factory()->create([
        'reorder_level' => 30,
    ]);

    // Adequate stock scenario: Ic (200) >= Threshold (10 * 7 + 30 = 100) -> Sr = 0%
    StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 200,
        'quantity_remaining' => 200,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
    ]);

    $safeResult = $this->riskService->calculateStockoutRisk($medicine, leadTimeDays: 7, dailyConsumption: 10.0);
    expect($safeResult['score'])->toEqual(0.0)
        ->and($safeResult['category'])->toBe('low');

    // Depleted stock scenario: Ic = 0 -> Sr = 100%
    $emptyMedicine = Medicine::factory()->create([
        'reorder_level' => 40,
    ]);

    $depletedResult = $this->riskService->calculateStockoutRisk($emptyMedicine, leadTimeDays: 7, dailyConsumption: 5.0);
    expect($depletedResult['score'])->toEqual(100.0)
        ->and($depletedResult['category'])->toBe('high');

    // Zero demand and zero buffer scenario: target threshold <= 0
    $zeroMedicine = Medicine::factory()->create([
        'reorder_level' => 0,
    ]);
    $zeroResult = $this->riskService->calculateStockoutRisk($zeroMedicine, leadTimeDays: 7, dailyConsumption: 0.0);
    expect($zeroResult['score'])->toEqual(100.0); // 0 stock with 0 threshold is out of stock
});

test('it calculates expiry risk correctly using mathematical formula', function () {
    // Ic = 100, Te = 6 days, Dc = 10 units/day
    // Projected consumption = 10 * 6 = 60
    // Er = max(0, min(100, (1 - 60 / 100) * 100)) = (1 - 0.6) * 100 = 40.0%
    $medicine = Medicine::factory()->create();

    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 100,
        'status' => 'received',
        'expiry_date' => Carbon::today()->addDays(6)->toDateString(),
    ]);

    $result = $this->riskService->calculateExpiryRisk($batch, dailyConsumption: 10.0);

    expect($result['score'])->toEqual(40.0)
        ->and($result['category'])->toBe('moderate')
        ->and($result['label'])->toBe('Moderate Risk')
        ->and($result['remaining_stock'])->toBe(100)
        ->and($result['projected_consumption'])->toEqual(60.0)
        ->and($result['projected_loss_units'])->toEqual(40.0);
});

test('it clamps expiry risk between 0 and 100 and handles edge cases', function () {
    $medicine = Medicine::factory()->create();

    // 1. High consumption covers entire batch before expiry: Dc * Te >= Ic -> Er = 0%
    $safeBatch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 50,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(20)->toDateString(),
    ]);
    // Projected consumption: 5 * 20 = 100 >= 50 remaining -> 0%
    $safeResult = $this->riskService->calculateExpiryRisk($safeBatch, dailyConsumption: 5.0);
    expect($safeResult['score'])->toEqual(0.0)
        ->and($safeResult['category'])->toBe('low');

    // 2. Already expired batch with remaining units: Te <= 0 -> Er = 100%
    $expiredBatch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 25,
        'status' => 'received',
        'expiry_date' => Carbon::now()->subDays(2)->toDateString(),
    ]);
    $expiredResult = $this->riskService->calculateExpiryRisk($expiredBatch, dailyConsumption: 5.0);
    expect($expiredResult['score'])->toEqual(100.0)
        ->and($expiredResult['category'])->toBe('high');

    // 3. Zero daily consumption velocity: Dc = 0 with active stock -> Er = 100%
    $stagnantBatch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 30,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(45)->toDateString(),
    ]);
    $stagnantResult = $this->riskService->calculateExpiryRisk($stagnantBatch, dailyConsumption: 0.0);
    expect($stagnantResult['score'])->toEqual(100.0)
        ->and($stagnantResult['category'])->toBe('high');

    // 4. Depleted batch: Ic = 0 -> Er = 0%
    $emptyBatch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 0,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(10)->toDateString(),
    ]);
    $emptyResult = $this->riskService->calculateExpiryRisk($emptyBatch, dailyConsumption: 5.0);
    expect($emptyResult['score'])->toEqual(0.0);
});

test('it categorizes scores accurately across clinical triage thresholds', function () {
    // Low: < 25
    $low = $this->riskService->categorizeScore(15.5);
    expect($low['category'])->toBe('low')
        ->and($low['label'])->toBe('Low Risk')
        ->and($low['action'])->toBe('Normal FEFO rotation');

    // Moderate: 25 <= score <= 70
    $mod25 = $this->riskService->categorizeScore(25.0);
    expect($mod25['category'])->toBe('moderate');

    $mod50 = $this->riskService->categorizeScore(50.0);
    expect($mod50['category'])->toBe('moderate')
        ->and($mod50['label'])->toBe('Moderate Risk')
        ->and($mod50['action'])->toBe('Monitor, plan PO restock or flag near-expiry batches');

    $mod70 = $this->riskService->categorizeScore(70.0);
    expect($mod70['category'])->toBe('moderate');

    // High: > 70
    $high71 = $this->riskService->categorizeScore(70.1);
    expect($high71['category'])->toBe('high')
        ->and($high71['label'])->toBe('High Risk')
        ->and($high71['action'])->toBe('Urgent restock PO trigger or batch return/disposal action');
});

test('it executes pharmacy:predict-risk command and updates database records', function () {
    $medicine = Medicine::factory()->create([
        'reorder_level' => 20,
        'stockout_risk_score' => null,
        'stockout_risk_category' => null,
    ]);

    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 100,
        'quantity_remaining' => 100,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(60)->toDateString(),
        'expiry_risk_score' => null,
        'expiry_risk_category' => null,
    ]);

    // Create a 30-day stock movement
    StockMovement::factory()->create([
        'medicine_id' => $medicine->id,
        'batch_id' => $batch->id,
        'type' => 'out',
        'quantity' => 60,
        'created_at' => Carbon::now()->subDays(5),
    ]);

    $this->artisan('pharmacy:predict-risk --lead-time=7')
        ->assertSuccessful();

    $medicine->refresh();
    $batch->refresh();

    expect($medicine->stockout_risk_score)->not->toBeNull()
        ->and($medicine->stockout_risk_category)->not->toBeNull()
        ->and($medicine->daily_consumption_rate)->toEqual(2.0)
        ->and($batch->expiry_risk_score)->not->toBeNull()
        ->and($batch->expiry_risk_category)->not->toBeNull();
});

test('it displays risk indicators on inventory index and show views', function () {
    $medicine = Medicine::factory()->create([
        'reorder_level' => 20,
        'stockout_risk_score' => 85.5,
        'stockout_risk_category' => 'high',
        'daily_consumption_rate' => 3.5,
    ]);

    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'quantity_received' => 50,
        'quantity_remaining' => 50,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(20)->toDateString(),
        'expiry_risk_score' => 75.0,
        'expiry_risk_category' => 'high',
    ]);

    actingAs($this->stockManager)
        ->get(route('inventory.index'))
        ->assertOk()
        ->assertSee('Stockout Risk')
        ->assertSee('High Risk')
        ->assertSee('85.5%');

    actingAs($this->stockManager)
        ->get(route('inventory.medicines.show', $medicine))
        ->assertOk()
        ->assertSee('Dual-Risk Prediction Engine')
        ->assertSee('Expiry Risk')
        ->assertSee('Available Stock')
        ->assertSee('Daily Consumption');
});

test('it displays dual-risk engine results immediately on the main dashboard', function () {
    $medicine = Medicine::factory()->create([
        'name' => 'Amoxicillin Trihydrate 500mg',
        'reorder_level' => 30,
        'stockout_risk_score' => 88.0,
        'stockout_risk_category' => 'high',
        'daily_consumption_rate' => 5.0,
    ]);

    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'batch_no' => 'AMX-2026-TEST',
        'quantity_received' => 60,
        'quantity_remaining' => 60,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(15)->toDateString(),
        'expiry_risk_score' => 80.0,
        'expiry_risk_category' => 'high',
    ]);

    actingAs($this->stockManager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('riskEngine')
        ->assertSee('Inventory Risk &amp; Demand Forecasting', false)
        ->assertSee('Stockout Threats')
        ->assertSee('Batches Near Expiry')
        ->assertSee($medicine->item_code)
        ->assertSee('Detailed Risk Analysis');
});

test('it categorizes risk into levels and identifies dual risk items', function () {
    // Medicine with both stockout threat and an expiring batch
    $medicine = Medicine::factory()->create([
        'reorder_level' => 50,
        'unit_price' => 25.00,
    ]);

    // Batch 1 is expiring with remaining stock that won't be fully consumed
    $batch = StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'batch_no' => 'DUAL-RISK-01',
        'quantity_received' => 20,
        'quantity_remaining' => 2,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(2)->toDateString(),
    ]);

    // Add some stock movement to establish consumption velocity
    StockMovement::factory()->create([
        'medicine_id' => $medicine->id,
        'batch_id' => $batch->id,
        'type' => 'out',
        'quantity' => 15,
        'created_at' => Carbon::now()->subDays(3),
    ]);

    $insights = $this->riskService->getDualEngineInsights(leadTimeDays: 7);

    expect($insights['telemetry'])->toHaveKeys(['high_stockout_count', 'high_expiry_count', 'total_financial_loss_at_risk'])
        ->and($insights['stockout_insights'])->not->toBeEmpty();

    $stockoutItem = collect($insights['stockout_insights'])->firstWhere('medicine_id', $medicine->id);
    expect($stockoutItem)->not->toBeNull()
        ->and($stockoutItem)->toHaveKey('level')
        ->and($stockoutItem)->toHaveKey('is_dual_risk');

    actingAs($this->stockManager)
        ->get(route('inventory.risk-engine'))
        ->assertOk()
        ->assertSee('Risk Summary')
        ->assertSee('Risk Forecasting')
        ->assertSee($medicine->item_code);
});

test('pharmacists have read-only visibility into risk analytics and are alerted without mutation permissions', function () {
    $pharmacist = User::factory()->create();
    $pharmacistRole = Role::findByName('pharmacist');
    $pharmacist->update(['role_id' => $pharmacistRole->id]);
    $pharmacist->assignRole($pharmacistRole);

    $medicine = Medicine::factory()->create([
        'code' => 'TEST-PHARM-01',
        'reorder_level' => 100,
    ]);

    StockBatch::factory()->create([
        'medicine_id' => $medicine->id,
        'batch_no' => 'BATCH-PHARM-ALERT',
        'quantity_received' => 10,
        'quantity_remaining' => 2,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(5)->toDateString(),
    ]);

    // 1. Pharmacist can view the Risk Engine analytics hub
    actingAs($pharmacist)
        ->get(route('inventory.risk-engine'))
        ->assertOk()
        ->assertSee('Dispensary Clinical Telemetry &amp; Alert Mode', false)
        ->assertSee('Telemetry Mode')
        ->assertSee('TEST-PHARM-01')
        ->assertDontSee('Recalculate Analysis');

    // 2. Pharmacist CANNOT trigger risk recalculation
    actingAs($pharmacist)
        ->post(route('inventory.risk-engine.recalculate'), ['lead_time' => 7])
        ->assertForbidden();

    // 3. Pharmacist CANNOT access warehouse management routes
    actingAs($pharmacist)
        ->get(route('inventory.index'))
        ->assertForbidden();

    actingAs($pharmacist)
        ->get(route('inventory.deliveries.create'))
        ->assertForbidden();

    // 4. Pharmacist can view the POS terminal with active Dual-Risk alerts
    actingAs($pharmacist)
        ->get(route('pos.index'))
        ->assertOk()
        ->assertSee('Risk Summary');
});

test('risk summary pill is consistently visible at the top for pharmacist, stock_manager, and admin, but hidden for nurse and patient', function () {
    $pharmacist = User::factory()->create();
    $pharmacist->assignRole(Role::findByName('pharmacist'));

    $stockManager = User::factory()->create();
    $stockManager->assignRole(Role::findByName('stock_manager'));

    $admin = User::factory()->create();
    $admin->assignRole(Role::findByName('admin'));

    $nurse = User::factory()->create();
    $nurse->assignRole(Role::findByName('nurse'));

    $patient = User::factory()->create();
    $patient->assignRole(Role::findByName('patient'));
    Patient::factory()->create(['user_id' => $patient->id]);

    // 1. Pharmacist sees Risk Summary at top
    actingAs($pharmacist)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Risk Summary');

    // 2. Stock Manager sees Risk Summary at top
    actingAs($stockManager)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Risk Summary');

    // 3. Admin sees Risk Summary at top
    actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Risk Summary');

    // 4. Nurse does NOT see Risk Summary at top
    actingAs($nurse)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee('Risk Summary');

    // 5. Patient does NOT see Risk Summary at top
    actingAs($patient)
        ->get(route('patient.dashboard'))
        ->assertOk()
        ->assertDontSee('Risk Summary');
});
