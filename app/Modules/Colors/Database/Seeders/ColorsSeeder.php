<?php

namespace App\Modules\Colors\Database\Seeders;

use App\Modules\Colors\Models\Color;
use Illuminate\Database\Seeder;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'name' => 'Red',
                'description' => 'Test description',
            ],
            [
                'name' => 'Green',
                'description' => 'Test description',
            ],
            [
                'name' => 'Blue',
                'description' => 'Test description',
            ],
            [
                'name' => 'Yellow',
                'description' => 'Test description',
            ],
            [
                'name' => 'White',
                'description' => 'Test description',
            ],
            [
                'name' => 'Orange',
                'description' => 'Test description',
            ],
            [
                'name' => 'Pink',
                'description' => 'Test description',
            ],
            [
                'name' => 'Black',
                'description' => 'Test description',
            ],
        ];
        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
