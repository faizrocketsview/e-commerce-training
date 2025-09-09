<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class UpsertPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:upsert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or insert modules into permission table';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        \App\Models\Permission::withTrashed()->update([
            'deleted_at' => now(), 
            'deleted_token' => Str::uuid()
        ]);

        foreach (config('permission.modules') as $modulePermission) {
            $modulePermissions = explode(':', $modulePermission);
            $module = $modulePermissions[0];
            $permissions = explode(',', $modulePermissions[1]);
            
            foreach ($permissions as $permission) {
                \App\Models\Permission::withTrashed()->updateOrCreate(
                    ['name' => $module.':'.$permission, 'guard_name' => 'web'],
                    ['deleted_at' => null, 'deleted_token' => '']
                );
            }
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
