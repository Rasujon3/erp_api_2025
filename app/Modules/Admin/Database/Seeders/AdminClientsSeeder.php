<?php

namespace App\Modules\Admin\Database\Seeders;

use App\Modules\Admin\Models\AdminClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'code' => Str::random(6),
                'name' => 'Client One',
                'admin_user_id' => 1, // Assumes an admin user with ID 1 exists
                'type' => 'system',
                'password' => Hash::make('12345678'),
                'confirm_password' => Hash::make('12345678'),
                'otp_enable' => true,
                'is_active' => true,
                'is_draft' => false,
                'is_delete' => false,
                'address' => '123 Main St',
                'phone' => '555-0101',
                'mobile' => '555-0102',
                'email' => 'client1@example.com',
                'website' => 'https://client1.com',
                'location' => 'New York, NY',
            ],
        ];

        // Insert the sample data
        foreach ($clients as $client) {
            AdminClient::create($client);
        }
    }
}
