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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // Thay vì foreignId()->constrained(), ta tách ra để đặt tên rõ ràng
            $table->unsignedBigInteger('user_id')->index();

            $table->string('title');
            $table->text('message');
            $table->enum('type', ['bill', 'repair', 'registration', 'system'])->index();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Đặt tên foreign key rõ ràng
            $table->foreign('user_id', 'fk_notifications_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
