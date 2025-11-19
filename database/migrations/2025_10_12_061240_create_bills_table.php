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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_code')->unique();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('booking_id')->constrained();
            $table->decimal('total_amount', 10, 0);
<<<<<<< HEAD
            $table->enum('status', ['unpaid', 'paid', 'partial', 'cancelled'])->default('unpaid');
=======
            $table->enum('status', ['unpaid', 'paid', 'partial', 'cancelled', 'refunded'])->default('unpaid');
>>>>>>> upstream-main
            $table->date('due_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_monthly_bill')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
