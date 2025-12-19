<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheHelper
{
    /**
     * Flush all product-related cache
     * This includes cache from ShopController, HomeController, and other product caches
     */
    public static function flushProductCaches(): void
    {
        // Flush specific cache keys
        $specificKeys = [
            'home.products.fruits',
            'home.products.vegetables',
            'home.bestseller',
        ];
        
        foreach ($specificKeys as $key) {
            Cache::forget($key);
        }
        
        // Flush categories cache for all stores
        $stores = DB::table('stores')->pluck('id');
        foreach ($stores as $storeId) {
            Cache::forget("categories_store_{$storeId}");
        }
        
        // Flush shop products cache for common query patterns
        // Since cache keys use md5 hash of query params, we flush common combinations
        foreach ($stores as $storeId) {
            // Common sort options
            $commonSorts = ['latest', 'price_low', 'price_high', 'name'];
            foreach ($commonSorts as $sort) {
                $cacheKey = "products_store_{$storeId}_" . md5(json_encode(['sort' => $sort]));
                Cache::forget($cacheKey);
            }
            
            // Empty query (default shop page)
            $cacheKey = "products_store_{$storeId}_" . md5(json_encode([]));
            Cache::forget($cacheKey);
            
            // Common category filters (if any)
            $categories = DB::table('categories')->where('is_active', true)->pluck('slug');
            foreach ($categories as $categorySlug) {
                $cacheKey = "products_store_{$storeId}_" . md5(json_encode(['category' => $categorySlug]));
                Cache::forget($cacheKey);
            }
        }
        
        // Try to use cache tags if supported (Redis, Memcached)
        try {
            $store = Cache::getStore();
            if (method_exists($store, 'tags')) {
                Cache::tags(['products'])->flush();
            }
        } catch (\Exception $e) {
            // Tags not supported, continue with manual flush
        }
        
        \Log::info('All product caches flushed', ['method' => 'CacheHelper::flushProductCaches']);
    }
    
    /**
     * Flush cache for a specific store
     */
    public static function flushStoreProductCache($storeId): void
    {
        Cache::forget("categories_store_{$storeId}");
        
        // Flush common query patterns for this store
        $commonSorts = ['latest', 'price_low', 'price_high', 'name'];
        foreach ($commonSorts as $sort) {
            $cacheKey = "products_store_{$storeId}_" . md5(json_encode(['sort' => $sort]));
            Cache::forget($cacheKey);
        }
        
        $cacheKey = "products_store_{$storeId}_" . md5(json_encode([]));
        Cache::forget($cacheKey);
        
        \Log::info('Store product cache flushed', ['store_id' => $storeId]);
    }
}
