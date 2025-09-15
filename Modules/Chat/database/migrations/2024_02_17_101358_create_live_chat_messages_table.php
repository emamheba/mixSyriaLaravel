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
        Schema::create('live_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("live_chat_id");
            $table->integer("from_user")->comment("1 = user, 2 = admin");
            $table->longText("message")->nullable();
            $table->string("file")->nullable();
            $table->tinyInteger("is_seen")->default(0)->comment('0=unseen, 1=seen');
            $table->foreign("live_chat_id")->references("id")->on("live_chats")->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_chat_messages');
    }
};
