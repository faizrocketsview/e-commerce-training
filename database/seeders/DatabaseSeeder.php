<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed permissions first
        $this->call(PermissionSeeder::class);
        
        // Create the first admin user
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create admin role if it doesn't exist
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Assign admin role to the user
        $admin->assignRole($adminRole);
        
        // Give admin role all permissions
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        
        // Seed products
        $this->call(ProductSeeder::class);
    }
}
