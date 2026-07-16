<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Step 1: Drop the old string column
            $table->dropColumn('role');

            // Step 2: Add the new integer foreign key
            $table->foreignId('role_id')->nullable()->after('email')->constrained('roles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->string('role')->default('Admin');
        });
    }
};
