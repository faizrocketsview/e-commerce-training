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
        
        // Create or find the first admin user
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create or find the second admin user
        $admin2 = \App\Models\User::firstOrCreate(
            ['email' => 'faizhiruko00@gmail.com'],
            [
                'name' => 'Faiz Nasir',
                'password' => bcrypt('faiz123'),
            ]
        );

        // Create admin role if it doesn't exist
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Assign admin role to both users
        $admin->assignRole($adminRole);
        $admin2->assignRole($adminRole);
        
        // Give admin role all permissions
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());
        
        // Seed products
        $this->call(ProductSeeder::class);
    }
}
