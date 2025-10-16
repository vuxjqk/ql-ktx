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
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('floor_number');
            $table->foreignId('branch_id')->constrained();
            $table->enum('gender_type', ['male', 'female', 'mixed'])->default('mixed');
            $table->timestamps();
            $table->unique(['floor_number', 'branch_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('floors');
    }
};
