<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use App\Services\ErrorTrackingService;

class LoggingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureLogging();
        $this->registerErrorHandlers();
    }

    /**
     * Configure custom logging channels
     */
    private function configureLogging()
    {
        // Security events log
        Log::channel('single')->info('Logging service provider initialized');
        
        // Custom channel for security events
        $this->app->singleton('log.security', function ($app) {
            $logger = new Logger('security');
            $handler = new RotatingFileHandler(
                storage_path('logs/security.log'),
                30, // Keep 30 days
                Logger::INFO
            );
            $logger->pushHandler($handler);
            return $logger;
        });

        // Custom channel for performance
        $this->app->singleton('log.performance', function ($app) {
            $logger = new Logger('performance');
            $handler = new RotatingFileHandler(
                storage_path('logs/performance.log'),
                7, // Keep 7 days
                Logger::INFO
            );
            $logger->pushHandler($handler);
            return $logger;
        });

        // Custom channel for user activity
        $this->app->singleton('log.activity', function ($app) {
            $logger = new Logger('activity');
            $handler = new RotatingFileHandler(
                storage_path('logs/activity.log'),
                30, // Keep 30 days
                Logger::INFO
            );
            $logger->pushHandler($handler);
            return $logger;
        });
    }

    /**
     * Register enhanced error handlers
     */
    private function registerErrorHandlers()
    {
        // Handle exceptions
        $this->app->error(function (\Throwable $e, $request) {
            ErrorTrackingService::trackError($e, $request, [
                'environment' => app()->environment(),
                'locale' => app()->getLocale(),
            ]);
        });

        // Handle PHP errors
        set_error_handler(function ($severity, $message, $file, $line) {
            if (!(error_reporting() & $severity)) {
                return false;
            }

            $error = new \ErrorException($message, 0, $severity, $file, $line);
            ErrorTrackingService::trackError($error, request(), [
                'type' => 'php_error',
                'severity' => $severity,
            ]);

            return true;
        });

        // Handle fatal errors
        register_shutdown_function(function () {
            $error = error_get_last();
            if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
                $exception = new \ErrorException(
                    $error['message'],
                    0,
                    $error['type'],
                    $error['file'],
                    $error['line']
                );
                ErrorTrackingService::trackError($exception, null, [
                    'type' => 'fatal_error',
                ]);
            }
        });
    }
}
