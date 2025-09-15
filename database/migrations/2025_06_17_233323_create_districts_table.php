<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->id(); 
            
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('city_id');
            
            $table->string('district', 255);
            $table->tinyInteger('status')->default(1)->comment('0=inactive 1=active');
            
            $table->timestamps(); 
            
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};