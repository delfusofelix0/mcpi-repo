<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            WorkPositionSeeder::class,
            SecretaryRoleSeeder::class,
        ]);

        // Create Admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // Change this to a secure password
        ]);
        $admin->assignRole('Admin');

        // Create HR user
        $hr = User::factory()->create([
            'name' => 'HR User',
            'email' => 'hr@example.com',
            'password' => Hash::make('password'), // Change this to a secure password
        ]);
        $hr->assignRole('HR');

        // Your existing test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
