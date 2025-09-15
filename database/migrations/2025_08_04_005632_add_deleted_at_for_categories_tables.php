<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });

        Schema::table('child_categories', function (Blueprint $table) {
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down()
    {
        Schema::table('catigories', function (Blueprint $table) {
            $table->dropColumn([
                'deleted_at',
            ]);
        });

        Schema::table('sub_categories', function (Blueprint $table) {
            $table->dropColumn([
                'deleted_at',
            ]);
        });

        Schema::table('child_categories', function (Blueprint $table) {
            $table->dropColumn([
                'deleted_at',
            ]);
        });
    }
};
