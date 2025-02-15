<?php

namespace App\Modules\Divisions\Database\Seeders;

use App\Modules\Divisions\Models\Division;
use App\Modules\Groups\Models\Group;
use Illuminate\Database\Seeder;

class DivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'name' => 'Google AdWords',
                'description' => 'Test description',
                'group_id' => '1',
            ],
            [
                'name' => 'Other Search Engines',
                'description' => 'Test description',
                'group_id' => '2',
            ],
            [
                'name' => 'Google (organic)',
                'description' => 'Test description',
                'group_id' => '3',
            ],
            [
                'name' => 'Social Media (Facebook, Twitter etc)',
                'description' => 'Test description',
                'group_id' => '4',
            ],
            [
                'name' => 'Cold Calling/Telemarketing',
                'description' => 'Test description',
                'group_id' => '5',
            ],
            [
                'name' => 'Advertising',
                'description' => 'Test description',
                'group_id' => '6',
            ],
            [
                'name' => 'Custom Referral',
                'description' => 'Test description',
                'group_id' => '7',
            ],
            [
                'name' => 'Expo/Seminar',
                'description' => 'Test description',
                'group_id' => '8',
            ],
        ];
        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}
