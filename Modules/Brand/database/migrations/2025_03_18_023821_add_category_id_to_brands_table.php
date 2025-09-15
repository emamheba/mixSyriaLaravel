<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryIdToBrandsTable extends Migration
{
    public function up()
    {
        // Check if both brands table and categories table exist
        if (Schema::hasTable('brands') && Schema::hasTable('categories')) {
            Schema::table('brands', function (Blueprint $table) {
                if (!Schema::hasColumn('brands', 'category_id')) {
                    // First add the column
                    $table->unsignedBigInteger('category_id')->nullable()->after('id');
                    
                    // Then add the foreign key constraint
                    $table->foreign('category_id')
                          ->references('id')
                          ->on('categories')
                          ->onDelete('cascade');
                }
            });
        }
    }

    public function down()
    {
        // Check if brands table exists
        if (Schema::hasTable('brands')) {
            Schema::table('brands', function (Blueprint $table) {
                // Check if the foreign key exists before trying to drop it
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys('brands');
                
                $foreignKeyExists = collect($foreignKeys)
                    ->contains(fn($fk) => in_array('category_id', $fk->getColumns()));
                
                if ($foreignKeyExists) {
                    $table->dropForeign(['category_id']);
                }
                
                // Drop the column if it exists
                if (Schema::hasColumn('brands', 'category_id')) {
                    $table->dropColumn('category_id');
                }
            });
        }
    }
}