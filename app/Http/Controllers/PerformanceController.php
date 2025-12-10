<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\OptimizationService;

class PerformanceController extends Controller
{
    /**
     * Performance dashboard
     */
    public function dashboard()
    {
        $metrics = $this->getPerformanceMetrics();
        
        return response()->json([
            'performance_metrics' => $metrics,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get comprehensive performance metrics
     */
    private function getPerformanceMetrics()
    {
        return [
            'database' => $this->getDatabaseMetrics(),
            'cache' => $this->getCacheMetrics(),
            'queries' => $this->getQueryMetrics(),
            'memory' => $this->getMemoryMetrics(),
            'response_time' => $this->getResponseTimeMetrics(),
        ];
    }

    private function getDatabaseMetrics()
    {
        try {
            $connection = DB::connection();
            
            return [
                'connection_status' => $connection->getPdo() ? 'connected' : 'disconnected',
                'slow_queries' => $this->getSlowQueriesCount(),
                'total_queries' => count(DB::getQueryLog()),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'table_sizes' => $this->getTableSizes(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getCacheMetrics()
    {
        try {
            $redis = Cache::getRedis();
            
            return [
                'driver' => config('cache.default'),
                'redis_connected' => $redis ? true : false,
                'cache_keys_count' => $redis ? count($redis->keys('*')) : 0,
                'memory_usage' => $redis ? $redis->info('memory') : null,
                'hit_rate' => $this->calculateCacheHitRate(),
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    private function getQueryMetrics()
    {
        $queries = DB::getQueryLog();
        
        return [
            'total_queries' => count($queries),
            'slow_queries' => $this->identifySlowQueries($queries),
            'average_query_time' => $this->calculateAverageQueryTime($queries),
            'most_frequent_queries' => $this->getMostFrequentQueries($queries),
        ];
    }

    private function getMemoryMetrics()
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'usage_percentage' => $this->calculateMemoryUsagePercentage(),
        ];
    }

    private function getResponseTimeMetrics()
    {
        return [
            'average_response_time' => $this->getAverageResponseTime(),
            'slow_requests' => $this->getSlowRequestsCount(),
            'fast_requests' => $this->getFastRequestsCount(),
        ];
    }

    /**
     * Performance optimization actions
     */
    public function optimize(Request $request)
    {
        $action = $request->input('action');
        
        switch ($action) {
            case 'clear_cache':
                CacheService::clearCache();
                $result = 'Cache cleared successfully';
                break;
                
            case 'warm_cache':
                CacheService::warmUpCache();
                $result = 'Cache warmed up successfully';
                break;
                
            case 'optimize_database':
                OptimizationService::optimizeTables();
                $result = 'Database optimized successfully';
                break;
                
            case 'add_indexes':
                OptimizationService::addPerformanceIndexes();
                $result = 'Performance indexes added successfully';
                break;
                
            default:
                $result = 'Invalid optimization action';
        }
        
        return response()->json([
            'action' => $action,
            'result' => $result,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Helper methods for metrics calculation
     */
    private function getSlowQueriesCount()
    {
        // This would typically be implemented with database-specific queries
        return 0; // Placeholder
    }

    private function getCacheHitRate()
    {
        // Calculate cache hit rate from Redis info
        try {
            $redis = Cache::getRedis();
            $info = $redis->info('stats');
            
            if (isset($info['keyspace_hits']) && isset($info['keyspace_misses'])) {
                $total = $info['keyspace_hits'] + $info['keyspace_misses'];
                return $total > 0 ? ($info['keyspace_hits'] / $total) * 100 : 0;
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        return 0;
    }

    private function getTableSizes()
    {
        return [
            'orders' => $this->getTableSize('orders'),
            'products' => $this->getTableSize('products'),
            'users' => $this->getTableSize('users'),
        ];
    }

    private function getTableSize($table)
    {
        try {
            $result = DB::select("SELECT COUNT(*) as count FROM {$table}");
            return $result[0]->count ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function identifySlowQueries($queries)
    {
        $slowQueries = [];
        foreach ($queries as $query) {
            if (isset($query['time']) && $query['time'] > 1000) { // > 1 second
                $slowQueries[] = $query;
            }
        }
        return $slowQueries;
    }

    private function calculateAverageQueryTime($queries)
    {
        if (empty($queries)) return 0;
        
        $totalTime = array_sum(array_column($queries, 'time'));
        return $totalTime / count($queries);
    }

    private function getMostFrequentQueries($queries)
    {
        // Simple frequency analysis
        $frequency = [];
        foreach ($queries as $query) {
            $pattern = preg_replace('/\d+/', '?', $query['query']);
            $frequency[$pattern] = ($frequency[$pattern] ?? 0) + 1;
        }
        
        arsort($frequency);
        return array_slice($frequency, 0, 5, true);
    }

    private function calculateMemoryUsagePercentage()
    {
        $usage = memory_get_usage(true);
        $limit = $this->parseMemoryLimit(ini_get('memory_limit'));
        
        return $limit > 0 ? ($usage / $limit) * 100 : 0;
    }

    private function parseMemoryLimit($limit)
    {
        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);
        
        switch ($unit) {
            case 'g': return $value * 1024 * 1024 * 1024;
            case 'm': return $value * 1024 * 1024;
            case 'k': return $value * 1024;
            default: return (int) $limit;
        }
    }

    private function getAverageResponseTime()
    {
        // This would typically be implemented with application monitoring
        return 0; // Placeholder
    }

    private function getSlowRequestsCount()
    {
        // This would typically be implemented with application monitoring
        return 0; // Placeholder
    }

    private function getFastRequestsCount()
    {
        // This would typically be implemented with application monitoring
        return 0; // Placeholder
    }

    private function calculateCacheHitRate()
    {
        return $this->getCacheHitRate();
    }
}
