<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Auto-detect if running in subdirectory (local development)
        // In production, APP_URL handles this automatically
        if (app()->environment('local')) {
            $request = request();
            if ($request) {
                // Debug logging
                \Log::info('Request URI: ' . $request->getRequestUri());
                \Log::info('Base Path: ' . $request->getBasePath());
                \Log::info('Path Info: ' . $request->getPathInfo());
                \Log::info('APP_URL: ' . config('app.url'));
                
                if (strpos($request->getRequestUri(), '/tajtrainerv2/public') === 0) {
                    URL::forceRootUrl(config('app.url'));
                    \Log::info('Forcing root URL to: ' . config('app.url'));
                }
            }
        }
    }
}
