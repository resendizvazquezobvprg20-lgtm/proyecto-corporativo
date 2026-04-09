<?php
// ============================================================
// ARCHIVO: routes/web.php — VERSIÓN FINAL CORREGIDA
// ============================================================
// Diferencias vs tu versión actual:
//   1. Se elimina la ruta '/' duplicada con name('login')
//   2. Se agrega la ruta POST /logout que faltaba
//   3. Alias de middleware corregido: 'jwt' y 'permission'
// ============================================================

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Security\ModuloController;
use App\Http\Controllers\Security\PermisoPerfilController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\Main\Principal1Controller;
use App\Http\Controllers\Main\Principal2Controller;
use App\Http\Controllers\DashboardController;

// ── Autenticación (públicas) ──────────────────────────────────────────
Route::get('/',       [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login',  [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');   // ← ESTA FALTABA

// ── Rutas protegidas (requieren JWT válido) ───────────────────────────
Route::middleware(['jwt'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Seguridad: Perfil (módulo id = 1) ──────────────────────────────
    Route::prefix('seguridad/perfil')->name('perfil.')->group(function () {
        Route::get('/',        [PerfilController::class, 'index'])  ->middleware('permission:1,bitConsulta')->name('index');
        Route::get('/list',    [PerfilController::class, 'list'])   ->middleware('permission:1,bitConsulta')->name('list');
        Route::post('/',       [PerfilController::class, 'store'])  ->middleware('permission:1,bitAgregar') ->name('store');
        Route::get('/{id}',    [PerfilController::class, 'show'])   ->middleware('permission:1,bitDetalle') ->name('show');
        Route::put('/{id}',    [PerfilController::class, 'update']) ->middleware('permission:1,bitEditar')  ->name('update');
        Route::delete('/{id}', [PerfilController::class, 'destroy'])->middleware('permission:1,bitEliminar')->name('destroy');
    });

    // ── Seguridad: Módulo (módulo id = 2) ──────────────────────────────
    Route::prefix('seguridad/modulo')->name('modulo.')->group(function () {
        Route::get('/',        [ModuloController::class, 'index'])  ->middleware('permission:2,bitConsulta')->name('index');
        Route::get('/list',    [ModuloController::class, 'list'])   ->middleware('permission:2,bitConsulta')->name('list');
        Route::post('/',       [ModuloController::class, 'store'])  ->middleware('permission:2,bitAgregar') ->name('store');
        Route::get('/{id}',    [ModuloController::class, 'show'])   ->middleware('permission:2,bitDetalle') ->name('show');
        Route::put('/{id}',    [ModuloController::class, 'update']) ->middleware('permission:2,bitEditar')  ->name('update');
        Route::delete('/{id}', [ModuloController::class, 'destroy'])->middleware('permission:2,bitEliminar')->name('destroy');
    });

    // ── Seguridad: Permisos Perfil (módulo id = 3) ─────────────────────
    Route::prefix('seguridad/permisos-perfil')->name('permiso.')->group(function () {
        Route::get('/',        [PermisoPerfilController::class, 'index'])  ->middleware('permission:3,bitConsulta')->name('index');
        Route::get('/list',    [PermisoPerfilController::class, 'list'])   ->middleware('permission:3,bitConsulta')->name('list');
        Route::post('/',       [PermisoPerfilController::class, 'store'])  ->middleware('permission:3,bitAgregar') ->name('store');
        Route::get('/{id}',    [PermisoPerfilController::class, 'show'])   ->middleware('permission:3,bitDetalle') ->name('show');
        Route::put('/{id}',    [PermisoPerfilController::class, 'update']) ->middleware('permission:3,bitEditar')  ->name('update');
        Route::delete('/{id}', [PermisoPerfilController::class, 'destroy'])->middleware('permission:3,bitEliminar')->name('destroy');
    });

    // ── Seguridad: Usuario (módulo id = 4) ─────────────────────────────
    // NOTA: update usa POST en lugar de PUT para soportar FormData con imagen (_method=PUT)
    Route::prefix('seguridad/usuario')->name('usuario.')->group(function () {
        Route::get('/',        [UsuarioController::class, 'index'])  ->middleware('permission:4,bitConsulta')->name('index');
        Route::get('/list',    [UsuarioController::class, 'list'])   ->middleware('permission:4,bitConsulta')->name('list');
        Route::post('/',       [UsuarioController::class, 'store'])  ->middleware('permission:4,bitAgregar') ->name('store');
        Route::get('/{id}',    [UsuarioController::class, 'show'])   ->middleware('permission:4,bitDetalle') ->name('show');
        Route::post('/{id}',   [UsuarioController::class, 'update']) ->middleware('permission:4,bitEditar')  ->name('update');  // POST + _method=PUT
        Route::delete('/{id}', [UsuarioController::class, 'destroy'])->middleware('permission:4,bitEliminar')->name('destroy');
    });

    // ── Principal 1 (módulos 5, 6) ─────────────────────────────────────
    Route::prefix('principal1')->name('p1.')->group(function () {
        Route::get('/sub1', [Principal1Controller::class, 'sub1'])->middleware('permission:5,bitConsulta')->name('sub1');
        Route::get('/sub2', [Principal1Controller::class, 'sub2'])->middleware('permission:6,bitConsulta')->name('sub2');
    });

    // ── Principal 2 (módulos 7, 8) ─────────────────────────────────────
    Route::prefix('principal2')->name('p2.')->group(function () {
        Route::get('/sub1', [Principal2Controller::class, 'sub1'])->middleware('permission:7,bitConsulta')->name('sub1');
        Route::get('/sub2', [Principal2Controller::class, 'sub2'])->middleware('permission:8,bitConsulta')->name('sub2');
    });
});

// ── 404 personalizado ─────────────────────────────────────────────────
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
// ── RUTA DE DIAGNÓSTICO TEMPORAL (eliminar después de resolver el problema) ──
Route::get('/debug-jwt', function (\Illuminate\Http\Request $request) {
    $info = [
        'jwt_secret_len'    => strlen(config('jwt.secret', '')),
        'jwt_secret_first8' => substr(config('jwt.secret', ''), 0, 8),
        'app_env'           => config('app.env'),
        'app_debug'         => config('app.debug'),
        'cookie_names'      => array_keys($request->cookies->all()),
        'has_jwt_cookie'    => $request->hasCookie('jwt_token'),
        'jwt_cookie_len'    => strlen($request->cookie('jwt_token', '')),
    ];

    if ($request->hasCookie('jwt_token')) {
        try {
            $token = $request->cookie('jwt_token');
            $user  = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
            $info['auth_result'] = $user ? 'OK: ' . $user->strNombreUsuario : 'null';
        } catch (\Exception $e) {
            $info['auth_error'] = get_class($e) . ': ' . $e->getMessage();
        }
    }

    return response()->json($info);
});
