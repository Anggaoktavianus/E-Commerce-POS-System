<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_id')->unique();
            $table->string('order_id_midtrans');
            $table->string('payment_type');
            $table->string('payment_method')->nullable();
            $table->enum('status', ['pending', 'capture', 'settlement', 'deny', 'cancel', 'expire', 'refund']);
            $table->decimal('gross_amount', 10, 2);
            $table->string('currency', 3)->default('IDR');
            $table->json('transaction_details')->nullable();
            $table->json('va_numbers')->nullable();
            $table->json('bill_key')->nullable();
            $table->json('biller_code')->nullable();
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('transaction_id');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
};
