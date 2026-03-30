<?php
// ============================================================
// ARCHIVO: bootstrap/app.php  (Laravel 11)
// ============================================================

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Registrar los alias usados en web.php
        $middleware->alias([
            'jwt'        => \App\Http\Middleware\JwtMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // 403 → redirigir al login con mensaje de error
        $exceptions->renderable(function (
            \Symfony\Component\HttpKernel\Exception\HttpException $e,
            \Illuminate\Http\Request $request
        ) {
            if ($e->getStatusCode() === 403) {
                return redirect()->route('login')
                    ->with('error', 'No tienes permisos para acceder a ese módulo.');
            }
        });

        // 404 → vista personalizada
        $exceptions->renderable(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            \Illuminate\Http\Request $request
        ) {
            return response()->view('errors.404', [], 404);
        });

        // 500 → vista personalizada
        $exceptions->renderable(function (
            \Throwable $e,
            \Illuminate\Http\Request $request
        ) {
            if (app()->environment('production')) {
                return response()->view('errors.500', [], 500);
            }
        });

    })->create();


/*
|--------------------------------------------------------------------------
| IMPORTANTE: Si usas Laravel 10, en lugar de este archivo edita:
| app/Http/Kernel.php → array $routeMiddleware:
|
|   'jwt'        => \App\Http\Middleware\JwtMiddleware::class,
|   'permission' => \App\Http\Middleware\CheckPermission::class,
|--------------------------------------------------------------------------
|
| COMANDOS a ejecutar después de instalar/actualizar:
|
|   php artisan storage:link          # para servir imágenes de usuarios
|   php artisan config:clear
|   php artisan route:clear
|   php artisan view:clear
|   php artisan cache:clear
|   php artisan jwt:secret            # si no existe JWT_SECRET en .env
|
|--------------------------------------------------------------------------
*/