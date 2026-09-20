<?php

namespace App\Policies;

use App\Models\StockBatch;
use App\Models\User;

class StockBatchPolicy
{
    /**
     * Determine whether the user can view any stock batches.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['stock_manager', 'pharmacist', 'nurse', 'admin']);
    }

    /**
     * Determine whether the user can view the stock batch.
     */
    public function view(User $user, StockBatch $batch): bool
    {
        return $user->hasAnyRole(['stock_manager', 'pharmacist', 'nurse', 'admin']);
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
