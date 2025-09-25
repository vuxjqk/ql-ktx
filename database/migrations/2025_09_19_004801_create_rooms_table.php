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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->string('room_code', 20);
            $table->string('block', 1);
            $table->unsignedTinyInteger('floor');
            $table->enum('gender_type', ['male', 'female', 'mixed']);
            $table->decimal('price_per_month', 10, 0);
            $table->unsignedTinyInteger('capacity');
            $table->unsignedTinyInteger('current_occupancy')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'room_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
