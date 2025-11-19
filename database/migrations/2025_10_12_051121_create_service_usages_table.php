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
        Schema::create('service_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('service_id')->constrained();
            $table->date('usage_date');
            $table->decimal('usage_amount', 10, 2);
<<<<<<< HEAD
            $table->decimal('unit_price', 10, 2);
=======
            $table->decimal('unit_price', 10, 0);
>>>>>>> upstream-main
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
            $table->unique(['room_id', 'service_id', 'usage_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_usages');
    }
};
