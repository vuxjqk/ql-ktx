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
            $table->string('vnp_transaction_no')->nullable();
            $table->unsignedBigInteger('vnp_amount');
            $table->string('vnp_bank_code')->nullable();
            $table->string('vnp_bank_tran_no')->nullable();
            $table->string('vnp_card_type')->nullable();
            $table->string('vnp_order_info')->nullable();
            $table->string('vnp_response_code');
            $table->string('vnp_transaction_status');
            $table->string('vnp_pay_date')->nullable();
            $table->string('vnp_txn_ref');
            $table->string('vnp_secure_hash');
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
