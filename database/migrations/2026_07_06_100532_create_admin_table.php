<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email', 191)->unique();

            // ✅ Added columns from superadmins table
            $table->string('profile_image')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // ✅ Unified role and status
            $table->string('role')->default('Admin'); // Admin or Superadmin
            $table->string('status')->default('Active');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
