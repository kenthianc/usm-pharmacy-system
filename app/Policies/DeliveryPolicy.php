<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view any deliveries.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin', 'pharmacist']);
    }

    /**
     * Determine whether the user can view the delivery.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin', 'pharmacist']);
    }

    /**
     * Determine whether the user can create a delivery request.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin', 'pharmacist']);
    }

    /**
     * Determine whether the user can confirm/receive the delivery.
     * Strictly restricted to Admin and Stock Manager.
     */
    public function confirm(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can cancel the delivery.
     * Strictly restricted to Admin and Stock Manager.
     */
    public function cancel(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }

    /**
     * Determine whether the user can change the delivery status.
     */
    public function updateStatus(User $user, Delivery $delivery): bool
    {
        return $user->hasAnyRole(['stock_manager', 'admin']);
    }
}
