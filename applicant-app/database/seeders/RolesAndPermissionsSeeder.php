<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'view applicants']);
        Permission::create(['name' => 'create applicants']);
        Permission::create(['name' => 'edit applicants']);
        Permission::create(['name' => 'delete applicants']);
        Permission::create(['name' => 'manage users']);

        // Create roles and assign permissions
        $hrRole = Role::create(['name' => 'HR']);
        $hrRole->givePermissionTo(['view applicants', 'create applicants', 'edit applicants']);

        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());
    }

}
