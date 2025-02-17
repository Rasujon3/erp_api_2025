<?php

namespace App\Modules\Units\Database\Seeders;

use App\Modules\Units\Models\Unit;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'Kilogram',
                'description' => 'Test description',
            ],
            [
                'name' => 'Meter',
                'description' => 'Test description',
            ],
            [
                'name' => 'Second',
                'description' => 'Test description',
            ],
            [
                'name' => 'Metre',
                'description' => 'Test description',
            ],
        ];
        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
