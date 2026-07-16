<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Superadmin']);
        Role::create(['name' => 'Shop Manager']);
        Role::create(['name' => 'Inventory Manager']);
        Role::create(['name' => 'Customer Support']);
        Role::create(['name' => 'Delivery Boy']);


        // Call other seeders
        $this->call([
            AdminSeeder::class,
            CustomerSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
