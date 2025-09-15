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
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('membership_type_id');
            $table->string('title');
            $table->string('image')->nullable();
            $table->double('price')->default(0);
            $table->integer('listing_limit')->default(0);
            $table->integer('gallery_images')->default(0);
            $table->integer('featured_listing')->default(0);
            $table->boolean('enquiry_form')->default(0);
            $table->boolean('business_hour')->default(0);
            $table->boolean('membership_badge')->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
