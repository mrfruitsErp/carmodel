<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista permessi
        $permissions = [
            'sinistri.view',
            'sinistri.create',
            'sinistri.edit',
            'sinistri.delete',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Ruolo admin
        $admin = Role::firstOrCreate(['name' => 'admin']);

        // Tutti i permessi all’admin
        $admin->syncPermissions($permissions);
    }
}