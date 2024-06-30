<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        RateLimiter::for('user', function (Request $request) {
            // return Limit::perMinute(2)->by($request->user()?->id ?: $request->ip());
            // return Limit::perMinute(100)->by($request->header('authorization'));
            return $request->header('authorization')
                ? Limit::perMinute(100)->by($request->header('authorization'))
                : Limit::perMinute(20)->by($request->ip());

        });
    }
}
 