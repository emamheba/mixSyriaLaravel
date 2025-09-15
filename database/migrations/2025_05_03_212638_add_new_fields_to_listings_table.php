<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->enum('listing_type', ['sell', 'rent', 'job', 'service'])->default('sell')->after('condition');
            $table->timestamp('expire_at')->nullable();

            $table->index(['lat', 'lon']);
            $table->index('price');
        });
    }

    public function down()
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['listing_type', 'expire_at']);
        });
    }
};
