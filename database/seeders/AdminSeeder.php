<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  public function run(): void
  {
    // Super Admin (role_id = 1)
    Admin::updateOrCreate(
      ['email' => 'superadmin@gmail.com'],
      [
        'name' => 'Super Admin',
        'password' => Hash::make('1234'),
        'role_id' => 1, // ✅ Superadmin
        'status' => 'Active',
      ]
    );

    // Regular Admin (role_id = 2)
    Admin::updateOrCreate(
      ['email' => 'admin@gmail.com'],
      [
        'name' => 'Admin',
        'password' => Hash::make('1234'),
        'role_id' => 2, // ✅ Admin
        'status' => 'Active',
      ]
    );
  }
}
