<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        Permission::create(['name' => 'add work position']);
        Permission::create(['name' => 'create work position']);
        Permission::create(['name' => 'view work position']);
        Permission::create(['name' => 'edit work position']);
        Permission::create(['name' => 'delete work position']);
        Permission::create(['name' => 'view table']);
        Permission::create(['name' => 'delete applicant']);
        Permission::create(['name' => 'view applicant']);
        Permission::create(['name' => 'edit applicant']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage all']);

        // Create roles and assign existing permissions
        $roleHR = Role::create(['name' => 'HR']);
        $roleHR->givePermissionTo(['create work position', 'view work position', 'edit work position', 'delete work position','edit applicant', 'view applicant', 'view table']);

        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        $user = User::factory()->create([
            'name' => 'Example User',
            'email' => 'tester@example.com',
        ]);
        $user->assignRole($roleHR);

        $user = User::factory()->create([
            'name' => 'Example Admin User',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($roleAdmin);
    }
}
