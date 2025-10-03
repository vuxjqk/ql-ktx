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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained();
            $table->string('transaction_no')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('bank_code')->nullable();
            $table->string('bank_tran_no')->nullable();
            $table->string('card_type')->nullable();
            $table->string('order_info')->nullable();
            $table->string('response_code');
            $table->string('transaction_status');
            $table->string('pay_date')->nullable();
            $table->string('txn_ref');
            $table->string('secure_hash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
