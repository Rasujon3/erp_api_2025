<?php

namespace App\Modules\Category\Database\Seeders;

use App\Modules\Category\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
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
                'department_id' => '22'
            ],
            [
                'name' => 'Other Search Engines',
                'description' => 'Test description',
                'department_id' => '23'
            ],
        ];
        foreach ($stores as $store) {
            Category::create($store);
        }
    }
}
