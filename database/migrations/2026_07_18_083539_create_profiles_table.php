<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            // ✅ POLYMORPHIC COLUMNS (Allows this table to link to both Admin and Customer)
            $table->string('profileable_type');
            $table->unsignedBigInteger('profileable_id');
            $table->index(['profileable_type', 'profileable_id']);

            // ✅ Profile specific fields
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
