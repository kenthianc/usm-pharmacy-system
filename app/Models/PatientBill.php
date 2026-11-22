<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientBill extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'prescription_id',
        'pos_transaction_id',
        'room_bed_number',
        'doctor_name',
        'gross_amount',
        'discount_amount',
        'net_amount',
        'status',
        'billed_by',
        'settled_at',
        'settled_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    /**
     * Patient associated with this hospitalization bill.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Prescription originating the charges.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * POS Transaction containing itemized batch deductions.
     */
    public function posTransaction(): BelongsTo
    {
        return $this->belongsTo(PosTransaction::class);
    }

    /**
     * Staff/Pharmacist who dispensed and billed the order.
     */
    public function billedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billed_by');
    }

    /**
     * Cashier who settled the bill during discharge.
     */
    public function settledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    /**
     * Scope for active bills charged to patient account.
     *
     * @param  Builder<static>  $query
     */
    public function scopeBilledToAccount(Builder $query): Builder
    {
        return $query->where('status', 'billed_to_account');
    }

    /**
     * Scope for settled bills.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSettled(Builder $query): Builder
    {
        return $query->where('status', 'settled');
    }
}
