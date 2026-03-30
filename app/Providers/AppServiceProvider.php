<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    public function boot(UrlGenerator $url): void
    {
        // Forzar HTTPS en producción
        if (env('APP_ENV') === 'production') {
            $url->forceScheme('https');
        }
    }
}