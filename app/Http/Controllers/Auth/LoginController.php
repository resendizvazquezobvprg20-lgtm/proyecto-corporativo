<?php
// ============================================================
// ARCHIVO: app/Http/Controllers/Auth/LoginController.php
// VERSIÓN SIN CAPTCHA — solo para diagnosticar el error 500
// ============================================================
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            // Sin token válido, mostrar login
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validar solo usuario y contraseña (captcha deshabilitado para diagnóstico)
        $request->validate([
            'strNombreUsuario' => 'required|string',
            'strPwd'           => 'required|string',
        ], [
            'strNombreUsuario.required' => 'El nombre de usuario es obligatorio.',
            'strPwd.required'           => 'La contraseña es obligatoria.',
        ]);

        Log::info('[LOGIN] Step 1 OK', ['user' => $request->strNombreUsuario]);

        $usuario = Usuario::where('strNombreUsuario', $request->strNombreUsuario)->first();

        if (!$usuario) {
            Log::warning('[LOGIN] Usuario no encontrado');
            return back()
                ->withErrors(['login' => 'Usuario o contraseña incorrectos.'])
                ->withInput(['strNombreUsuario' => $request->strNombreUsuario]);
        }

        Log::info('[LOGIN] Step 2 OK', [
            'id'     => $usuario->id,
            'estado' => $usuario->idEstadoUsuario,
            'hash'   => str_starts_with($usuario->strPwd, '$2y$') ? 'bcrypt OK' : 'TEXTO PLANO',
        ]);

        if (!Hash::check($request->strPwd, $usuario->strPwd)) {
            Log::warning('[LOGIN] Contraseña incorrecta');
            return back()
                ->withErrors(['login' => 'Usuario o contraseña incorrectos.'])
                ->withInput(['strNombreUsuario' => $request->strNombreUsuario]);
        }

        Log::info('[LOGIN] Step 3 OK - contraseña correcta');

        if ($usuario->idEstadoUsuario != 1) {
            Log::warning('[LOGIN] Usuario inactivo');
            return back()
                ->withErrors(['login' => 'El usuario no existe o su estado es inactivo.'])
                ->withInput(['strNombreUsuario' => $request->strNombreUsuario]);
        }

        Log::info('[LOGIN] Step 4 OK - estado activo');

        try {
            $token = JWTAuth::fromUser($usuario);
            Log::info('[LOGIN] Step 5 OK - JWT generado');
        } catch (\Exception $e) {
            Log::error('[LOGIN] ERROR JWT', ['msg' => $e->getMessage()]);
            return back()->withErrors(['login' => 'Error interno. Verifica JWT_SECRET en Railway.']);
        }

        $isProduction = config('app.env') === 'production';
        Log::info('[LOGIN] Step 6 - redirigiendo al dashboard', ['secure_cookie' => $isProduction]);

        return redirect()->route('dashboard')
            ->cookie('jwt_token', $token, config('jwt.ttl', 60), '/', null, $isProduction, true);
    }

    public function logout(Request $request)
    {
        try {
            if (!$request->bearerToken() && $request->cookie('jwt_token')) {
                JWTAuth::setToken($request->cookie('jwt_token'));
            }
            JWTAuth::parseToken()->invalidate();
        } catch (\Exception $e) {
            // Token expirado, no importa
        }

        return redirect()->route('login')
            ->withCookie(\Cookie::forget('jwt_token'))
            ->with('success', 'Sesión cerrada correctamente.');
    }
}