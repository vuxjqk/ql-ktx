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
            $table->string('room_code', 20);
            $table->foreignId('branch_id')->constrained();
            $table->string('block', 1);
            $table->unsignedTinyInteger('floor');
            $table->enum('gender_type', ['male', 'female', 'mixed']);
            $table->decimal('price', 10, 0);
            $table->unsignedTinyInteger('capacity');
            $table->unsignedTinyInteger('current_occupancy')->default(0);
            $table->boolean('is_active')->default(true);
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
