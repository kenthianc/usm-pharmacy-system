<?php

namespace App\Policies;

use App\Models\PosTransaction;
use App\Models\User;

class PosTransactionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('pharmacist') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PosTransaction $posTransaction): bool
    {
        return $user->hasRole('pharmacist') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('pharmacist') || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PosTransaction $posTransaction): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PosTransaction $posTransaction): bool
    {
        return false;
    }
}
