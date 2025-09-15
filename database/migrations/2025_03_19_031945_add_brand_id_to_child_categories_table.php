<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandIdToChildCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('child_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('child_categories', 'brand_id')) {
                $table->unsignedBigInteger('brand_id')->after('sub_category_id');
                $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            }
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('child_categories', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn('brand_id');
        });
    }
}
