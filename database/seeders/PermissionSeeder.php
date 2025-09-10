<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for each module
        $permissions = [
            // Product Categories
            'ecommerce.managements.categories:create',
            'ecommerce.managements.categories:read',
            'ecommerce.managements.categories:update',
            'ecommerce.managements.categories:edit',
            'ecommerce.managements.categories:delete',
            
            // Products
            'ecommerce.managements.products:create',
            'ecommerce.managements.products:read',
            'ecommerce.managements.products:update',
            'ecommerce.managements.products:edit',
            'ecommerce.managements.products:delete',
            
            // Orders
            'ecommerce.managements.orders:create',
            'ecommerce.managements.orders:read',
            'ecommerce.managements.orders:update',
            'ecommerce.managements.orders:edit',
            'ecommerce.managements.orders:delete',
            
            // Order Items
            'ecommerce.managements.items:create',
            'ecommerce.managements.items:read',
            'ecommerce.managements.items:update',
            'ecommerce.managements.items:edit',
            'ecommerce.managements.items:delete',
            
            // Users
            'ecommerce.managements.users:create',
            'ecommerce.managements.users:read',
            'ecommerce.managements.users:update',
            'ecommerce.managements.users:edit',
            'ecommerce.managements.users:delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
