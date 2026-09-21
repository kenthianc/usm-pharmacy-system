<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockBatch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'medicine_id',
        'batch_no',
        'quantity_received',
        'quantity_remaining',
        'expiry_date',
        'received_date',
        'supplier',
        'status',
        'received_by',
        'confirmed_at',
        'expiry_risk_score',
        'expiry_risk_category',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_received' => 'integer',
            'quantity_remaining' => 'integer',
            'expiry_risk_score' => 'float',
            'expiry_date' => 'date',
            'received_date' => 'date',
            'confirmed_at' => 'datetime',
        ];
    }

    /**
     * Get the medicine associated with the batch.
     */
    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    /**
     * Get the user who confirmed receipt into active inventory.
     */
    public function receivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the stock movements for this batch.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'batch_id');
    }

    /**
     * Check if batch delivery is still pending confirmation.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if batch delivery has been confirmed and added to stock.
     */
    public function isReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Scope a query to only include active (received, unexpired with remaining stock) batches.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
    }

    /**
     * Scope a query to pending delivery batches awaiting physical confirmation.
     *
     * @param  Builder<static>  $query
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to received/confirmed batches.
     *
     * @param  Builder<static>  $query
     */
    public function scopeReceived(Builder $query): Builder
    {
        return $query->where('status', 'received');
    }

    /**
     * Scope a query to cancelled/rejected batches.
     *
     * @param  Builder<static>  $query
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to order batches by First-Expired-First-Out (FEFO).
     *
     * @param  Builder<static>  $query
     */
    public function scopeFefo(Builder $query): Builder
    {
        return $query->orderBy('expiry_date', 'asc');
    }

    /**
     * Scope a query to batches expiring within a given number of days.
     *
     * @param  Builder<static>  $query
     */
    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'received')
            ->where('expiry_date', '>=', now()->toDateString())
            ->where('expiry_date', '<=', now()->addDays($days)->toDateString())
            ->where('quantity_remaining', '>', 0);
    }

    /**
     * Scope a query to batches with high expiry risk.
     *
     * @param  Builder<static>  $query
     */
    public function scopeHighExpiryRisk(Builder $query): Builder
    {
        return $query->where('expiry_risk_category', 'high');
    }

    /**
     * Scope a query to batches with moderate expiry risk.
     *
     * @param  Builder<static>  $query
     */
    public function scopeModerateExpiryRisk(Builder $query): Builder
    {
        return $query->where('expiry_risk_category', 'moderate');
    }

    /**
     * Get human-readable label for expiry risk category.
     */
    public function getExpiryRiskLabelAttribute(): string
    {
        return match ($this->expiry_risk_category) {
            'high' => 'High Risk',
            'moderate' => 'Moderate Risk',
            'low' => 'Low Risk',
            default => 'Not Calculated',
        };
    }

    /**
     * Get Tailwind badge classes for expiry risk category.
     */
    public function getExpiryRiskBadgeClassAttribute(): string
    {
        return match ($this->expiry_risk_category) {
            'high' => 'bg-rose-100 text-rose-800 border border-rose-300',
            'moderate' => 'bg-amber-100 text-amber-900 border border-amber-300',
            'low' => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            default => 'bg-gray-100 text-gray-600 border border-gray-200',
        };
    }

    /**
     * Get clinical action recommendation for expiry risk category.
     */
    public function getExpiryRiskActionAttribute(): string
    {
        return match ($this->expiry_risk_category) {
            'high' => 'Urgent restock PO trigger or batch return/disposal action',
            'moderate' => 'Monitor, plan PO restock or flag near-expiry batches',
            'low' => 'Normal FEFO rotation',
            default => 'Pending risk calculation',
        };
    }
}
