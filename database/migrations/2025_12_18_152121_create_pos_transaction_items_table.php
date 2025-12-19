<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pos_transaction_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('pos_transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('product_name', 255);
            $table->string('product_sku', 100)->nullable();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->integer('stock_before')->nullable()->comment('Stok sebelum transaksi');
            $table->integer('stock_after')->nullable()->comment('Stok setelah transaksi');
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pos_transaction_items');
    }
};
