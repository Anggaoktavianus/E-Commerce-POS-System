<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            $table->enum('payment_method', ['cash', 'card', 'ewallet', 'qris']);
            $table->decimal('amount', 15, 2);
            $table->json('payment_details')->nullable();
            $table->string('reference_number', 100)->nullable()->comment('Nomor referensi pembayaran');
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
