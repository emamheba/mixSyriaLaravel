<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the users table exists before trying to modify it
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('check_online_status')->nullable()->after('email_verified_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the users table exists before trying to modify it
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('check_online_status');
            });
        }
    }
};