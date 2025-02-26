<?php

namespace App\Modules\Currencies\Database\Seeders;

use App\Modules\Currencies\Models\Currency;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CurrenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'name_in_bangla' => 'ইউরো',
                'name_in_arabic' => 'يورو',
                'is_default' => false,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'symbol' => '€',
                'exchange' => 0.85, // Approx. rate vs USD
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'name_in_bangla' => 'জাপানি ইয়েন',
                'name_in_arabic' => 'ين ياباني',
                'is_default' => false,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'symbol' => '¥',
                'exchange' => 150.25, // Approx. rate vs USD
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
