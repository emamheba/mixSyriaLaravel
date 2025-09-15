<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('type_id')->constrained('notification_types');
            $table->json('channels')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            
            $table->unique(['user_id', 'type_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_notification_settings');
    }
};