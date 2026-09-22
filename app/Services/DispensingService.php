<?php

namespace App\Services;

use App\Models\Medicine;
use App\Models\PatientBill;
use App\Models\PosTransaction;
use App\Models\Prescription;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class DispensingService
{
    /**
     * Suggest batches for a medicine based on FEFO (First-Expired-First-Out).
     */
    public function suggestFefoBatches(Medicine $medicine, int $quantity): array
    {
        $batches = $medicine->stockBatches()
            ->active()
            ->fefo()
            ->get();

        $allocations = [];
        $remaining = $quantity;

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $allocate = min($batch->quantity_remaining, $remaining);
            $allocations[] = [
                'batch_id' => $batch->id,
                'batch_no' => $batch->batch_no,
                'expiry_date' => $batch->expiry_date,
                'quantity' => $allocate,
                'available' => $batch->quantity_remaining,
            ];
            $remaining -= $allocate;
        }

        return $allocations;
    }

    /**
     * Compute statutory discount breakdown (Regular, Senior RA 9994, PWD RA 10754, Student).
     *
     * @return array{gross: float, vat_exempt: float, discount: float, net: float}
     */
    public function calculateDiscountBreakdown(float $grossAmount, string $discountType = 'regular'): array
    {
        $gross = round($grossAmount, 2);

        if (in_array($discountType, ['senior', 'pwd'], true)) {
            // Under Philippine tax law for retail medicine (RA 9994 / RA 10754):
            // 1. VAT Exemption: Remove 12% VAT
            $netOfVat = round($gross / 1.12, 2);
            $vatExempt = round($gross - $netOfVat, 2);
            // 2. 20% discount on the net-of-VAT amount
            $discount = round($netOfVat * 0.20, 2);
            // 3. Net total due
            $net = round($netOfVat - $discount, 2);

            return [
                'gross' => $gross,
                'vat_exempt' => $vatExempt,
                'discount' => $discount,
                'net' => $net,
            ];
        }

        if ($discountType === 'student') {
            // Institutional student health discount (10%)
            $vatExempt = 0.00;
            $discount = round($gross * 0.10, 2);
            $net = round($gross - $discount, 2);

            return [
                'gross' => $gross,
                'vat_exempt' => $vatExempt,
                'discount' => $discount,
                'net' => $net,
            ];
        }

        // Regular: standard rate
        return [
            'gross' => $gross,
            'vat_exempt' => 0.00,
            'discount' => 0.00,
            'net' => $gross,
        ];
    }

    /**
     * Dispense a prescription.
     *
     * @param  array  $allocations  Format: ['medicine_id' => ['batch_id' => quantity, ...], ...]
     *
     * @throws Exception
     */
    public function dispensePrescription(
        Prescription $prescription,
        array $allocations,
        User $cashier,
        string $paymentMethod,
        string $discountType = 'regular',
        ?string $discountIdNumber = null
    ): PosTransaction {
        return DB::transaction(function () use ($prescription, $allocations, $cashier, $paymentMethod, $discountType, $discountIdNumber) {
            $grossAmount = 0;
            $isBilledToAccount = ($paymentMethod === 'hospital_bill');
            $isInpatient = ($isBilledToAccount || ($prescription->order_type ?? null) === 'inpatient');
            $billingStatus = $isBilledToAccount ? 'billed_to_account' : 'paid';

            $transaction = PosTransaction::create([
                'prescription_id' => $prescription->id,
                'cashier_id' => $cashier->id,
                'subtotal' => 0,
                'total_amount' => 0,
                'discount_type' => $discountType,
                'discount_id_number' => $discountIdNumber,
                'vat_exempt_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => 0,
                'payment_method' => $paymentMethod,
                'order_type' => $prescription->order_type ?? 'inpatient',
                'room_bed_number' => $prescription->room_bed_number,
                'billing_status' => $billingStatus,
            ]);

            foreach ($allocations as $medicineId => $batches) {
                $medicine = Medicine::findOrFail($medicineId);

                foreach ($batches as $batchId => $quantity) {
                    if ($quantity <= 0) {
                        continue;
                    }

                    // Lock batch for update
                    $batch = StockBatch::where('id', $batchId)->lockForUpdate()->firstOrFail();

                    if ($batch->quantity_remaining < $quantity) {
                        throw new Exception("Insufficient stock for batch {$batch->batch_no}.");
                    }

                    // Decrement stock
                    $batch->quantity_remaining -= $quantity;
                    $batch->save();

                    $unitPrice = $medicine->unit_price;
                    $subtotal = $unitPrice * $quantity;
                    $grossAmount += $subtotal;

                    // Create Transaction Item
                    $transaction->items()->create([
                        'medicine_id' => $medicineId,
                        'batch_id' => $batchId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    // Create Stock Movement
                    StockMovement::create([
                        'medicine_id' => $medicineId,
                        'batch_id' => $batchId,
                        'type' => 'out',
                        'quantity' => $quantity,
                        'reference_type' => $isInpatient ? 'inpatient_dispensation' : 'prescription',
                        'reference_id' => $prescription->id,
                        'created_by' => $cashier->id,
                        'notes' => $isInpatient ? 'Charged to Hospital Bill: '.($prescription->room_bed_number ?? 'In-Patient') : null,
                    ]);
                }
            }

            $breakdown = $this->calculateDiscountBreakdown($grossAmount, $discountType);

            $transaction->update([
                'subtotal' => $breakdown['gross'],
                'vat_exempt_amount' => $breakdown['vat_exempt'],
                'discount_amount' => $breakdown['discount'],
                'net_amount' => $breakdown['net'],
                'total_amount' => $breakdown['net'],
            ]);

            if ($isBilledToAccount) {
                PatientBill::create([
                    'patient_id' => $prescription->patient_id,
                    'prescription_id' => $prescription->id,
                    'pos_transaction_id' => $transaction->id,
                    'room_bed_number' => $prescription->room_bed_number,
                    'doctor_name' => $prescription->doctor_name,
                    'gross_amount' => $breakdown['gross'],
                    'discount_amount' => $breakdown['discount'],
                    'net_amount' => $breakdown['net'],
                    'status' => 'billed_to_account',
                    'billed_by' => $cashier->id,
                ]);

                $prescription->update([
                    'status' => 'dispensed',
                    'billing_status' => 'billed_to_account',
                ]);
            } else {
                $prescription->update([
                    'status' => 'dispensed',
                    'billing_status' => 'paid',
                ]);
            }

            return $transaction;
        });
    }

    /**
     * Process an OTC sale.
     *
     * @param  array  $items  Format: [['medicine_id' => id, 'quantity' => qty], ...]
     *
     * @throws Exception
     */
    public function processOtcSale(
        array $items,
        User $cashier,
        string $paymentMethod,
        string $discountType = 'regular',
        ?string $discountIdNumber = null
    ): PosTransaction {
        return DB::transaction(function () use ($items, $cashier, $paymentMethod, $discountType, $discountIdNumber) {
            $grossAmount = 0;

            $transaction = PosTransaction::create([
                'prescription_id' => null,
                'cashier_id' => $cashier->id,
                'subtotal' => 0,
                'total_amount' => 0,
                'discount_type' => $discountType,
                'discount_id_number' => $discountIdNumber,
                'vat_exempt_amount' => 0,
                'discount_amount' => 0,
                'net_amount' => 0,
                'payment_method' => $paymentMethod,
                'order_type' => 'outpatient',
                'billing_status' => 'paid',
            ]);

            foreach ($items as $item) {
                $medicine = Medicine::findOrFail($item['medicine_id']);
                $quantityToDispense = $item['quantity'];

                // Get FEFO batches directly inside transaction
                $batches = $medicine->stockBatches()
                    ->active()
                    ->fefo()
                    ->lockForUpdate()
                    ->get();

                $remaining = $quantityToDispense;

                foreach ($batches as $batch) {
                    if ($remaining <= 0) {
                        break;
                    }

                    $allocate = min($batch->quantity_remaining, $remaining);

                    // Decrement stock
                    $batch->quantity_remaining -= $allocate;
                    $batch->save();

                    $unitPrice = $medicine->unit_price;
                    $subtotal = $unitPrice * $allocate;
                    $grossAmount += $subtotal;

                    // Create Transaction Item
                    $transaction->items()->create([
                        'medicine_id' => $medicine->id,
                        'batch_id' => $batch->id,
                        'quantity' => $allocate,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ]);

                    // Create Stock Movement
                    StockMovement::create([
                        'medicine_id' => $medicine->id,
                        'batch_id' => $batch->id,
                        'type' => 'out',
                        'quantity' => $allocate,
                        'reference_type' => 'otc_sale',
                        'reference_id' => $transaction->id,
                        'created_by' => $cashier->id,
                    ]);

                    $remaining -= $allocate;
                }

                if ($remaining > 0) {
                    throw new Exception("Insufficient stock for medicine {$medicine->generic_name}.");
                }
            }

            $breakdown = $this->calculateDiscountBreakdown($grossAmount, $discountType);

            $transaction->update([
                'subtotal' => $breakdown['gross'],
                'vat_exempt_amount' => $breakdown['vat_exempt'],
                'discount_amount' => $breakdown['discount'],
                'net_amount' => $breakdown['net'],
                'total_amount' => $breakdown['net'],
            ]);

            return $transaction;
        });
    }
}
