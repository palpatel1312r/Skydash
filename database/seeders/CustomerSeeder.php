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
        // 1. Copy Admins to Customers Table
        $admins = Admin::all();

        foreach ($admins as $admin) {
            Customer::updateOrCreate(
                ['email' => $admin->email], // Check if email exists
                [
                    'fullname' => $admin->name,      // Use fullname instead of name
                    'email' => $admin->email,
                    'password' => Hash::make('1234'), // Default password
                    'role' => 'customer',
                    'status' => 'Active',
                ]
            );
        }

        // 2. Add Additional Customers
        $customers = [
            [
                'fullname' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('1234'),
                'role' => 'customer',
                'status' => 'Active',
            ],
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
