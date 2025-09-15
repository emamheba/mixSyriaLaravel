<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandIdToSubCategoriesTable extends Migration
{
  public function up()
{
    Schema::table('sub_categories', function (Blueprint $table) {
        if (!Schema::hasColumn('sub_categories', 'brand_id')) {
            $table->unsignedBigInteger('brand_id')->after('id');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        }
    });
}

public function down()
{
    Schema::table('sub_categories', function (Blueprint $table) {
        $table->dropForeign(['brand_id']);
        $table->dropColumn('brand_id');
    });
}

}
