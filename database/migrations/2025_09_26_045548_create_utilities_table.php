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
        Schema::create('utilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();
            $table->date('month');
            $table->decimal('electric_usage', 10, 2)->default(0);
            $table->decimal('water_usage', 10, 2)->default(0);
            $table->decimal('electric_cost', 10, 0)->default(0);
            $table->decimal('water_cost', 10, 0)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['room_id', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilities');
    }
};
