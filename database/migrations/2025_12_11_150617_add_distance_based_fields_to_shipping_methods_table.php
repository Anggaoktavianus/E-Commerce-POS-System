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
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->decimal('price_per_km', 10, 0)->nullable()->after('max_distance_km')->comment('Tarif per kilometer untuk distance-based shipping');
            $table->boolean('is_distance_based')->default(false)->after('price_per_km')->comment('Flag untuk metode pengiriman berbasis jarak');
            $table->decimal('min_cost', 10, 0)->nullable()->after('price_per_km')->comment('Minimum cost untuk distance-based shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_methods', function (Blueprint $table) {
            $table->dropColumn(['price_per_km', 'is_distance_based', 'min_cost']);
        });
    }
};
