<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class QmsRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Command to run this seeder: php artisan db:seed --class=QmsRolesSeeder
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create new permissions
        $qmsPermissions = [
            'view tickets',
            'call tickets',
            'complete tickets',
            'skip tickets',
            'manage windows'
        ];

        foreach ($qmsPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create QMS roles with specified number of users
        $roleConfig = [
            'Accounting' => 2,
            'Registrar' => 3,
            'Cashier' => 3
        ];

        foreach ($roleConfig as $roleName => $userCount) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions([
                'view tickets',
                'call tickets',
                'complete tickets',
                'skip tickets'
            ]);

            // Create multiple users for each role
            for ($i = 1; $i <= $userCount; $i++) {
                $user = User::factory()->create([
                    'name' => "$roleName User $i",
                    'email' => strtolower($roleName) . $i . '@mcpi.edu.ph',
                    'password' => Hash::make('MCPI@2025'), // Change this to a secure password
                ]);

                $user->assignRole($roleName);
            }
        }
    }
}
