<?php

namespace Database\Seeders;

use App\Models\Registration;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WorkPosition;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
//
//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);
        $this->call([
            RolesAndPermissionsSeeder::class,
            WorkPositionSeeder::class,
            RegistrationSeeder::class,
        ]);
    }
}
