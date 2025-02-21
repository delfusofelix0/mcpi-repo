<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\WorkPosition;
use Illuminate\Database\Seeder;

class WorkPositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Software Engineer', 'description' => 'Responsible for developing software solutions.'],
            ['name' => 'Web Developer', 'description' => 'Specializes in creating websites and web applications.'],
            ['name' => 'Data Analyst', 'description' => 'Analyzes data to help make informed business decisions.'],
            ['name' => 'Product Manager', 'description' => 'Oversees product development from conception to launch.'],
            ['name' => 'UX/UI Designer', 'description' => 'Designs user interfaces and experiences.'],
            ['name' => 'QA Engineer', 'description' => 'Ensures the quality of software through testing.'],
            ['name' => 'DevOps Engineer', 'description' => 'Facilitates collaboration between development and operations.'],
            ['name' => 'Other', 'description' => 'Other positions not listed.']
        ];

        foreach ($positions as $position) {
            WorkPosition::create($position);
        }
    }
}
