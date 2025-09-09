<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ecommerce.managements.products:read');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('ecommerce.managements.products:create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:update');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function edit(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:update');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $model): bool
    {
        return $user->can('ecommerce.managements.products:delete');
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        return $user->can('ecommerce.managements.products:delete');
    }

    /**
     * Determine whether the user can reorder models.
     */
    public function reorder(User $user): bool
    {
        return $user->can('ecommerce.managements.products:update');
    }
}