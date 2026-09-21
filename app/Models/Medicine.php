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
        'code',
        'sku',
        'barcode',
        'name',
        'generic_name',
        'category',
        'unit',
        'unit_price',
        'purchase_price',
        'reorder_level',
        'stockout_risk_score',
        'stockout_risk_category',
        'daily_consumption_rate',
        'is_active',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Medicine $medicine) {
            if (empty($medicine->code)) {
                $uniqueCode = static::generateUniqueProductCode($medicine->category, $medicine->id);
                $medicine->updateQuietly([
                    'code' => $uniqueCode,
                ]);
            }
        });
    }

    /**
     * Generate a guaranteed unique product code based on category prefix and medicine ID.
     */
    public static function generateUniqueProductCode(?string $category, int|string $id): string
    {
        $clean = preg_replace('/[^A-Za-z0-9]/', '', (string) ($category ?: 'MED'));
        $prefix = strtoupper(substr($clean ?: 'MED', 0, 3));
        if (strlen($prefix) < 3) {
            $prefix = str_pad($prefix, 3, 'X');
        }

        $baseCode = $prefix.'-'.str_pad((string) $id, 4, '0', STR_PAD_LEFT);
        $candidate = $baseCode;
        $counter = 1;

        // Ensure uniqueness across the medicines table
        while (static::where('code', $candidate)->where('id', '!=', $id)->exists()) {
            $candidate = $baseCode.'-'.$counter;
            $counter++;
        }

        return $candidate;
    }

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
            'stockout_risk_score' => 'float',
            'daily_consumption_rate' => 'float',
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

    /**
     * Get human-readable label for stockout risk category.
     */
    public function getStockoutRiskLabelAttribute(): string
    {
        return match ($this->stockout_risk_category) {
            'high' => 'High Risk',
            'moderate' => 'Moderate Risk',
            'low' => 'Low Risk',
            default => 'Not Calculated',
        };
    }

    /**
     * Get Tailwind badge classes for stockout risk category.
     */
    public function getStockoutRiskBadgeClassAttribute(): string
    {
        return match ($this->stockout_risk_category) {
            'high' => 'bg-rose-100 text-rose-800 border border-rose-300',
            'moderate' => 'bg-amber-100 text-amber-900 border border-amber-300',
            'low' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            default => 'bg-gray-100 text-gray-600 border border-gray-200',
        };
    }

    /**
     * Get clinical action recommendation for stockout risk category.
     */
    public function getStockoutRiskActionAttribute(): string
    {
        return match ($this->stockout_risk_category) {
            'high' => 'Urgent restock PO trigger or batch return/disposal action',
            'moderate' => 'Monitor, plan PO restock or flag near-expiry batches',
            'low' => 'Normal FEFO rotation',
            default => 'Pending risk calculation',
        };
    }

    /**
     * Get SKU alias for code attribute.
     */
    public function getSkuAttribute(): ?string
    {
        return $this->code;
    }

    /**
     * Set SKU alias for code attribute.
     */
    public function setSkuAttribute(?string $value): void
    {
        $this->attributes['code'] = $value;
    }

    /**
     * Get system item code with fallback.
     */
    public function getItemCodeAttribute(): string
    {
        if (! empty($this->code)) {
            return $this->code;
        }

        return static::generateUniqueProductCode($this->category, $this->id ?? 0);
    }
}
