<?php

namespace App\Actions\User;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Formation\Actions\SaveAction;

class EcommerceManagementUserSaveAction extends SaveAction
{
    /**
     * Execute the save action for User model with permission handling
     *
     * @param Object $object The user object to save
     * @param String $actionType The action type (create/edit)
     * @return int The saved user ID
     */
    public function execute(Object $object, String $actionType): int
    {
        if ($actionType == 'create') {
            $newUser = User::create([
                'name' => $object->name,
                'email' => $object->email,
                'password' => Hash::make($object->password),
                'role' => $object->role ?? 'user',
                'created_by' => $object->created_by ?? Auth::id(),
                'updated_by' => $object->updated_by ?? Auth::id(),
                'deleted_token' => $object->deleted_token ?? null,
                'partition_created_at' => $object->partition_created_at ?? now(),
            ]);

            // Process permission fields
            $productsPermissions = $this->processPermissionField($object->products ?? []);
            $categoriesPermissions = $this->processPermissionField($object->categories ?? []);
            $itemsPermissions = $this->processPermissionField($object->items ?? []);
            $usersPermissions = $this->processPermissionField($object->users ?? []);
            $ordersPermissions = $this->processPermissionField($object->orders ?? []);

            $permissions = array_unique(array_merge(
                $productsPermissions, 
                $categoriesPermissions, 
                $itemsPermissions, 
                $usersPermissions, 
                $ordersPermissions
            ), SORT_REGULAR);

            $newUser->syncPermissions($permissions);
            
            return $newUser->id;
        } else {
            // Process permission fields
            $productsPermissions = $this->processPermissionField($object->products ?? []);
            $categoriesPermissions = $this->processPermissionField($object->categories ?? []);
            $itemsPermissions = $this->processPermissionField($object->items ?? []);
            $usersPermissions = $this->processPermissionField($object->users ?? []);
            $ordersPermissions = $this->processPermissionField($object->orders ?? []);

            $permissions = array_unique(array_merge(
                $productsPermissions, 
                $categoriesPermissions, 
                $itemsPermissions, 
                $usersPermissions, 
                $ordersPermissions
            ), SORT_REGULAR);
            
            $user = User::find($object->id);

            $user->update([
                'name' => $object->name,
                'email' => $object->email,
                'role' => $object->role ?? $user->role,
                'updated_by' => Auth::id(),
            ]);
            
            $user->syncPermissions($permissions);

            return $object->id;
        }
    }

    /**
     * Process permission field data (handle both array and pipe-separated string formats)
     *
     * @param mixed $permissionData The permission data
     * @return array Array of permission IDs
     */
    private function processPermissionField($permissionData): array
    {
        if (is_array($permissionData)) {
            // Already an array, filter and convert to integers
            return array_map('intval', array_filter($permissionData, function($value) {
                return !empty($value) && is_numeric($value);
            }));
        } elseif (is_string($permissionData) && !empty($permissionData)) {
            // Pipe-separated string, explode and convert to integers
            return array_map('intval', array_filter(explode('|', $permissionData), function($value) {
                return !empty($value) && is_numeric($value);
            }));
        }
        
        return [];
    }
}
