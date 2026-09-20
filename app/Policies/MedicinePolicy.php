<?php

namespace App\Policies;

use App\Models\Medicine;
use App\Models\StockBatch;
use App\Models\User;

class MedicinePolicy
{
    /**
     * Determine whether the user can view any medicines.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['stock_manager', 'pharmacist', 'nurse', 'admin']);
    }

    /**
     * Determine whether the user can view the medicine.
     */
    public function view(User $user, Medicine $medicine): bool
    {
        return $user->hasAnyRole(['stock_manager', 'pharmacist', 'nurse', 'admin']);
    }

    /**
     * Determine whether the user can create a medicine.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can update the medicine.
     */
    public function update(User $user, Medicine $medicine): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can deactivate (soft-delete) the medicine.
     */
    public function delete(User $user, Medicine $medicine): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can receive a new batch for the medicine.
     */
    public function receiveBatch(User $user, Medicine $medicine): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can dispose a stock batch.
     */
    public function disposeBatch(User $user, StockBatch $batch): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can adjust a stock batch quantity.
     */
    public function adjustStock(User $user, StockBatch $batch): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }
}
