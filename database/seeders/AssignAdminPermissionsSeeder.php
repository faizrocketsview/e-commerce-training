<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignAdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the admin user
        $admin = User::where('email', 'admin@example.com')->first();
        
        if (!$admin) {
            $this->command->error('Admin user with email admin@example.com not found!');
            return;
        }

        // Get all permissions
        $allPermissions = Permission::all();
        
        if ($allPermissions->isEmpty()) {
            $this->command->error('No permissions found! Please run the permission seeder first.');
            return;
        }

        // Assign all permissions to the admin user
        $admin->syncPermissions($allPermissions->pluck('id')->toArray());

        $this->command->info('Successfully assigned ' . $allPermissions->count() . ' permissions to admin@example.com');
        
        // Also create and assign admin role if it doesn't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($allPermissions->pluck('id')->toArray());
        
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info('Admin role created/updated and assigned to admin@example.com');
    }
}