<?php

namespace App\Modules\Branches\Database\Seeders;

use App\Modules\Branches\Models\Branch;
use Illuminate\Database\Seeder;

class BranchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = [
            [
                'code' => 'BR001',
                'name' => 'Downtown Branch',
                'company_name' => 'Tech Corp',
                'website' => 'https://techcorp.com',
                'vat_number' => 'VAT123456',
                'city' => 'Dhaka',
                'state' => 'Dhaka Division',
                'bank_id' => 1, // Assumes bank with ID 1 exists
                'country_id' => 217, // Assumes country with ID 1 exists (e.g., Bangladesh)
                'currency_id' => 1, // Assumes currency with ID 1 exists (e.g., BDT)
                'zip_code' => '1205',
                'phone' => '+8801234567890',
                'address' => '123 Tech Street, Dhaka, Bangladesh',
                'is_default' => true,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'is_deleted' => false,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BR002',
                'name' => 'Uptown Branch',
                'company_name' => 'Tech Corp',
                'website' => 'https://techcorp.com/uptown',
                'vat_number' => 'VAT654321',
                'city' => 'New York',
                'state' => 'New York',
                'bank_id' => 2, // Assumes bank with ID 2 exists
                'country_id' => 218, // Assumes country with ID 2 exists (e.g., USA)
                'currency_id' => 2, // Assumes currency with ID 2 exists (e.g., USD)
                'zip_code' => '10001',
                'phone' => '+12025550123',
                'address' => '456 Business Ave, New York, NY, USA',
                'is_default' => false,
                'draft' => true,
                'drafted_at' => now()->subDays(3),
                'is_active' => false,
                'is_deleted' => false,
                'status' => false,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'code' => 'BR003',
                'name' => 'North Branch',
                'company_name' => 'Tech Corp',
                'website' => null,
                'vat_number' => 'VAT987654',
                'city' => 'Riyadh',
                'state' => 'Riyadh Region',
                'bank_id' => 4, // Assumes bank with ID 3 exists
                'country_id' => 219, // Assumes country with ID 3 exists (e.g., Saudi Arabia)
                'currency_id' => 6, // Assumes currency with ID 3 exists (e.g., SAR)
                'zip_code' => '11564',
                'phone' => '+966112345678',
                'address' => '789 North Rd, Riyadh, Saudi Arabia',
                'is_default' => false,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'is_deleted' => true,
                'status' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
                'deleted_at' => now()->subDays(2), // Soft-deleted
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
