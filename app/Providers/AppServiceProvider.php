<?php

namespace App\Providers;

use App\Listeners\NotifyAdminOfNewUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        Event::listen(
            Registered::class,
            NotifyAdminOfNewUser::class,
        );

        RateLimiter::for('ai_insight', function (Request $request) {
            // Increased to 100 per minute to support multiple users on same IP
            return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        });
    }
}
