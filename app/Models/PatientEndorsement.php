<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientEndorsement extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'admission_id',
        'endorsed_by',
        'receiving_nurse_id',
        'nurse_station',
        'endorsement_notes',
        'acknowledgment_status',
        'acknowledged_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'acknowledged_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Admission, $this>
     */
    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function endorsedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'endorsed_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function receivingNurse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiving_nurse_id');
    }

    /**
     * Scope for pending endorsements.
     *
     * @param  Builder<PatientEndorsement>  $query
     * @return Builder<PatientEndorsement>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('acknowledgment_status', 'pending');
    }

    /**
     * Scope for acknowledged endorsements.
     *
     * @param  Builder<PatientEndorsement>  $query
     * @return Builder<PatientEndorsement>
     */
    public function scopeAcknowledged(Builder $query): Builder
    {
        return $query->where('acknowledgment_status', 'acknowledged');
    }
}
