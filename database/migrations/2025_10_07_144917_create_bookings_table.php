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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('booking_type', ['registration', 'transfer', 'extension']);
            $table->enum('rental_type', ['daily', 'monthly']);
            $table->date('check_in_date');
            $table->date('expected_check_out_date');
            $table->date('actual_check_out_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'expired', 'terminated'])->default('pending');
            $table->text('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
