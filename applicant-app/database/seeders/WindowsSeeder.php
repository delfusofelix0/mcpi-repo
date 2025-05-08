<?php

namespace Database\Seeders;

use App\Models\Window;
use Illuminate\Database\Seeder;

class WindowsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create cashier windows
        Window::create([
            'name' => 'Cashier 1',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Cashier 2',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Cashier 3',
            'is_active' => true,
        ]);

        // Create accounting windows
        Window::create([
            'name' => 'Accounting 1',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Accounting 2',
            'is_active' => true,
        ]);

        // Create registrar windows
        Window::create([
            'name' => 'Registrar 1 - Grade School/College',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Registrar 2 - Junior High School',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Registrar 3 - Senior High School',
            'is_active' => true,
        ]);
    }
}
