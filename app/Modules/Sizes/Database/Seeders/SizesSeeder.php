<?php

namespace App\Modules\Sizes\Database\Seeders;

use App\Modules\Sizes\Models\Size;
use Illuminate\Database\Seeder;

class SizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
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
        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
