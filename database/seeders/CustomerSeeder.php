<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Combine all customers into one array
        $customers = [
            [
                'fullname' => 'User 1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('1234'),
                'role' => 'customer',
                'status' => 'Active',
            ],
            [
                'fullname' => 'User 2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('1234'),
                'role' => 'customer',
                'status' => 'Active',
            ],
            // Add more customers as needed
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                $customer
            );
        }

        $this->command->info('✅ Customers seeded successfully!');
        $this->command->info('📊 Total customers: ' . Customer::count());
    }
}
