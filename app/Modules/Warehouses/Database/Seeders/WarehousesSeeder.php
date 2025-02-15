<?php

namespace App\Modules\Warehouses\Database\Seeders;

use App\Modules\Warehouses\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'Google AdWords',
                'description' => 'Test description',
                'division_id' => '1',
            ],
            [
                'name' => 'Other Search Engines',
                'description' => 'Test description',
                'division_id' => '2',
            ],
            [
                'name' => 'Google (organic)',
                'description' => 'Test description',
                'division_id' => '3',
            ],
            [
                'name' => 'Social Media (Facebook, Twitter etc)',
                'description' => 'Test description',
                'division_id' => '4',
            ],
            [
                'name' => 'Cold Calling/Telemarketing',
                'description' => 'Test description',
                'division_id' => '5',
            ],
            [
                'name' => 'Advertising',
                'description' => 'Test description',
                'division_id' => '6',
            ],
            [
                'name' => 'Custom Referral',
                'description' => 'Test description',
                'division_id' => '7',
            ],
            [
                'name' => 'Expo/Seminar',
                'description' => 'Test description',
                'division_id' => '8',
            ],
        ];
        foreach ($warehouses as $warehouse) {
            Warehouse::create($warehouse);
        }
    }
}
