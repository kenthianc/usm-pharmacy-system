<?php

use App\Models\Medicine;
use App\Models\Patient;
use App\Models\PatientBill;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\User;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'pharmacist']);
    Role::firstOrCreate(['name' => 'nurse']);
    Role::firstOrCreate(['name' => 'doctor']);

    $this->pharmacist = User::factory()->create();
    $this->pharmacist->assignRole('pharmacist');

    $this->nurse = User::factory()->create();
    $this->nurse->assignRole('nurse');

    $this->medicine = Medicine::factory()->create(['unit_price' => 112.00]);
    $this->patient = Patient::factory()->create(['patient_type' => 'resident']);
});

it('calculates regular sale with no statutory discount or vat exemption', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'regular',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 10, // 10 * 112 = 1120.00
                ],
            ],
        ])
        ->assertRedirect();

    $transaction = PosTransaction::latest()->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->subtotal)->toBe(1120.00)
        ->and((float) $transaction->vat_exempt_amount)->toBe(0.00)
        ->and((float) $transaction->discount_amount)->toBe(0.00)
        ->and((float) $transaction->net_amount)->toBe(1120.00)
        ->and((float) $transaction->total_amount)->toBe(1120.00)
        ->and($transaction->discount_type)->toBe('regular');
});

it('applies statutory RA 9994 senior citizen 20 percent discount and vat exemption', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6),
    ]);

    // Gross: 10 * 112 = 1120.00
    // Net of VAT: 1120 / 1.12 = 1000.00
    // VAT Exemption: 1120 - 1000 = 120.00
    // 20% Discount on Net of VAT: 1000 * 0.20 = 200.00
    // Net Amount Due: 1000 - 200 = 800.00
    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'senior',
            'discount_id_number' => 'OSCA-2024-9994',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 10,
                ],
            ],
        ])
        ->assertRedirect();

    $transaction = PosTransaction::latest()->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->subtotal)->toBe(1120.00)
        ->and((float) $transaction->vat_exempt_amount)->toBe(120.00)
        ->and((float) $transaction->discount_amount)->toBe(200.00)
        ->and((float) $transaction->net_amount)->toBe(800.00)
        ->and((float) $transaction->total_amount)->toBe(800.00)
        ->and($transaction->discount_type)->toBe('senior')
        ->and($transaction->discount_id_number)->toBe('OSCA-2024-9994');
});

it('applies statutory RA 10754 PWD discount and vat exemption', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'pwd',
            'discount_id_number' => 'PWD-ID-10754',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 10,
                ],
            ],
        ])
        ->assertRedirect();

    $transaction = PosTransaction::latest()->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->vat_exempt_amount)->toBe(120.00)
        ->and((float) $transaction->discount_amount)->toBe(200.00)
        ->and((float) $transaction->net_amount)->toBe(800.00)
        ->and($transaction->discount_type)->toBe('pwd')
        ->and($transaction->discount_id_number)->toBe('PWD-ID-10754');
});

it('applies 10 percent institutional student subsidy discount', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6),
    ]);

    // Gross: 10 * 112 = 1120.00
    // Student 10% Discount: 1120 * 0.10 = 112.00
    // Net: 1120 - 112 = 1008.00
    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'student',
            'discount_id_number' => 'USM-2024-12345',
            'items' => [
                [
                    'medicine_id' => $this->medicine->id,
                    'quantity' => 10,
                ],
            ],
        ])
        ->assertRedirect();

    $transaction = PosTransaction::latest()->first();
    expect($transaction)->not->toBeNull()
        ->and((float) $transaction->subtotal)->toBe(1120.00)
        ->and((float) $transaction->vat_exempt_amount)->toBe(0.00)
        ->and((float) $transaction->discount_amount)->toBe(112.00)
        ->and((float) $transaction->net_amount)->toBe(1008.00)
        ->and($transaction->discount_type)->toBe('student')
        ->and($transaction->discount_id_number)->toBe('USM-2024-12345');
});

it('requires discount ID number when senior or pwd discount is selected', function () {
    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 50,
        'expiry_date' => now()->addMonths(6),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'senior',
            // discount_id_number omitted
            'items' => [
                ['medicine_id' => $this->medicine->id, 'quantity' => 5],
            ],
        ])
        ->assertSessionHasErrors('discount_id_number');

    actingAs($this->pharmacist)
        ->post(route('pos.otc.store'), [
            'payment_method' => 'cash',
            'discount_type' => 'pwd',
            // discount_id_number omitted
            'items' => [
                ['medicine_id' => $this->medicine->id, 'quantity' => 5],
            ],
        ])
        ->assertSessionHasErrors('discount_id_number');
});

