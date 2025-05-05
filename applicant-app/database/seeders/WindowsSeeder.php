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
        // Create the two cashier windows
        Window::create([
            'name' => 'Cashier 1',
            'is_active' => true,
        ]);

        Window::create([
            'name' => 'Cashier 2',
            'is_active' => true,
        ]);
    }
}
