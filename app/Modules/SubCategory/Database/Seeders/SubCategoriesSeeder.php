<?php

namespace App\Modules\SubCategory\Database\Seeders;

use App\Modules\SubCategory\Models\SubCategory;
use Illuminate\Database\Seeder;

class SubCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCategories = [
            [
                'name' => 'Google AdWords',
                'description' => 'Test description',
                'category_id' => 1,
            ],
            [
                'name' => 'Other Search Engines',
                'description' => 'Test description',
                'category_id' => 2,
            ],
        ];
        foreach ($subCategories as $subCategory) {
            SubCategory::create($subCategory);
        }
    }
}
