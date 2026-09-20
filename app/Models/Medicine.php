<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicine extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'generic_name',
        'category',
        'unit',
        'unit_price',
        'purchase_price',
        'reorder_level',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'reorder_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active medicines.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the stock batches for the medicine.
     */
    public function stockBatches(): HasMany
    {
        return $this->hasMany(StockBatch::class);
    }

    /**
     * Get the prescription items for the medicine.
     */
    public function prescriptionItems(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Get the stock movements for the medicine.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the delivery items for this medicine.
     */
    public function deliveryItems(): HasMany
    {
        return $this->hasMany(DeliveryItem::class);
    }

    /**
     * Get total unexpired available quantity remaining across all confirmed batches.
     */
    public function getAvailableStockAttribute(): int
    {
        return (int) $this->stockBatches()
            ->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0)
            ->sum('quantity_remaining');
    }

    /**
     * Get total quantity across pending delivery batches and pending delivery requests awaiting physical inspection.
     */
    public function getPendingDeliveryStockAttribute(): int
    {
        $batchPending = (int) $this->stockBatches()
            ->where('status', 'pending')
            ->sum('quantity_received');

        $deliveryPending = (int) $this->deliveryItems()
            ->whereHas('delivery', fn ($q) => $q->where('status', 'pending'))
            ->sum('quantity');

        return $batchPending + $deliveryPending;
    }

    /**
     * Get the retail selling price per unit.
     */
    public function getSellingPriceAttribute(): float
    {
        return (float) $this->unit_price;
    }

    /**
     * Get the purchase / cost price per unit.
     * Falls back to 70% of selling price if purchase_price is not explicitly set.
     */
    public function getCostPriceAttribute(): float
    {
        return (float) ($this->purchase_price ?? round(((float) $this->unit_price) * 0.70, 2));
    }

    /**
     * Total stock valuation based on purchase / cost price.
     */
    public function getStockValueAttribute(): float
    {
        return round($this->available_stock * $this->cost_price, 2);
    }

    /**
     * Total sale valuation based on retail selling price.
     */
    public function getSaleValueAttribute(): float
    {
        return round($this->available_stock * $this->selling_price, 2);
    }

    /**
     * Expected profit for the current stock of this medicine.
     */
    public function getExpectedProfitAttribute(): float
    {
        return round($this->sale_value - $this->stock_value, 2);
    }
}
