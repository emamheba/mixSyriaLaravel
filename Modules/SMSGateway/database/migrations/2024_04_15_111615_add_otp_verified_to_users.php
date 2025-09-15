<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Check if the users table exists before trying to modify it
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('otp_verified')->default(0)->after('terms_condition');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('otp_verified');
            });
        }
    }
};