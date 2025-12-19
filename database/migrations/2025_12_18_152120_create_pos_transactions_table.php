<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('restrict');
            $table->foreignId('shift_id')->constrained('pos_shifts')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('customer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'card', 'ewallet', 'qris', 'split']);
            $table->json('payment_details')->nullable();
            $table->decimal('cash_received', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();
            $table->enum('status', ['completed', 'cancelled', 'refunded'])->default('completed');
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('cancel_reason')->nullable();
            $table->boolean('receipt_printed')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['outlet_id', 'created_at']);
            $table->index('shift_id');
            $table->index('transaction_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transactions');
    }
};
