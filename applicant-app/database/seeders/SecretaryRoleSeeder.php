<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SecretaryRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Command to run this seeder: php artisan db:seed --class=SecretaryRoleSeeder
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions
        $newPermissions = [
            'view appointments',
            'manage appointments',
            'manage offices'
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Secretary role
        $secretaryRole = Role::firstOrCreate(['name' => 'Secretary']);
        $secretaryRole->syncPermissions($newPermissions);
    }
}