it('dispenses in-patient prescription and charges to patient hospital bill', function () {
    $prescription = Prescription::factory()->create([
        'status' => 'routed',
        'patient_id' => $this->patient->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'Ward 4B - Bed 16',
        'billing_status' => 'pending',
    ]);

    $prescription->items()->create([
        'medicine_id' => $this->medicine->id,
        'quantity' => 10,
        'dosage_instructions' => 'Every 8 hours IV',
    ]);

    $batch = StockBatch::factory()->create([
        'medicine_id' => $this->medicine->id,
        'quantity_remaining' => 30,
        'expiry_date' => now()->addMonths(6),
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.dispense', $prescription), [
            'payment_method' => 'hospital_bill',
            'discount_type' => 'regular',
            'allocations' => [
                $this->medicine->id => [
                    $batch->id => 10,
                ],
            ],
        ])
        ->assertRedirect();

    $prescription->refresh();
    expect($prescription->status)->toBe('dispensed')
        ->and($prescription->billing_status)->toBe('billed_to_account');

    $batch->refresh();
    expect($batch->quantity_remaining)->toBe(20);

    // Assert transaction
    $transaction = PosTransaction::where('prescription_id', $prescription->id)->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->order_type)->toBe('inpatient')
        ->and($transaction->room_bed_number)->toBe('Ward 4B - Bed 16')
        ->and($transaction->payment_method)->toBe('hospital_bill')
        ->and($transaction->billing_status)->toBe('billed_to_account')
        ->and((float) $transaction->net_amount)->toBe(1120.00);

    // Assert patient hospital ledger record created
    $bill = PatientBill::where('prescription_id', $prescription->id)->first();
    expect($bill)->not->toBeNull()
        ->and($bill->patient_id)->toBe($this->patient->id)
        ->and($bill->room_bed_number)->toBe('Ward 4B - Bed 16')
        ->and($bill->status)->toBe('billed_to_account')
        ->and((float) $bill->gross_amount)->toBe(1120.00)
        ->and((float) $bill->net_amount)->toBe(1120.00);
});

it('allows pharmacist to mark in-patient ward order as prepared before dispensing', function () {
    $prescription = Prescription::factory()->create([
        'status' => 'routed',
        'patient_id' => $this->patient->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'Room 201 - Bed A',
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.prepare', $prescription), [], ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'rx_id' => $prescription->id,
        ]);

    $prescription->refresh();
    expect($prescription->status)->toBe('prepared');
});

it('settles patient hospital bill upon discharge', function () {
    $prescription = Prescription::factory()->create([
        'status' => 'dispensed',
        'patient_id' => $this->patient->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'ICU - Bed 3',
        'billing_status' => 'billed_to_account',
    ]);

    $transaction = PosTransaction::create([
        'prescription_id' => $prescription->id,
        'cashier_id' => $this->pharmacist->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'ICU - Bed 3',
        'payment_method' => 'hospital_bill',
        'billing_status' => 'billed_to_account',
        'subtotal' => 1500.00,
        'total_amount' => 1500.00,
        'net_amount' => 1500.00,
    ]);

    $bill = PatientBill::create([
        'patient_id' => $this->patient->id,
        'prescription_id' => $prescription->id,
        'pos_transaction_id' => $transaction->id,
        'room_bed_number' => 'ICU - Bed 3',
        'gross_amount' => 1500.00,
        'discount_amount' => 0.00,
        'net_amount' => 1500.00,
        'status' => 'billed_to_account',
        'billed_by' => $this->pharmacist->id,
    ]);

    actingAs($this->pharmacist)
        ->post(route('pos.bills.settle', $bill), [], ['Accept' => 'application/json'])
        ->assertStatus(200)
        ->assertJson([
            'success' => true,
            'bill_id' => $bill->id,
        ]);

    $bill->refresh();
    $prescription->refresh();
    $transaction->refresh();

    expect($bill->status)->toBe('settled')
        ->and($bill->settled_at)->not->toBeNull()
        ->and($bill->settled_by)->toBe($this->pharmacist->id)
        ->and($prescription->billing_status)->toBe('settled')
        ->and($transaction->billing_status)->toBe('settled');
});

it('renders printable receipt with statutory breakdown and ward room bed information', function () {
    $prescription = Prescription::factory()->create([
        'status' => 'dispensed',
        'patient_id' => $this->patient->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'Room 105 - Bed 2',
    ]);

    $transaction = PosTransaction::create([
        'prescription_id' => $prescription->id,
        'cashier_id' => $this->pharmacist->id,
        'order_type' => 'inpatient',
        'room_bed_number' => 'Room 105 - Bed 2',
        'payment_method' => 'hospital_bill',
        'discount_type' => 'senior',
        'discount_id_number' => 'OSCA-RECEIPT-TEST',
        'subtotal' => 1120.00,
        'vat_exempt_amount' => 120.00,
        'discount_amount' => 200.00,
        'net_amount' => 800.00,
        'total_amount' => 800.00,
        'billing_status' => 'billed_to_account',
    ]);

    $batch = StockBatch::factory()->create(['medicine_id' => $this->medicine->id]);

    $transaction->items()->create([
        'medicine_id' => $this->medicine->id,
        'batch_id' => $batch->id,
        'quantity' => 10,
        'unit_price' => 112.00,
        'subtotal' => 1120.00,
    ]);

    actingAs($this->pharmacist)
        ->get(route('pos.receipt', $transaction))
        ->assertStatus(200)
        ->assertSee('Room 105 - Bed 2')
        ->assertSee('OSCA-RECEIPT-TEST')
        ->assertSee('12% VAT Exemption')
        ->assertSee('Senior Citizen 20% Discount')
        ->assertSee('Charge to Hospital Bill')
        ->assertSee('₱800.00');
});
