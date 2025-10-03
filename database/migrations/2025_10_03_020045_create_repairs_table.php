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
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->index(); // Sinh viên báo
            $table->unsignedBigInteger('room_id')->index();
            $table->unsignedBigInteger('assigned_to')->nullable();

            $table->enum('type', ['electric', 'water', 'furniture', 'other'])->index();
            $table->text('description');
            $table->string('photo_url')->nullable(); // Upload ảnh
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open')->index();
            $table->text('notes')->nullable(); // Tiến độ
            $table->timestamp('reported_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Foreign keys - đặt tên rõ ràng để tránh trùng
            $table->foreign('user_id', 'fk_repairs_user')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('room_id', 'fk_repairs_room')
                ->references('id')->on('rooms')
                ->cascadeOnDelete();

            $table->foreign('assigned_to', 'fk_repairs_assigned_to')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
