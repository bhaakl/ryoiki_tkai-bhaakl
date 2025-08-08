<?php

namespace App\Policies;

use App\Models\Order;

use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Order $order): bool
    {
        return $order->user_id == request()->header('user');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Order $order): bool
    {
        //
    }
}
