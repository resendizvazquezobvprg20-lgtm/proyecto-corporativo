<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar HTTPS cuando está en producción (Railway)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}