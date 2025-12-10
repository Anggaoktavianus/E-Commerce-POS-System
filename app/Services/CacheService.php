<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CacheService
{
    /**
     * Cache products with store relationship
     */
    public static function cacheProducts($storeId = null, $ttl = 3600)
    {
        $cacheKey = "products_" . ($storeId ?? 'all');
        
        return Cache::remember($cacheKey, $ttl, function () use ($storeId) {
            $query = DB::table('products')
                ->select('products.*', 'stores.name as store_name')
                ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
                ->where('products.is_active', 1);
                
            if ($storeId) {
                $query->where('products.store_id', $storeId);
            }
            
            return $query->get()->toArray();
        });
    }

    /**
     * Cache locations data
     */
    public static function cacheLocations($type = 'provinsis', $ttl = 86400)
    {
        $cacheKey = "locations_{$type}";
        
        return Cache::remember($cacheKey, $ttl, function () use ($type) {
            // Check if table exists first
            if (!Schema::hasTable("loc_{$type}")) {
                return [];
            }
            
            return DB::table("loc_{$type}")->orderBy('name')->get()->toArray();
        });
    }

    /**
     * Cache user dashboard stats
     */
    public static function cacheUserStats($userId, $ttl = 1800)
    {
        $cacheKey = "user_stats_{$userId}";
        
        return Cache::remember($cacheKey, $ttl, function () use ($userId) {
            return [
                'total_orders' => DB::table('orders')->where('user_id', $userId)->count(),
                'pending_orders' => DB::table('orders')->where('user_id', $userId)->where('status', 'pending')->count(),
                'completed_orders' => DB::table('orders')->where('user_id', $userId)->where('status', 'paid')->count(),
                'total_spent' => DB::table('orders')->where('user_id', $userId)->where('status', 'paid')->sum('total_amount'),
            ];
        });
    }

    /**
     * Cache store statistics
     */
    public static function cacheStoreStats($storeId, $ttl = 1800)
    {
        $cacheKey = "store_stats_{$storeId}";
        
        return Cache::remember($cacheKey, $ttl, function () use ($storeId) {
            return [
                'total_products' => DB::table('products')->where('store_id', $storeId)->where('is_active', 1)->count(),
                'total_orders' => DB::table('orders')->where('store_id', $storeId)->count(),
                'pending_orders' => DB::table('orders')->where('store_id', $storeId)->where('status', 'pending')->count(),
                'revenue' => DB::table('orders')->where('store_id', $storeId)->where('status', 'paid')->sum('total_amount'),
            ];
        });
    }

    /**
     * Cache site settings
     */
    public static function cacheSettings($ttl = 86400)
    {
        return Cache::remember('site_settings', $ttl, function () {
            $settings = DB::table('settings')->pluck('value', 'key')->toArray();
            
            // Add computed settings
            $settings['currency_symbol'] = 'IDR';
            $settings['decimal_places'] = 0;
            $settings['thousands_separator'] = '.';
            
            return $settings;
        });
    }

    /**
     * Clear specific cache
     */
    public static function clearCache($pattern = null)
    {
        if ($pattern) {
            // For database cache, we need to clear specific keys
            if (config('cache.default') === 'database') {
                $cacheKeys = DB::table('cache')
                    ->where('key', 'like', "%{$pattern}%")
                    ->pluck('key');
                    
                if ($cacheKeys->isNotEmpty()) {
                    DB::table('cache')->whereIn('key', $cacheKeys)->delete();
                }
            } else {
                // For Redis or other cache drivers
                try {
                    $redis = Cache::getRedis();
                    if ($redis) {
                        $cacheKeys = $redis->keys("*{$pattern}*");
                        if (!empty($cacheKeys)) {
                            $redis->del($cacheKeys);
                        }
                    }
                } catch (\Exception $e) {
                    // Fallback to flush all if Redis not available
                    Cache::flush();
                }
            }
        } else {
            // Clear all application cache
            Cache::flush();
        }
    }

    /**
     * Warm up cache on application start
     */
    public static function warmUpCache()
    {
        // Cache frequently accessed data
        self::cacheSettings();
        
        // Only cache locations if tables exist
        if (Schema::hasTable('loc_provinsis')) {
            self::cacheLocations('provinsis');
        }
        if (Schema::hasTable('loc_kabkotas')) {
            self::cacheLocations('kabkotas');
        }
        
        return true;
    }
}
