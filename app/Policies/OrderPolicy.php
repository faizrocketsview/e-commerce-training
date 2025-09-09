<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ecommerce.managements.orders:read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ecommerce.managements.orders:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:update');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function edit(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $model): bool
    {
        return $user->can('ecommerce.managements.orders:delete');
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('ecommerce.managements.orders:delete');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('ecommerce.managements.orders:update');
    }
}