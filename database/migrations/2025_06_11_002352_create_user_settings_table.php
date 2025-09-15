<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->boolean('show_phone_to_buyers')->default(true);
            $table->boolean('enable_location_tracking')->default(true);
            $table->boolean('share_usage_data')->default(false);
            
            $table->boolean('enable_all_notifications')->default(true);
            $table->boolean('new_message_notifications')->default(true);
            $table->boolean('listing_comment_notifications')->default(true);
            $table->boolean('weekly_email_summary')->default(false);
            $table->boolean('email_matching_listings')->default(false);
            $table->boolean('email_offers_promotions')->default(false);
            $table->boolean('sms_notifications')->default(false);
            
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->enum('language', ['ar', 'en'])->default('ar');
            $table->boolean('show_nearby_listings')->default(true);
            $table->boolean('show_currency_rates')->default(false);
            $table->boolean('enable_image_caching')->default(true);
            $table->boolean('disable_listing_comments')->default(false);
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
};