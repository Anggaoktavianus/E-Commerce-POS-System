<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('type', ['in', 'out', 'adjustment', 'restore'])->comment('in: stock masuk, out: stock keluar, adjustment: manual adjustment, restore: restore dari order cancel');
            $table->integer('quantity')->comment('Jumlah perubahan stok (positif untuk in/adjustment/restore, negatif untuk out)');
            $table->integer('old_stock')->comment('Stok sebelum perubahan');
            $table->integer('new_stock')->comment('Stok setelah perubahan');
            $table->string('reference_type')->nullable()->comment('Model class (Order, OrderItem, dll)');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID dari reference (order_id, dll)');
            $table->string('reference_number')->nullable()->comment('Nomor referensi (order_number, dll)');
            $table->text('notes')->nullable()->comment('Catatan/keterangan perubahan stok');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('User yang melakukan perubahan (untuk manual adjustment)');
            $table->timestamps();

            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
