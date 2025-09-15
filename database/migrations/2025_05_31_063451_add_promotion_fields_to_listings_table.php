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
        Schema::table('listings', function (Blueprint $table) {
            if (!Schema::hasColumn('listings', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('lat');
            }
            $table->timestamp('promoted_until')->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            if (Schema::hasColumn('listings', 'is_featured') && !Schema::hasColumn('listings', 'promoted_until')) {
            } else if (Schema::hasColumn('listings', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            $table->dropColumn('promoted_until');
        });
    }
};
