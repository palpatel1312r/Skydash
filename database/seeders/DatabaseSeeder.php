<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Role::create(['id' => 1, 'name' => 'Super Admin']);
        Role::create(['id' => 2, 'name' => 'Admin']);
        Role::create(['name' => 'Customer']);
        Role::create(['name' => 'Shop Manager']);
        Role::create(['name' => 'Inventory Manager']);
        Role::create(['name' => 'Customer Support']);
        Role::create(['name' => 'Delivery Boy']);

        // 3. Call other seeders
        $this->call([
            AdminSeeder::class,
            CustomerSeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
