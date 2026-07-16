<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::create([
            'fullname' => 'User 1',
            'email' => 'User1@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 3, // ✅ Customer
            'status' => 'Active',
        ]);

        Customer::create([
            'fullname' => 'User 2',
            'email' => 'User2@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 3, // ✅ Customer
            'status' => 'Active',
        ]);

        Customer::create([
            'fullname' => 'User 3',
            'email' => 'User3@gmail.com',
            'password' => Hash::make('1234'),
            'role_id' => 3, // ✅ Customer
            'status' => 'Active',
        ]);

        $this->command->info('✅ 3 Customers seeded successfully!');
    }
}
