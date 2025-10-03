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
        Schema::create('violations', function (Blueprint $table) {
            $table->id();

            // Dùng unsignedBigInteger + index riêng
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('room_id')->nullable()->index();

            $table->enum('type', ['overdue', 'damage', 'rule_break', 'abandon'])->index();
            $table->text('description');
            $table->decimal('fine_amount', 10, 0)->nullable();
            $table->enum('status', ['pending', 'resolved'])->default('pending')->index();
            $table->timestamps();

            // Đặt tên foreign key rõ ràng
            $table->foreign('user_id', 'fk_violations_user')
                ->references('id')->on('users')->cascadeOnDelete();

            $table->foreign('room_id', 'fk_violations_room')
                ->references('id')->on('rooms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
