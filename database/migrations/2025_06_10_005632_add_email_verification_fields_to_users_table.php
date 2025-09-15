<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verify_token_expires_at')->nullable()->after('email_verify_token');
            $table->integer('email_verification_attempts')->default(0)->after('email_verify_token_expires_at');
            $table->timestamp('last_verification_attempt_at')->nullable()->after('email_verification_attempts');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'email_verify_token_expires_at',
                'email_verification_attempts', 
                'last_verification_attempt_at'
            ]);
        });
    }
};