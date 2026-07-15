<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
  public function run(): void
  {
    $admins = [

      [
        'name' => 'Admin',
        'email' => 'admin@gmail.com',
        'password' => Hash::make('1234'),
      ],
    ];

    foreach ($admins as $admin) {
      Admin::updateOrCreate(
        ['email' => $admin['email']],
        $admin
      );
    }
  }
}
