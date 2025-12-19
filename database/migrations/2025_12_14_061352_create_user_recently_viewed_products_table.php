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
        Schema::create('user_recently_viewed_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('session_id')->nullable(); // For guest users
            $table->timestamps();
            
            // Prevent duplicates - one record per user/product or session/product
            $table->unique(['user_id', 'product_id'], 'user_product_unique');
            $table->unique(['session_id', 'product_id'], 'session_product_unique');
            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_recently_viewed_products');
    }
};
