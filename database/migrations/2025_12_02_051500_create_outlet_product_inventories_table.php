<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlet_product_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // Optional per-outlet stock; null means not tracked at outlet level (use store/product stock)
            $table->integer('stock')->nullable();
            // Optional price override per outlet
            $table->decimal('price_override', 12, 2)->nullable();
            $table->enum('status', ['available', 'unavailable', 'preorder'])->default('available');
            $table->timestamps();

            $table->unique(['outlet_id', 'product_id'], 'outlet_product_unique');
            $table->index(['product_id']);
            $table->index(['outlet_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlet_product_inventories');
    }
};
