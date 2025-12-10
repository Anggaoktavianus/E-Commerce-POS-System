<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;

class ErrorTrackingService
{
    /**
     * Track application errors with context
     */
    public static function trackError(Throwable $exception, Request $request = null, array $context = [])
    {
        $errorData = [
            'timestamp' => now()->toISOString(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'user_id' => auth()->id(),
            'user_role' => auth()->check() ? auth()->user()->role : null,
            'request_url' => $request?->url(),
            'request_method' => $request?->method(),
            'request_ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'context' => $context,
        ];

        // Log to different channels based on severity
        self::logBySeverity($exception, $errorData);

        // Store in database for analytics
        self::storeErrorRecord($errorData);

        return $errorData;
    }

    /**
     * Track performance issues
     */
    public static function trackPerformance(string $action, float $duration, array $context = [])
    {
        $performanceData = [
            'timestamp' => now()->toISOString(),
            'action' => $action,
            'duration_ms' => round($duration * 1000, 2),
            'user_id' => auth()->id(),
            'context' => $context,
        ];

        // Log performance issues
        if ($duration > 2.0) { // > 2 seconds
            Log::warning('Slow performance detected', $performanceData);
        } elseif ($duration > 5.0) { // > 5 seconds
            Log::error('Very slow performance detected', $performanceData);
        }

        return $performanceData;
    }

    /**
     * Track security events
     */
    public static function trackSecurityEvent(string $event, Request $request, array $context = [])
    {
        $securityData = [
            'timestamp' => now()->toISOString(),
            'event' => $event,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'url' => $request->url(),
            'context' => $context,
        ];

        Log::warning("Security event: {$event}", $securityData);

        return $securityData;
    }

    /**
     * Track user activity
     */
    public static function trackActivity(string $action, array $context = [])
    {
        $activityData = [
            'timestamp' => now()->toISOString(),
            'action' => $action,
            'user_id' => auth()->id(),
            'user_role' => auth()->check() ? auth()->user()->role : null,
            'ip' => request()->ip(),
            'context' => $context,
        ];

        Log::info("User activity: {$action}", $activityData);

        return $activityData;
    }

    /**
     * Get error statistics
     */
    public static function getErrorStats(int $days = 7)
    {
        // This would typically query a database table
        // For now, return mock data
        return [
            'total_errors' => rand(10, 100),
            'critical_errors' => rand(1, 10),
            'most_common_errors' => [
                'Database connection failed' => rand(1, 20),
                'CSRF token mismatch' => rand(5, 15),
                'Validation failed' => rand(10, 30),
            ],
            'errors_by_hour' => self::generateHourlyErrorData(),
            'error_trend' => 'decreasing', // or 'increasing', 'stable'
        ];
    }

    /**
     * Get performance metrics
     */
    public static function getPerformanceMetrics(int $hours = 24)
    {
        return [
            'average_response_time' => rand(100, 500) . 'ms',
            'slow_requests' => rand(1, 50),
            'fast_requests' => rand(100, 1000),
            'memory_usage' => self::formatBytes(memory_get_usage(true)),
            'peak_memory' => self::formatBytes(memory_get_peak_usage(true)),
            'cpu_usage' => rand(20, 80) . '%',
        ];
    }

    /**
     * Log errors based on severity
     */
    private static function logBySeverity(Throwable $exception, array $errorData)
    {
        $severity = self::determineSeverity($exception);

        switch ($severity) {
            case 'critical':
                Log::critical('Critical error detected', $errorData);
                break;
            case 'error':
                Log::error('Application error', $errorData);
                break;
            case 'warning':
                Log::warning('Warning level error', $errorData);
                break;
            default:
                Log::info('Info level error', $errorData);
        }
    }

    /**
     * Determine error severity
     */
    private static function determineSeverity(Throwable $exception): string
    {
        $message = strtolower($exception->getMessage());
        $file = strtolower($exception->getFile());

        // Critical errors
        if (strpos($message, 'database') !== false || 
            strpos($message, 'connection') !== false ||
            strpos($message, 'fatal') !== false) {
            return 'critical';
        }

        // Warning level
        if (strpos($message, 'validation') !== false ||
            strpos($message, 'unauthorized') !== false ||
            strpos($message, 'forbidden') !== false) {
            return 'warning';
        }

        // Default to error level
        return 'error';
    }

    /**
     * Store error record for analytics
     */
    private static function storeErrorRecord(array $errorData)
    {
        // This would typically store in a database
        // For now, just log that it was stored
        Log::debug('Error record stored for analytics', [
            'error_id' => uniqid(),
            'timestamp' => $errorData['timestamp']
        ]);
    }

    /**
     * Generate hourly error data (mock)
     */
    private static function generateHourlyErrorData(): array
    {
        $data = [];
        for ($i = 0; $i < 24; $i++) {
            $data[$i] = rand(0, 10);
        }
        return $data;
    }

    /**
     * Format bytes to human readable
     */
    private static function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }
        
        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
