<?php

namespace App\Modules\Admin\Database\Seeders;

use App\Modules\Admin\Models\AdminClientsTemplate;
use Illuminate\Database\Seeder;

class AdminClientsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminClientsTemplateSeeder = [
            [
                'code' => '101',
                'name' => 'Rapid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '102',
                'name' => 'Star',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '103',
                'name' => 'Walton',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '104',
                'name' => 'Paran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '105',
                'name' => 'BD Food',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($adminClientsTemplateSeeder as $adminClientTemplateSeeder) {
            AdminClientsTemplate::create($adminClientTemplateSeeder);
        }
    }
}
