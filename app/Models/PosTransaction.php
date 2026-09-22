<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'prescription_id',
        'cashier_id',
        'subtotal',
        'total_amount',
        'discount_type',
        'discount_id_number',
        'vat_exempt_amount',
        'discount_amount',
        'net_amount',
        'payment_method',
        'order_type',
        'room_bed_number',
        'billing_status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'vat_exempt_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
        ];
    }

    /**
     * Hospitalization bill associated with this transaction.
     */
    public function patientBill()
    {
        return $this->hasOne(PatientBill::class);
    }

    /**
     * Get the prescription associated with the transaction (null for OTC walk-in sales).
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the cashier (pharmacist/admin user) who processed the transaction.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the itemized line items for this transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PosTransactionItem::class, 'pos_transaction_id');
    }

    /**
     * Check if this transaction is an OTC walk-in sale.
     */
    public function isOtc(): bool
    {
        return is_null($this->prescription_id);
    }
}
