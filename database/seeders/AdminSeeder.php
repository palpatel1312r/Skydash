<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  public function run(): void
  {
    // Get role IDs
    $superadminRole = Role::where('name', 'Superadmin')->first();
    $adminRole = Role::where('name', 'Admin')->first();

    // Super Admin
    Admin::updateOrCreate(
      ['email' => 'superadmin@gmail.com'],
      [
        'name' => 'Super Admin',
        'password' => Hash::make('1234'),
        'role_id' => $superadminRole->id, // ✅ Use role_id
        'status' => 'Active',
      ]
    );

    // Regular Admin
    Admin::updateOrCreate(
      ['email' => 'admin@gmail.com'],
      [
        'name' => 'Admin',
        'password' => Hash::make('1234'),
        'role_id' => $adminRole->id, // ✅ Use role_id
        'status' => 'Active',
      ]
    );
  }
}
