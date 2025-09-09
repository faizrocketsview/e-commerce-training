<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderItemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ecommerce.managements.items:read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderItem $model): bool
    {
        return $user->can('ecommerce.managements.items:read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ecommerce.managements.items:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderItem $model): bool
    {
        return $user->can('ecommerce.managements.items:update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderItem $model): bool
    {
        return $user->can('ecommerce.managements.items:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderItem $model): bool
    {
        return $user->can('ecommerce.managements.items:update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderItem $model): bool
    {
        return $user->can('ecommerce.managements.items:delete');
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('ecommerce.managements.items:delete');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('ecommerce.managements.items:update');
    }
}