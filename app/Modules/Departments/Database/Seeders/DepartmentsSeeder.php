<?php

namespace App\Modules\Departments\Database\Seeders;

use App\Modules\Departments\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Google AdWords',
                'description' => 'Test description',
            ],
            [
                'name' => 'Other Search Engines',
                'description' => 'Test description',
            ],
            [
                'name' => 'Google (organic)',
                'description' => 'Test description',
            ],
            [
                'name' => 'Social Media (Facebook, Twitter etc)',
                'description' => 'Test description',
            ],
            [
                'name' => 'Cold Calling/Telemarketing',
                'description' => 'Test description',
            ],
            [
                'name' => 'Advertising',
                'description' => 'Test description',
            ],
            [
                'name' => 'Custom Referral',
                'description' => 'Test description',
            ],
            [
                'name' => 'Expo/Seminar',
                'description' => 'Test description',
            ],
        ];
        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
