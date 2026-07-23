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
        Role::create(['name' => 'Customer']);          
        Role::create(['name' => 'Shop Manager']);      
        Role::create(['name' => 'Inventory Manager']); 
        Role::create(['name' => 'Customer Support']);  
        Role::create(['name' => 'Delivery Boy']);      
    }
}
