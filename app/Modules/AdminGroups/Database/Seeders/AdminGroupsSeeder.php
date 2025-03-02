<?php

namespace App\Modules\AdminGroups\Database\Seeders;

use App\Modules\AdminGroups\Models\AdminGroup;
use Illuminate\Database\Seeder;

class AdminGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminGroups = [
            [
                'code' => 'AG001',
                'english' => 'Headquarters Admin',
                'arabic' => 'إدارة المقر الرئيسي',
                'bengali' => 'প্রধান কার্যালয় প্রশাসন',
                'country_id' => 217, // e.g., Bangladesh
                'is_default' => true,
                'is_draft' => false,
                'is_active' => true,
                'is_deleted' => false,
                'drafted_at' => null,
                'flag' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AG002',
                'english' => 'Regional Managers',
                'arabic' => 'مديرون إقليميون',
                'bengali' => 'আঞ্চলিক ব্যবস্থাপক',
                'country_id' => 218, // e.g., USA
                'is_default' => false,
                'is_draft' => true,
                'is_active' => false,
                'is_deleted' => false,
                'drafted_at' => now()->subDays(3),
                'flag' => null,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'code' => 'AG003',
                'english' => 'Support Team',
                'arabic' => 'فريق الدعم',
                'bengali' => 'সমর্থন দল',
                'country_id' => 219, // e.g., Saudi Arabia
                'is_default' => false,
                'is_draft' => false,
                'is_active' => true,
                'is_deleted' => true,
                'drafted_at' => null,
                'flag' => null,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
                'deleted_at' => now()->subDays(2), // Soft-deleted
            ],
        ];

        foreach ($adminGroups as $group) {
            AdminGroup::create($group);
        }
    }
}
