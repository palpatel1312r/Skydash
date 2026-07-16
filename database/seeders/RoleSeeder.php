<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ These will be created in this exact order
        Role::create(['name' => 'Superadmin']);        // ID 1
        Role::create(['name' => 'Admin']);             // ID 2
        Role::create(['name' => 'Customer']);          // ID 3
        Role::create(['name' => 'Shop Manager']);      // ID 4
        Role::create(['name' => 'Inventory Manager']); // ID 5
        Role::create(['name' => 'Customer Support']);  // ID 6
        Role::create(['name' => 'Delivery Boy']);      // ID 7
    }
}
