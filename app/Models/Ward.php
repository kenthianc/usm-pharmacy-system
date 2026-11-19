<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Ward extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'ward_name',
        'ward_type',
        'nurse_station_assigned',
        'status',
    ];

    /**
     * Scope for active wards.
     *
     * @param  Builder<Ward>  $query
     * @return Builder<Ward>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Get all beds in this ward.
     *
     * @return HasMany<Bed, $this>
     */
    public function beds(): HasMany
    {
        return $this->hasMany(Bed::class);
    }

    /**
     * Get all admissions for beds in this ward.
     *
     * @return HasManyThrough<Admission, Bed, $this>
     */
    public function admissions(): HasManyThrough
    {
        return $this->hasManyThrough(Admission::class, Bed::class);
    }

    /**
     * Count total available beds in this ward.
     */
    public function getAvailableBedsCountAttribute(): int
    {
        return $this->beds->where('occupancy_status', 'available')->count();
    }

    /**
     * Count total occupied beds in this ward.
     */
    public function getOccupiedBedsCountAttribute(): int
    {
        return $this->beds->where('occupancy_status', 'occupied')->count();
    }
}
