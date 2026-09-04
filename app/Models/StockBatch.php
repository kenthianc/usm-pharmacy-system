<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'expiry_date' => 'date',
            'received_date' => 'date',
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
     * Scope a query to only include active (unexpired with remaining stock) batches.
     *
     * @param  Builder<static>  $query
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('expiry_date', '>=', now()->toDateString())
            ->where('quantity_remaining', '>', 0);
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
}
