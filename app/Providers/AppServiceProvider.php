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
        // Admin login throttling (alias used in routes: throttle:admin-login)
        RateLimiter::for('admin-login', function (Request $request) {
            $key = 'admin-login:' . ($request->input('email') ?: $request->ip());
            return [
                Limit::perMinute(5)->by($key),
            ];
        });
    }
}
