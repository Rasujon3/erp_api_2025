<?php

namespace App\Modules\Banks\Database\Seeders;

use App\Modules\Banks\Models\Bank;
use Illuminate\Database\Seeder;

class banksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'bank_name' => 'National Bank',
                'account_number' => '1234567890',
                'branch_name' => 'Main Branch',
                'iban_number' => 'IBAN123456789',
                'bank_details' => 'Primary account for corporate transactions.',
                'opening_balance' => '10000.00',
                'is_default' => true,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'is_deleted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'bank_name' => 'City Bank',
                'account_number' => '0987654321',
                'branch_name' => 'Downtown Branch',
                'iban_number' => 'IBAN098765432',
                'bank_details' => 'Secondary account for payroll.',
                'opening_balance' => '5000.00',
                'is_default' => false,
                'draft' => true,
                'drafted_at' => now()->subDays(2),
                'is_active' => false,
                'is_deleted' => false,
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
            [
                'bank_name' => 'Global Bank',
                'account_number' => '1122334455',
                'branch_name' => 'North Branch',
                'iban_number' => 'IBAN112233445',
                'bank_details' => 'Closed account, marked for deletion.',
                'opening_balance' => '0.00',
                'is_default' => false,
                'draft' => false,
                'drafted_at' => null,
                'is_active' => true,
                'is_deleted' => true,
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
                'deleted_at' => now()->subDays(3), // Soft-deleted
            ],
        ];

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}
