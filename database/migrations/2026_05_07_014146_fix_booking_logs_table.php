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
        Schema::table('booking_logs', function (Blueprint $table) {
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action', ['confirmed', 'completed', 'cancelled', 'no_show']);
            $table->text('note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('booking_logs', function (Blueprint $table) {
            $table->dropForeign(['booking_id']);
            $table->dropForeign(['staff_id']);
            $table->dropColumn(['booking_id', 'staff_id', 'action', 'note']);
        });
    }
};
