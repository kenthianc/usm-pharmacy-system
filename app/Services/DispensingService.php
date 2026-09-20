<?php

namespace App\Services;

use App\Models\Medicine;
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
     * Dispense a prescription.
     *
     * @param  array  $allocations  Format: ['medicine_id' => ['batch_id' => quantity, ...], ...]
     *
     * @throws Exception
     */
    public function dispensePrescription(Prescription $prescription, array $allocations, User $cashier, string $paymentMethod): PosTransaction
    {
        return DB::transaction(function () use ($prescription, $allocations, $cashier, $paymentMethod) {
            $totalAmount = 0;

            $transaction = PosTransaction::create([
                'prescription_id' => $prescription->id,
                'cashier_id' => $cashier->id,
                'total_amount' => 0, // will update later
                'payment_method' => $paymentMethod,
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

                    $unitPrice = $medicine->unit_price; // Or maybe price is on batch, typically medicine
                    $subtotal = $unitPrice * $quantity;
                    $totalAmount += $subtotal;

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
                        'reference_type' => 'prescription',
                        'reference_id' => $prescription->id,
                        'created_by' => $cashier->id,
                    ]);
                }
            }

            $transaction->update(['total_amount' => $totalAmount]);

            $prescription->update(['status' => 'dispensed']);

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
    public function processOtcSale(array $items, User $cashier, string $paymentMethod): PosTransaction
    {
        return DB::transaction(function () use ($items, $cashier, $paymentMethod) {
            $totalAmount = 0;

            $transaction = PosTransaction::create([
                'prescription_id' => null,
                'cashier_id' => $cashier->id,
                'total_amount' => 0,
                'payment_method' => $paymentMethod,
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
                    $totalAmount += $subtotal;

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

            $transaction->update(['total_amount' => $totalAmount]);

            return $transaction;
        });
    }
}
