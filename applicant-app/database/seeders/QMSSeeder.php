<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            QmsRolesSeeder::class,
            WindowsSeeder::class,
            WindowDepartmentSeeder::class
        ]);
    }
}
