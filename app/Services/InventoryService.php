<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Create a multi-medicine delivery request with status 'pending'.
     *
     * This creates the delivery request and its medicine items.
     * ZERO stock change occurs at this stage. Stock is only incremented
     * once an Admin or Stock Manager physically confirms the delivery.
     *
     * @param  array{reference_no?: ?string, supplier?: ?string, delivery_date?: ?string, notes?: ?string}  $data
     * @param  array<int, array{medicine_id: int, quantity: int, batch_no?: ?string, expiry_date?: ?string}>  $items
     */
    public function createDeliveryRequest(array $data, array $items, User $creator): Delivery
    {
        return DB::transaction(function () use ($data, $items, $creator) {
            $referenceNo = ! empty($data['reference_no'])
                ? $data['reference_no']
                : ('DEL-'.date('Ymd').'-'.strtoupper(Str::random(5)));

            $delivery = Delivery::create([
                'reference_no' => $referenceNo,
                'supplier' => $data['supplier'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? now()->toDateString(),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => $creator->id,
                'received_by' => null,
                'confirmed_at' => null,
            ]);

            foreach ($items as $itemData) {
                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'medicine_id' => $itemData['medicine_id'],
                    'quantity' => $itemData['quantity'],
                    'batch_no' => $itemData['batch_no'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                ]);
            }

            return $delivery->load(['items.medicine', 'createdBy']);
        });
    }

    /**
     * Confirm a pending delivery request into active usable inventory stock.
     *
     * Only Admin or Stock Manager should call this method.
     * Ensures atomic execution and idempotency: stock cannot be added twice
     * even if refreshed or processed multiple times.
     */
    public function confirmDeliveryRequest(Delivery $delivery, User $actor): Delivery
    {
        return DB::transaction(function () use ($delivery, $actor) {
            // Lock delivery record for update to prevent race conditions
            $delivery = Delivery::where('id', $delivery->id)->lockForUpdate()->firstOrFail();

            // Idempotency check: if already confirmed, do not add stock again
            if ($delivery->status !== 'pending') {
                return $delivery->load(['items.medicine', 'items.stockBatch', 'receivedBy']);
            }

            // Load items
            $delivery->load('items');

            foreach ($delivery->items as $item) {
                // Secondary check: don't duplicate batch if item was somehow already attached
                if ($item->stock_batch_id) {
                    continue;
                }

                $batchNo = ! empty($item->batch_no)
                    ? $item->batch_no
                    : ('BATCH-'.date('Ymd').'-'.strtoupper(Str::random(5)));

                $expiryDate = ! empty($item->expiry_date)
                    ? $item->expiry_date
                    : now()->addMonths(18)->toDateString();

                $batch = StockBatch::create([
                    'medicine_id' => $item->medicine_id,
                    'batch_no' => $batchNo,
                    'quantity_received' => $item->quantity,
                    'quantity_remaining' => $item->quantity,
                    'expiry_date' => $expiryDate,
                    'received_date' => now()->toDateString(),
                    'supplier' => $delivery->supplier,
                    'status' => 'received',
                    'received_by' => $actor->id,
                    'confirmed_at' => now(),
                ]);

                $item->update(['stock_batch_id' => $batch->id]);

                StockMovement::create([
                    'medicine_id' => $item->medicine_id,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $item->quantity,
                    'reference_type' => 'delivery',
                    'reference_id' => $delivery->id,
                    'notes' => "Delivery {$delivery->reference_no} confirmed by {$actor->name}.",
                    'created_by' => $actor->id,
                ]);
            }

            $delivery->update([
                'status' => 'delivered',
                'received_by' => $actor->id,
                'confirmed_at' => now(),
            ]);

            return $delivery->fresh(['items.medicine', 'items.stockBatch', 'receivedBy']);
        });
    }

    /**
     * Cancel a pending delivery request without altering inventory stock.
     */
    public function cancelDeliveryRequest(Delivery $delivery, User $actor, ?string $reason = null): Delivery
    {
        return DB::transaction(function () use ($delivery, $actor, $reason) {
            $delivery = Delivery::where('id', $delivery->id)->lockForUpdate()->firstOrFail();

            if ($delivery->status !== 'pending') {
                return $delivery;
            }

            $delivery->update([
                'status' => 'cancelled',
                'received_by' => $actor->id,
                'confirmed_at' => now(),
                'notes' => $reason ? ($delivery->notes ? $delivery->notes.' | Reason: '.$reason : 'Reason: '.$reason) : $delivery->notes,
            ]);

            return $delivery->fresh(['items.medicine', 'receivedBy']);
        });
    }

    /**
     * Log an incoming delivery shipment for a single medicine (backward compatibility).
     *
     * @param  array{batch_no: string, quantity_received: int, expiry_date: string, received_date: string, supplier: ?string, notes: ?string}  $data
     */
    public function recordDelivery(Medicine $medicine, array $data, User $actor, bool $autoConfirm = false): StockBatch
    {
        return DB::transaction(function () use ($medicine, $data, $actor, $autoConfirm) {
            if ($autoConfirm) {
                $batch = $medicine->stockBatches()->create([
                    'batch_no' => $data['batch_no'],
                    'quantity_received' => $data['quantity_received'],
                    'quantity_remaining' => $data['quantity_received'],
                    'expiry_date' => $data['expiry_date'],
                    'received_date' => $data['received_date'],
                    'supplier' => $data['supplier'] ?? null,
                    'status' => 'received',
                    'received_by' => $actor->id,
                    'confirmed_at' => now(),
                ]);

                StockMovement::create([
                    'medicine_id' => $medicine->id,
                    'batch_id' => $batch->id,
                    'type' => 'in',
                    'quantity' => $data['quantity_received'],
                    'reference_type' => 'delivery',
                    'reference_id' => null,
                    'notes' => $data['notes'] ?? null,
                    'created_by' => $actor->id,
                ]);
            } else {
                // Pending inspection — no stock effect yet.
                $batch = $medicine->stockBatches()->create([
                    'batch_no' => $data['batch_no'],
                    'quantity_received' => $data['quantity_received'],
                    'quantity_remaining' => 0,
                    'expiry_date' => $data['expiry_date'],
                    'received_date' => $data['received_date'],
                    'supplier' => $data['supplier'] ?? null,
                    'status' => 'pending',
                    'received_by' => null,
                    'confirmed_at' => null,
                ]);
            }

            return $batch;
        });
    }

    /**
     * Alias for recordDelivery to support existing controllers and test expectations.
     */
    public function receiveBatch(Medicine $medicine, array $data, User $actor, bool $autoConfirm = false): StockBatch
    {
        return $this->recordDelivery($medicine, $data, $actor, $autoConfirm);
    }

    /**
     * Confirm a pending delivery batch into active usable stock.
     */
    public function confirmDelivery(StockBatch $batch, User $actor): StockBatch
    {
        return DB::transaction(function () use ($batch, $actor) {
            $batch = StockBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            if ($batch->status !== 'pending') {
                return $batch;
            }

            $batch->update([
                'status' => 'received',
                'quantity_remaining' => $batch->quantity_received,
                'received_by' => $actor->id,
                'confirmed_at' => now(),
            ]);

            StockMovement::create([
                'medicine_id' => $batch->medicine_id,
                'batch_id' => $batch->id,
                'type' => 'in',
                'quantity' => $batch->quantity_received,
                'reference_type' => 'delivery',
                'reference_id' => null,
                'notes' => "Delivery confirmed by {$actor->name}.",
                'created_by' => $actor->id,
            ]);

            return $batch->fresh();
        });
    }

    /**
     * Reject / cancel a pending delivery batch.
     */
    public function rejectDelivery(StockBatch $batch, User $actor, ?string $reason = null): StockBatch
    {
        return DB::transaction(function () use ($batch, $actor) {
            $batch = StockBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            if ($batch->status !== 'pending') {
                return $batch;
            }

            $batch->update([
                'status' => 'cancelled',
                'received_by' => $actor->id,
                'confirmed_at' => now(),
            ]);

            return $batch->fresh();
        });
    }

    /**
     * Dispose of an entire received batch (e.g. expired, damaged, recalled).
     */
    public function disposeBatch(StockBatch $batch, string $reason, User $actor): StockMovement
    {
        return DB::transaction(function () use ($batch, $reason, $actor) {
            $batch = StockBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            $disposedQuantity = $batch->quantity_remaining;

            $batch->quantity_remaining = 0;
            $batch->save();

            return StockMovement::create([
                'medicine_id' => $batch->medicine_id,
                'batch_id' => $batch->id,
                'type' => 'disposal',
                'quantity' => $disposedQuantity,
                'reference_type' => 'disposal',
                'reference_id' => null,
                'notes' => $reason,
                'created_by' => $actor->id,
            ]);
        });
    }

    /**
     * Adjust the stock quantity of a batch (e.g. physical count correction).
     */
    public function adjustStock(StockBatch $batch, int $newQuantity, string $reason, User $actor): StockMovement
    {
        return DB::transaction(function () use ($batch, $newQuantity, $reason, $actor) {
            $batch = StockBatch::where('id', $batch->id)->lockForUpdate()->firstOrFail();

            $difference = $newQuantity - $batch->quantity_remaining;

            $batch->quantity_remaining = $newQuantity;
            $batch->save();

            return StockMovement::create([
                'medicine_id' => $batch->medicine_id,
                'batch_id' => $batch->id,
                'type' => 'adjustment',
                'quantity' => abs($difference),
                'reference_type' => 'adjustment',
                'reference_id' => null,
                'notes' => $reason,
                'created_by' => $actor->id,
            ]);
        });
    }
}
