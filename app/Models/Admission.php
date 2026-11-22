<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Admission extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'patient_id',
        'bed_id',
        'admitted_by',
        'patient_classification',
        'respiratory_status',
        'chief_complaint',
        'baseline_vitals',
        'admission_date',
        'discharge_date',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'baseline_vitals' => 'array',
            'admission_date' => 'datetime',
            'discharge_date' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Patient, $this>
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * @return BelongsTo<Bed, $this>
     */
    public function bed(): BelongsTo
    {
        return $this->belongsTo(Bed::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function admittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    /**
     * @return HasMany<PatientEndorsement, $this>
     */
    public function endorsements(): HasMany
    {
        return $this->hasMany(PatientEndorsement::class);
    }

    /**
     * Get the latest endorsement for this admission.
     *
     * @return HasOne<PatientEndorsement, $this>
     */
    public function latestEndorsement(): HasOne
    {
        return $this->hasOne(PatientEndorsement::class)->latestOfMany();
    }

    /**
     * @return HasMany<Prescription, $this>
     */
    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Scope for currently admitted patients.
     *
     * @param  Builder<Admission>  $query
     * @return Builder<Admission>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'admitted');
    }

    /**
     * Formatted string of baseline vitals for display.
     */
    public function getFormattedVitalsAttribute(): string
    {
        $vitals = $this->baseline_vitals ?? [];
        if (empty($vitals)) {
            return 'No vitals recorded';
        }

        $parts = [];
        if (! empty($vitals['bp'])) {
            $parts[] = "BP: {$vitals['bp']}";
        }
        if (! empty($vitals['temp'])) {
            $parts[] = "T: {$vitals['temp']}°C";
        }
        if (! empty($vitals['pulse'])) {
            $parts[] = "HR: {$vitals['pulse']} bpm";
        }
        if (! empty($vitals['rr'])) {
            $parts[] = "RR: {$vitals['rr']}/min";
        }
        if (! empty($vitals['spo2'])) {
            $parts[] = "SpO2: {$vitals['spo2']}%";
        }

        return implode(' | ', $parts);
    }
}
