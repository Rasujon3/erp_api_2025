<?php

namespace App\Modules\AdminGroups\Database\Seeders;

use App\Modules\AdminGroups\Models\AdminGroupTemplate;
use Illuminate\Database\Seeder;

class AdminGroupTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminGroupTemplates = [
            [
                'code' => '101',
                'english' => 'Rapid',
                'arabic' => 'إدارة المقر الرئيسي',
                'bengali' => 'প্রধান কার্যালয় প্রশাসন',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '102',
                'english' => 'Star',
                'arabic' => 'مديرون إقليميون',
                'bengali' => 'আঞ্চলিক ব্যবস্থাপক',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '103',
                'english' => 'Walton',
                'arabic' => 'فريق الدعم',
                'bengali' => 'সমর্থন দল',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '104',
                'english' => 'Paran',
                'arabic' => 'فريق الدعم',
                'bengali' => 'আঞ্চলিক সমর্থন দল',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '105',
                'english' => 'BD Food',
                'arabic' => 'فريق الدعم',
                'bengali' => 'প্রধান সমর্থন দল',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($adminGroupTemplates as $adminGroupTemplate) {
            AdminGroupTemplate::create($adminGroupTemplate);
        }
    }
}
