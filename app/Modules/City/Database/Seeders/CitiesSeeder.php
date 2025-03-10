<?php

namespace App\Modules\City\Database\Seeders;

use App\Modules\City\Models\City;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            [
                'name' => 'California',
                'name_in_bangla' => 'ক্যালিফোর্নিয়া',
                'name_in_arabic' => 'كاليفورنيا',
                'country_id' => 215,
                'state_id' => 1,
                'is_default' => 0,
                'draft' => 0,
                'drafted_at' => null,
                'is_active' => 1,
                'description' => 'State of the USA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Texas',
                'name_in_bangla' => 'টেক্সাস',
                'name_in_arabic' => 'تكساس',
                'country_id' => 216,
                'state_id' => 2,
                'is_default' => 0,
                'draft' => 0,
                'drafted_at' => null,
                'is_active' => 1,
                'description' => 'State in the southern USA',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Dhaka',
                'name_in_bangla' => 'ঢাকা',
                'name_in_arabic' => 'دكا',
                'country_id' => 217,
                'state_id' => 3,
                'is_default' => 1,
                'draft' => 0,
                'drafted_at' => null,
                'is_active' => 1,
                'description' => 'Capital of Bangladesh',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        foreach ($states as $state) {
            City::create($state);
        }
    }
}
