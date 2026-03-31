<?php

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
            if (JWTAuth::setToken(request()->bearerToken() ?? request()->cookie('jwt_token'))->authenticate()) {
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            // Sin token válido
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('[LOGIN] Iniciando intento de login', ['user' => $request->strNombreUsuario]);

        // 1. VALIDACIÓN (Incluye Captcha)
        $request->validate([
            'strNombreUsuario'     => 'required|string',
            'strPwd'               => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'strNombreUsuario.required'     => 'El nombre de usuario es obligatorio.',
            'strPwd.required'               => 'La contraseña es obligatoria.',
            'g-recaptcha-response.required' => 'Debes completar el captcha.',
            'g-recaptcha-response.captcha'  => 'Captcha incorrecto. Inténtalo de nuevo.',
        ]);

        Log::info('[LOGIN] Step 1 OK - Validación y Captcha pasados');

        // 2. BUSCAR USUARIO
        $usuario = Usuario::where('strNombreUsuario', $request->strNombreUsuario)->first();

        if (!$usuario) {
            Log::warning('[LOGIN] Usuario no encontrado');
            return back()->withErrors(['login' => 'Usuario o contraseña incorrectos.'])->withInput();
        }

        // 3. VERIFICAR CONTRASEÑA
        if (!Hash::check($request->strPwd, $usuario->strPwd)) {
            Log::warning('[LOGIN] Contraseña incorrecta');
            return back()->withErrors(['login' => 'Usuario o contraseña incorrectos.'])->withInput();
        }

        // 4. VERIFICAR ESTADO
        if ($usuario->idEstadoUsuario != 1) {
            Log::warning('[LOGIN] Usuario inactivo');
            return back()->withErrors(['login' => 'El usuario no existe o su estado es inactivo.']);
        }

        // 5. GENERAR TOKEN
        try {
            $token = JWTAuth::fromUser($usuario);
            Log::info('[LOGIN] Step 5 OK - JWT generado');
        } catch (\Exception $e) {
            Log::error('[LOGIN] ERROR JWT', ['msg' => $e->getMessage()]);
            return back()->withErrors(['login' => 'Error interno de autenticación.']);
        }

        // 6. REDIRECCIÓN CON COOKIE SEGURA
        // Railway usa HTTPS en producción pero Laravel lo detecta como HTTP detrás del proxy.
        // Con trustProxies configurado en bootstrap/app.php, request()->secure() ya devuelve
        // true correctamente. Usamos SameSite=Lax para compatibilidad máxima.
        Log::info('[LOGIN] Redirigiendo al dashboard');

        return redirect()->route('dashboard')
            ->cookie(
                'jwt_token',
                $token,
                config('jwt.ttl', 60),
                '/',
                null,
                true,   // secure: siempre true (Railway siempre es HTTPS)
                true,   // httpOnly
                false,
                'Lax'
            );
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken() ?? $request->cookie('jwt_token');
            if ($token) JWTAuth::setToken($token)->invalidate();
        } catch (\Exception $e) { }

        return redirect()->route('login')
            ->withCookie(\Cookie::forget('jwt_token'))
            ->with('success', 'Sesión cerrada correctamente.');
    }
}