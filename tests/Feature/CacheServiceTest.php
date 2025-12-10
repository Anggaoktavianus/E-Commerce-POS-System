<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\CacheService;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test products caching
     */
    public function test_products_caching(): void
    {
        // Clear cache first
        Cache::flush();
        
        // First call should cache the data
        $products1 = CacheService::cacheProducts();
        
        // Second call should return cached data
        $products2 = CacheService::cacheProducts();
        
        $this->assertEquals($products1, $products2);
        $this->assertTrue(Cache::has('products_all'));
    }

    /**
     * Test locations caching
     */
    public function test_locations_caching(): void
    {
        // Clear cache first
        Cache::flush();
        
        // Test provinsis caching
        $provinsis1 = CacheService::cacheLocations('provinsis');
        $provinsis2 = CacheService::cacheLocations('provinsis');
        
        $this->assertEquals($provinsis1, $provinsis2);
        $this->assertTrue(Cache::has('locations_provinsis'));
    }

    /**
     * Test user stats caching
     */
    public function test_user_stats_caching(): void
    {
        // Create user
        $user = User::factory()->create();
        
        // Clear cache first
        Cache::flush();
        
        // First call should cache the data
        $stats1 = CacheService::cacheUserStats($user->id);
        
        // Second call should return cached data
        $stats2 = CacheService::cacheUserStats($user->id);
        
        $this->assertEquals($stats1, $stats2);
        $this->assertTrue(Cache::has("user_stats_{$user->id}"));
        
        // Verify structure
        $this->assertArrayHasKey('total_orders', $stats1);
        $this->assertArrayHasKey('pending_orders', $stats1);
        $this->assertArrayHasKey('completed_orders', $stats1);
        $this->assertArrayHasKey('total_spent', $stats1);
    }

    /**
     * Test cache clearing
     */
    public function test_cache_clearing(): void
    {
        // Set some cache
        Cache::put('test_key', 'test_value', 3600);
        Cache::put('products_test', 'products_value', 3600);
        
        $this->assertTrue(Cache::has('test_key'));
        $this->assertTrue(Cache::has('products_test'));
        
        // Clear specific pattern
        CacheService::clearCache('products');
        
        $this->assertFalse(Cache::has('products_test'));
        $this->assertTrue(Cache::has('test_key')); // Should remain
        
        // Clear all
        CacheService::clearCache();
        
        $this->assertFalse(Cache::has('test_key'));
    }

    /**
     * Test cache warm up
     */
    public function test_cache_warm_up(): void
    {
        // Clear cache first
        Cache::flush();
        
        // Warm up cache
        $result = CacheService::warmUpCache();
        
        $this->assertTrue($result);
        $this->assertTrue(Cache::has('site_settings'));
        $this->assertTrue(Cache::has('locations_provinsis'));
        $this->assertTrue(Cache::has('locations_kabkotas'));
    }

    /**
     * Test cache TTL (time to live)
     */
    public function test_cache_ttl(): void
    {
        // Cache with short TTL for testing
        Cache::put('test_ttl', 'value', 1); // 1 second
        
        $this->assertTrue(Cache::has('test_ttl'));
        
        // Wait for cache to expire (simulate)
        sleep(2);
        
        // Cache should be expired
        $this->assertFalse(Cache::has('test_ttl'));
    }

    /**
     * Test cache performance
     */
    public function test_cache_performance(): void
    {
        // Simulate database query
        $dbStart = microtime(true);
        $products = CacheService::cacheProducts();
        $dbTime = microtime(true) - $dbStart;
        
        // Second call should be faster (from cache)
        $cacheStart = microtime(true);
        $productsCached = CacheService::cacheProducts();
        $cacheTime = microtime(true) - $cacheStart;
        
        $this->assertEquals($products, $productsCached);
        $this->assertLessThan($dbTime, $cacheTime); // Cache should be faster
    }
}
