<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prescription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'encoded_by',
        'doctor_name',
        'order_type',
        'room_bed_number',
        'status',
        'billing_status',
    ];

    /**
     * Get the patient that the prescription belongs to.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the user who encoded the prescription.
     */
    public function encodedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encoded_by');
    }

    /**
     * Get the line items of the prescription.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Scope a query to only include pending prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include routed prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeRouted(Builder $query): Builder
    {
        return $query->where('status', 'routed');
    }

    /**
     * Scope a query to only include dispensed prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeDispensed(Builder $query): Builder
    {
        return $query->where('status', 'dispensed');
    }

    /**
     * Scope a query to only include prepared prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopePrepared(Builder $query): Builder
    {
        return $query->where('status', 'prepared');
    }

    /**
     * Scope a query to only include cancelled prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include in-patient prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeInpatient(Builder $query): Builder
    {
        return $query->where('order_type', 'inpatient');
    }

    /**
     * Scope a query to only include out-patient prescriptions.
     *
     * @param  Builder<static>  $query
     */
    public function scopeOutpatient(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('order_type')->orWhere('order_type', 'outpatient');
        });
    }

    /**
     * Get the hospitalization bill associated with the prescription.
     */
    public function patientBill()
    {
        return $this->hasOne(PatientBill::class);
    }

    /**
     * Get the formatted prescription number.
     */
    public function getPrescriptionNumberAttribute(): string
    {
        return 'RX-'.str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }
}
