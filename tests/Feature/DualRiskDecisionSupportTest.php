<?php

use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\User;
use App\Services\RiskPredictionService;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    $this->seed(RoleSeeder::class);

    $this->stockManager = User::factory()->create();
    $stockRole = Role::findByName('stock_manager');
    $this->stockManager->update(['role_id' => $stockRole->id]);
    $this->stockManager->assignRole($stockRole);

    $this->riskService = app(RiskPredictionService::class);
});

test('dual-risk engine generates actionable decision support recommendations and 1-line priority summary', function () {
    // 1. Critical stockout formulation: 0 stock, 12/d velocity, reorder level 20, lead time 7
    // Threshold = (12 * 7) + 20 = 104 -> Recommended order: 110 tabs
    $criticalMed = Medicine::factory()->create([
        'code' => 'TEST-AZI-01',
        'name' => 'Azithromycin 500mg',
        'unit' => 'tablet',
        'reorder_level' => 20,
        'daily_consumption_rate' => 12.0,
    ]);

    // 2. High expiry batch: 150 units remaining, expires in 10 days, 1.0/d velocity, unit price 28
    // Projected consumption = 10 -> at risk = 140 units -> financial loss = 3,920.00
    $expiringMed = Medicine::factory()->create([
        'code' => 'TEST-AMX-02',
        'name' => 'Co-Amoxiclav 625mg',
        'unit' => 'tablet',
        'unit_price' => 28.00,
        'reorder_level' => 30,
        'daily_consumption_rate' => 1.0,
    ]);

    $expiringBatch = StockBatch::factory()->create([
        'medicine_id' => $expiringMed->id,
        'batch_no' => 'BATCH-EXP-01',
        'quantity_received' => 200,
        'quantity_remaining' => 150,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addDays(10)->toDateString(),
    ]);

    $insights = $this->riskService->getDualEngineInsights(leadTimeDays: 7);

    // Verify Decision Support Top Summary
    expect($insights)->toHaveKey('decision_summary')
        ->and($insights['decision_summary'])->toContain('Priority Actions:')
        ->and($insights['decision_summary'])->toContain('Restock')
        ->and($insights['decision_summary'])->toContain('FEFO rotation');

    // Verify Stockout Prescriptive Recommendation
    $stockoutItem = collect($insights['stockout_insights'])->firstWhere('medicine_id', $criticalMed->id);
    expect($stockoutItem)->not->toBeNull()
        ->and($stockoutItem['score'])->toEqual(100.0)
        ->and($stockoutItem['recommended_reorder_qty'])->toBeGreaterThan(100)
        ->and($stockoutItem['recommended_action'])->toContain('Order')
        ->and($stockoutItem['recommended_action'])->toContain('now')
        ->and($stockoutItem['doh_label'])->toContain('0d');

    // Verify Expiry Prescriptive Guidance
    $expiryItem = collect($insights['expiry_insights'])->firstWhere('batch_id', $expiringBatch->id);
    expect($expiryItem)->not->toBeNull()
        ->and($expiryItem['score'])->toBeGreaterThanOrEqual(70.0)
        ->and($expiryItem['recommended_action'])->toBe('Review for Supplier Return')
        ->and($expiryItem['financial_loss'])->toBeGreaterThan(3000.0);
});

test('what-if lead time simulator dynamically recalculates stockout risk and recommended reorders', function () {
    $med = Medicine::factory()->create([
        'code' => 'TEST-SIM-01',
        'name' => 'Paracetamol 500mg',
        'unit' => 'tablet',
        'reorder_level' => 50,
        'daily_consumption_rate' => 10.0,
    ]);

    // Available stock = 90
    StockBatch::factory()->create([
        'medicine_id' => $med->id,
        'quantity_received' => 90,
        'quantity_remaining' => 90,
        'status' => 'received',
        'expiry_date' => Carbon::now()->addMonths(12)->toDateString(),
    ]);

    // Scenario A: 7-day lead time -> Threshold = (10 * 7) + 50 = 120. Deficit = 30.
    // Sr = (1 - 90 / 120) * 100 = 25.0%
    $insights7 = $this->riskService->getDualEngineInsights(leadTimeDays: 7);
    $item7 = collect($insights7['stockout_insights'])->firstWhere('medicine_id', $med->id);

    // Scenario B: 14-day lead time -> Threshold = (10 * 14) + 50 = 190. Deficit = 100.
    // Sr = (1 - 90 / 190) * 100 = 52.63%
    $insights14 = $this->riskService->getDualEngineInsights(leadTimeDays: 14);
    $item14 = collect($insights14['stockout_insights'])->firstWhere('medicine_id', $med->id);

    expect($item14['score'])->toBeGreaterThan($item7['score'])
        ->and($item14['recommended_reorder_qty'])->toBeGreaterThan($item7['recommended_reorder_qty']);
});

test('stock manager can view decision support UI and access pre-filled restock modal data', function () {
    actingAs($this->stockManager);

    $response = get(route('inventory.risk-engine'));

    $response->assertOk()
        ->assertSee('Risk Forecasting &amp; Demand Intelligence', false)
        ->assertSee('Lead Time (What-If):', false)
        ->assertSee('Sync: Real-time (Stock Movement) • Daily EOD', false)
        ->assertSee('Immediate Stockout Vulnerabilities', false)
        ->assertSee('Imminent Batch Expiry Vulnerabilities', false);
});
