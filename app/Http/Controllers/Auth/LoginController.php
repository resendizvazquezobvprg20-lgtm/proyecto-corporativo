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
        // NO intentar leer el token aquí.
        // Si el usuario ya está autenticado y navega a /login,
        // simplemente mostramos el formulario — no hay riesgo de loop.
        return view('auth.login');
    }

    public function login(Request $request)
    {
        Log::info('[LOGIN] Inicio para: ' . $request->strNombreUsuario);

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

        $usuario = Usuario::where('strNombreUsuario', $request->strNombreUsuario)->first();

        if (!$usuario || !Hash::check($request->strPwd, $usuario->strPwd)) {
            Log::warning('[LOGIN] Credenciales inválidas');
            return back()->withErrors(['login' => 'Usuario o contraseña incorrectos.'])->withInput();
        }

        if ($usuario->idEstadoUsuario != 1) {
            return back()->withErrors(['login' => 'El usuario no existe o su estado es inactivo.']);
        }

        try {
            $token = JWTAuth::fromUser($usuario);
            Log::info('[LOGIN] Token OK, len=' . strlen($token));
        } catch (\Exception $e) {
            Log::error('[LOGIN] JWT error: ' . $e->getMessage());
            return back()->withErrors(['login' => 'Error interno de autenticación.']);
        }

        $ttl = config('jwt.ttl', 60);

        // secure:false → Railway termina SSL en el proxy externo,
        // el PHP ve HTTP interno → el browser no enviaría una cookie secure.
        return redirect()->route('dashboard')
            ->cookie('jwt_token', $token, $ttl, '/', null, false, true, false, 'Lax');
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->cookie('jwt_token');
            if ($token) JWTAuth::setToken($token)->invalidate();
        } catch (\Exception $e) { }

        return redirect()->route('login')
            ->withCookie(\Cookie::forget('jwt_token'))
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
