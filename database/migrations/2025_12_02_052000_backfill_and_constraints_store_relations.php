<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill defaults and fix invalid references (NULL or non-existing)
        // Products
        DB::statement('UPDATE products p LEFT JOIN stores s ON p.store_id = s.id SET p.store_id = 1 WHERE p.store_id IS NULL OR s.id IS NULL');
        // Orders
        DB::statement('UPDATE orders o LEFT JOIN stores s ON o.store_id = s.id SET o.store_id = 1 WHERE o.store_id IS NULL OR s.id IS NULL');

        // Make NOT NULL using raw SQL to avoid doctrine/dbal dependency
        // Products.store_id
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE products MODIFY COLUMN store_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE orders MODIFY COLUMN store_id BIGINT UNSIGNED NOT NULL');
        } else {
            // Fallback using schema change if supported
            try {
                Schema::table('products', function (Blueprint $table) {
                    $table->unsignedBigInteger('store_id')->nullable(false)->change();
                });
                Schema::table('orders', function (Blueprint $table) {
                    $table->unsignedBigInteger('store_id')->nullable(false)->change();
                });
            } catch (\Throwable $e) {
                // ignore if not supported; assume MySQL branch handled
            }
        }

        // Add foreign keys and unique constraints
        Schema::table('products', function (Blueprint $table) {
            // FK products.store_id -> stores.id
            $table->foreign('store_id')->references('id')->on('stores')->cascadeOnDelete();
            // Composite unique on (store_id, sku) if sku exists
            if (Schema::hasColumn('products', 'sku')) {
                $table->unique(['store_id', 'sku'], 'products_store_sku_unique');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            // FK orders.store_id -> stores.id
            $table->foreign('store_id')->references('id')->on('stores')->cascadeOnDelete();
            // FK orders.outlet_id -> outlets.id (nullable)
            $table->foreign('outlet_id')->references('id')->on('outlets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Drop constraints
        Schema::table('orders', function (Blueprint $table) {
            try { $table->dropForeign(['outlet_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['store_id']); } catch (\Throwable $e) {}
        });
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'sku')) {
                try { $table->dropUnique('products_store_sku_unique'); } catch (\Throwable $e) {}
            }
            try { $table->dropForeign(['store_id']); } catch (\Throwable $e) {}
        });

        // Optionally revert NOT NULL back to NULLABLE to match previous migration state
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            try { DB::statement('ALTER TABLE products MODIFY COLUMN store_id BIGINT UNSIGNED NULL'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE orders MODIFY COLUMN store_id BIGINT UNSIGNED NULL'); } catch (\Throwable $e) {}
        }
    }
};
