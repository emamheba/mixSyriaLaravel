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
        Schema::create('user_memberships', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('membership_id');
            $table->double('price')->default(0);
            $table->bigInteger('initial_listing_limit')->default(0);
            $table->bigInteger('initial_gallery_images')->default(0);
            $table->bigInteger('initial_featured_listing')->default(0);
            $table->boolean('initial_enquiry_form')->default(0);
            $table->boolean('initial_business_hour')->default(0);
            $table->boolean('initial_membership_badge')->default(0);
            $table->bigInteger('listing_limit')->default(0);
            $table->bigInteger('gallery_images')->default(0);
            $table->bigInteger('featured_listing')->default(0);
            $table->boolean('enquiry_form')->default(0);
            $table->boolean('business_hour')->default(0);
            $table->boolean('membership_badge')->default(0);
            $table->string('payment_gateway')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('manual_payment_image')->nullable();
            $table->timestamp('expire_date')->nullable();
            $table->tinyInteger('status')->default(0);
            // Indexes
            $table->index('user_id');
            $table->index('membership_id');
            $table->index('expire_date');
            $table->index('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_memberships');
    }
};
