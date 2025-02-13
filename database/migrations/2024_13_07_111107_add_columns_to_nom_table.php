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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('frequency')->nullable(); // Frequency: day, week, month
            $table->timestamp('next_scheduled_at')->nullable(); // Next scheduled transfer date
            $table->boolean('is_scheduled')->default(false); // Flag to check if it's a scheduled transfer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['frequency', 'next_scheduled_at', 'is_scheduled']);
        });
    }
};
