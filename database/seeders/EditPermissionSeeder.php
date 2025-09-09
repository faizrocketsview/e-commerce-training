<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EditPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create edit permissions for each module
        $editPermissions = [
            'ecommerce.managements.categories:edit',
            'ecommerce.managements.products:edit',
            'ecommerce.managements.orders:edit',
            'ecommerce.managements.items:edit',
            'ecommerce.managements.users:edit',
        ];

        foreach ($editPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign edit permissions to admin user
        $admin = \App\Models\User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->givePermissionTo($editPermissions);
        }
    }
}
