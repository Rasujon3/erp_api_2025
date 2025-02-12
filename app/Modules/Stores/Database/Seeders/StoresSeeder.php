<?php

namespace App\Modules\Stores\Database\Seeders;

use App\Modules\Stores\Models\Store;
use Illuminate\Database\Seeder;

class StoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = [
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
        foreach ($stores as $store) {
            Store::create($store);
        }
    }
}
