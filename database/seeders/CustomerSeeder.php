<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // 🛑 Make sure you have a Role with ID 1 (e.g., "Customer") in your roles table!

        Customer::create([
            'fullname' => 'User 1',
            'email' => 'User1@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 1, 
            'status' => 'Active',
        ]);

        Customer::create([
            'fullname' => 'User 2',
            'email' => 'User2@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 1,
            'status' => 'Active',
        ]);

        Customer::create([
            'fullname' => 'User 3',
            'email' => 'User3@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 1,
            'status' => 'Active',
        ]);

        $this->command->info('✅ 3 Customers seeded successfully!');
    }
}
