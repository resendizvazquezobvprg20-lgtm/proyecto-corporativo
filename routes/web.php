<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Security\ModuloController;
use App\Http\Controllers\Security\PermisoPerfilController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Main\Principal1Controller;
use App\Http\Controllers\Main\Principal2Controller;
use App\Http\Controllers\DashboardController;

// ── Autenticación ─────────────────────────────────────────────────────
Route::get('/',       [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login',  [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ── Vistas (públicas - el JS verifica el token al cargar) ─────────────
// El middleware de autenticación lo maneja JavaScript con localStorage
Route::get('/dashboard',              [DashboardController::class, 'index'])->name('dashboard');
Route::get('/seguridad/perfil',       [PerfilController::class, 'index'])->name('perfil.index');
Route::get('/seguridad/modulo',       [ModuloController::class, 'index'])->name('modulo.index');
Route::get('/seguridad/permisos-perfil', [PermisoPerfilController::class, 'index'])->name('permiso.index');
Route::get('/seguridad/usuario',      [UsuarioController::class, 'index'])->name('usuario.index');
Route::get('/principal1/sub1',        [Principal1Controller::class, 'sub1'])->name('p1.sub1');
Route::get('/principal1/sub2',        [Principal1Controller::class, 'sub2'])->name('p1.sub2');
Route::get('/principal2/sub1',        [Principal2Controller::class, 'sub1'])->name('p2.sub1');
Route::get('/principal2/sub2',        [Principal2Controller::class, 'sub2'])->name('p2.sub2');

// ── API JSON (protegidas con JWT Bearer header) ────────────────────────
Route::middleware(['jwt'])->prefix('api')->group(function () {

    // Menú dinámico según permisos
    Route::get('/menu', [DashboardController::class, 'menuJson']);

    // Perfil CRUD
    Route::get('/perfil',       [PerfilController::class, 'list']);
    Route::post('/perfil',      [PerfilController::class, 'store']);
    Route::get('/perfil/{id}',  [PerfilController::class, 'show']);
    Route::put('/perfil/{id}',  [PerfilController::class, 'update']);
    Route::delete('/perfil/{id}', [PerfilController::class, 'destroy']);

    // Módulo CRUD
    Route::get('/modulo',         [ModuloController::class, 'list']);
    Route::post('/modulo',        [ModuloController::class, 'store']);
    Route::get('/modulo/{id}',    [ModuloController::class, 'show']);
    Route::put('/modulo/{id}',    [ModuloController::class, 'update']);
    Route::delete('/modulo/{id}', [ModuloController::class, 'destroy']);

    // Permisos Perfil
    Route::get('/permiso',         [PermisoPerfilController::class, 'list']);
    Route::post('/permiso',        [PermisoPerfilController::class, 'store']);
    Route::get('/permiso/{id}',    [PermisoPerfilController::class, 'show']);
    Route::put('/permiso/{id}',    [PermisoPerfilController::class, 'update']);
    Route::delete('/permiso/{id}', [PermisoPerfilController::class, 'destroy']);

    // Usuario CRUD
    Route::get('/usuario',          [UsuarioController::class, 'list']);
    Route::post('/usuario',         [UsuarioController::class, 'store']);
    Route::get('/usuario/{id}',     [UsuarioController::class, 'show']);
    Route::post('/usuario/{id}',    [UsuarioController::class, 'update']); // POST+_method por FormData
    Route::delete('/usuario/{id}',  [UsuarioController::class, 'destroy']);
});

// ── Error 404 personalizado ───────────────────────────────────────────
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
