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
        Schema::create('listing_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_package_id')->constrained()->onDelete('cascade');

            $table->enum('payment_method', ['bank_transfer', 'stripe']);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'requires_action'])->default('pending');
            $table->string('transaction_id')->nullable()->comment('Stripe charge ID or bank transfer ref');
            $table->string('bank_transfer_proof_path')->nullable()->comment('Path to uploaded bank transfer proof image');
            $table->timestamp('payment_confirmed_at')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('amount_paid', 8, 2); // Could be different from package price if discounts applied

            $table->text('admin_notes')->nullable(); // For admin to add notes about bank transfer verification

            $table->timestamps();
            $table->softDeletes(); // In case you want to soft delete promotion history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_promotions');
    }
};
