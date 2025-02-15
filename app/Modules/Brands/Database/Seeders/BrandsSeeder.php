<?php

namespace App\Modules\Brands\Database\Seeders;

use App\Modules\Brands\Models\Brand;
use Illuminate\Database\Seeder;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
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
        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
