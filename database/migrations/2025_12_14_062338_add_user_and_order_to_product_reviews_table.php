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
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('product_id')->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->nullable()->after('user_id')->constrained('orders')->nullOnDelete();
            $table->unique(['user_id', 'product_id', 'order_id'], 'unique_user_product_order_review');
            $table->index(['user_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['order_id']);
            $table->dropUnique('unique_user_product_order_review');
            $table->dropIndex(['user_id', 'product_id']);
            $table->dropColumn(['user_id', 'order_id']);
        });
    }
};
