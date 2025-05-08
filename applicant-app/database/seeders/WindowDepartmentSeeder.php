<?php

namespace Database\Seeders;

use App\Models\Window;
use Illuminate\Database\Seeder;

class WindowDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map window names to departments based on naming patterns
        $windows = Window::all();

        foreach ($windows as $window) {
            $name = strtolower($window->name);

            if (str_contains($name, 'cashier')) {
                $window->department = 'cashier';
            } elseif (str_contains($name, 'accounting')) {
                $window->department = 'accounting';
            } elseif (str_contains($name, 'registrar')) {
                $window->department = 'registrar';
            }

            $window->save();
        }
    }
}
