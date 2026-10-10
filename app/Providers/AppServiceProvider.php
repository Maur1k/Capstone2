<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Increase PHP limits for large file uploads (GLB files)
        // Note: post_max_size and upload_max_filesize can only be set in php.ini
        // These ini_set calls will help with memory and execution time
        @ini_set('memory_limit', '256M');
        
        // Set execution time: unlimited (0) for CLI commands, 300 seconds for web requests
        // This prevents "Maximum execution time exceeded" errors when running php artisan serve
        if (php_sapi_name() === 'cli') {
            @ini_set('max_execution_time', '0'); // Unlimited for CLI
            @ini_set('max_input_time', '0'); // Unlimited for CLI
        } else {
            @ini_set('max_execution_time', '300'); // 5 minutes for web requests
            @ini_set('max_input_time', '300'); // 5 minutes for web requests
        }
        
        // Admin login throttling (alias used in routes: throttle:admin-login)
        RateLimiter::for('admin-login', function (Request $request) {
            $key = 'admin-login:' . ($request->input('email') ?: $request->ip());
            return [
                Limit::perMinute(5)->by($key),
            ];
        });
    }
}
