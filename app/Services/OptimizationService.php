<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class OptimizationService
{
    /**
     * Add database indexes for performance
     */
    public static function addPerformanceIndexes()
    {
        $indexes = [
            'orders' => [
                'idx_orders_status_created' => ['status', 'created_at'],
                'idx_orders_user_status' => ['user_id', 'status'],
                'idx_orders_store_status' => ['store_id', 'status'],
                'idx_orders_midtrans' => 'midtrans_order_id',
            ],
            'products' => [
                'idx_products_store_active' => ['store_id', 'is_active'],
                'idx_products_category_active' => ['category_id', 'is_active'],
                'idx_products_name' => 'name',
                'idx_products_price' => 'price',
            ],
            'users' => [
                'idx_users_role' => 'role',
                'idx_users_email' => 'email',
                'idx_users_verified' => 'is_verified',
            ],
            'order_items' => [
                'idx_order_items_order' => 'order_id',
                'idx_order_items_product' => 'product_id',
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($tableIndexes as $indexName => $columns) {
                if (!Schema::hasIndex($table, $indexName)) {
                    $columnList = is_array($columns) ? implode(',', $columns) : $columns;
                    DB::statement("CREATE INDEX {$indexName} ON {$table} ({$columnList})");
                }
            }
        }

        return true;
    }

    /**
     * Optimize slow queries
     */
    public static function optimizeQueries()
    {
        // Enable query logging to identify slow queries
        DB::enableQueryLog();

        return [
            'orders_with_relations' => self::optimizeOrdersQuery(),
            'products_with_stores' => self::optimizeProductsQuery(),
            'user_statistics' => self::optimizeUserStatsQuery(),
        ];
    }

    private static function optimizeOrdersQuery()
    {
        return Order::with(['user:id,name,email', 'store:id,name', 'items.product:id,name'])
            ->select(['id', 'order_number', 'user_id', 'store_id', 'total_amount', 'status', 'created_at'])
            ->latest()
            ->take(50)
            ->get();
    }

    private static function optimizeProductsQuery()
    {
        return Product::with(['store:id,name', 'category:id,name'])
            ->select(['id', 'name', 'price', 'store_id', 'category_id', 'is_active', 'created_at'])
            ->where('is_active', 1)
            ->latest()
            ->take(20)
            ->get();
    }

    private static function optimizeUserStatsQuery()
    {
        return User::select(['id', 'name', 'email', 'role', 'created_at'])
            ->withCount(['orders' => function ($query) {
                $query->where('status', 'paid');
            }])
            ->latest()
            ->take(100)
            ->get();
    }

    /**
     * Database table optimization
     */
    public static function optimizeTables()
    {
        $tables = ['orders', 'products', 'users', 'order_items', 'payment_transactions'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ANALYZE TABLE {$table}");
                DB::statement("OPTIMIZE TABLE {$table}");
            }
        }

        return true;
    }

    /**
     * Clear and reset query cache
     */
    public static function resetQueryCache()
    {
        DB::statement('RESET QUERY CACHE');
        Cache::flush();
        
        return true;
    }
}
