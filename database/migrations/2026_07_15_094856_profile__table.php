<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProfileTable extends Migration
{
    public function up()
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'profile_image')) {
                $table->string('profile_image')->nullable()->after('email');
            }
            // Add any other columns here with similar checks
        });
    }

    public function down()
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
}
