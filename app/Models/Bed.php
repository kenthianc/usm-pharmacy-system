<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bed extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ward_id',
        'room_number',
        'bed_number',
        'occupancy_status',
    ];

    /**
     * @return BelongsTo<Ward, $this>
     */
    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    /**
     * @return HasMany<Admission, $this>
     */
    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    /**
     * Get the current active admission in this bed.
     *
     * @return HasOne<Admission, $this>
     */
    public function currentAdmission(): HasOne
    {
        return $this->hasOne(Admission::class)->where('status', 'admitted')->latestOfMany();
    }

    /**
     * Full bed identifier label (e.g., "Ward 1 - Rm 101 - Bed 1").
     */
    public function getFullLocationAttribute(): string
    {
        $wardName = $this->ward ? $this->ward->ward_name : 'Ward';

        return "{$wardName} ({$this->room_number} - {$this->bed_number})";
    }

    /**
     * Scope for available beds.
     *
     * @param  Builder<Bed>  $query
     * @return Builder<Bed>
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('occupancy_status', 'available');
    }

    /**
     * Scope for occupied beds.
     *
     * @param  Builder<Bed>  $query
     * @return Builder<Bed>
     */
    public function scopeOccupied(Builder $query): Builder
    {
        return $query->where('occupancy_status', 'occupied');
    }

    /**
     * Scope for maintenance beds.
     *
     * @param  Builder<Bed>  $query
     * @return Builder<Bed>
     */
    public function scopeMaintenance(Builder $query): Builder
    {
        return $query->where('occupancy_status', 'maintenance');
    }
}
