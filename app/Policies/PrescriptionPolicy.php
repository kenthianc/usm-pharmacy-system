<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;

class PrescriptionPolicy
{
    /**
     * Determine whether the user can view any prescriptions.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['nurse', 'pharmacist', 'admin']);
    }

    /**
     * Determine whether the user can view the prescription.
     */
    public function view(User $user, Prescription $prescription): bool
    {
        return $user->hasAnyRole(['nurse', 'pharmacist', 'admin']);
    }

    /**
     * Determine whether the user can create prescriptions.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['nurse', 'admin']);
    }

    /**
     * Determine whether the user can update the prescription.
     */
    public function update(User $user, Prescription $prescription): bool
    {
        return $user->hasAnyRole(['nurse', 'admin'])
            && $prescription->status === 'pending';
    }

    /**
     * Determine whether the user can route the prescription to the pharmacy.
     */
    public function route(User $user, Prescription $prescription): bool
    {
        return $user->hasAnyRole(['nurse', 'admin'])
            && $prescription->status === 'pending';
    }

    /**
     * Determine whether the user can cancel the prescription.
     */
    public function cancel(User $user, Prescription $prescription): bool
    {
        return $user->hasAnyRole(['nurse', 'admin'])
            && $prescription->status === 'pending';
    }
}
