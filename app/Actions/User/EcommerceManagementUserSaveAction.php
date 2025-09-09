<?php

namespace App\Actions\User;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Formation\Actions\SaveAction;
use Spatie\Permission\Models\Role;

class EcommerceManagementUserSaveAction extends SaveAction
{
    /**
     * Execute the save action for User model with permission handling
     *
     * @param object $object The user object to save
     * @param string $actionType The action type (create/edit)
     * @return int The saved user ID
     */
    public function execute(Object $object, String $actionType): int
    {
        if ($actionType == 'create') {
            $newUser = User::create([
                'username' => $object->username ?? null,
                'name' => $object->name,
                'email' => $object->email,
                'contact_number' => $object->contact_number ?? null,
                'password' => Hash::make($object->password),
                'role' => $object->role ?? 'user',
                'created_by' => $object->created_by ?? Auth::id(),
                'updated_by' => $object->updated_by ?? Auth::id(),
                'deleted_token' => $object->deleted_token ?? null,
                'partition_created_at' => $object->partition_created_at ?? now(),
            ]);

            // Ensure Spatie role relation reflects selected role
            if (!empty($object->role)) {
                $roleName = $object->role;
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if (!$role) {
                    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
                }
                $newUser->syncRoles([$role->name]);
            }

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
                'username' => $object->username ?? $user->username,
                'name' => $object->name,
                'email' => $object->email,
                'contact_number' => $object->contact_number ?? $user->contact_number,
                'role' => $object->role ?? $user->role,
                'updated_by' => Auth::id(),
            ]);

            // Sync Spatie role relation when role field changes
            if (!empty($object->role)) {
                $roleName = $object->role;
                $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
                if (!$role) {
                    $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
                }
                $user->syncRoles([$role->name]);
            }
            
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
