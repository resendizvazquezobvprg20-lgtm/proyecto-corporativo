<?php
// ============================================================
// ARCHIVO: app/Http/Controllers/Auth/LoginController.php
// ============================================================
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        // Si ya hay token válido, redirigir al dashboard
        try {
            if (JWTAuth::parseToken()->authenticate()) {
                return redirect()->route('dashboard');
            }
        } catch (\Exception $e) {
            // No hay token, continuar
        }

        return view('auth.login');
    }

    /**
     * Procesa el login con validación de captcha, usuario y contraseña
     */
    public function login(Request $request)
    {
        // Validar campos + captcha
        $request->validate([
            'strNombreUsuario' => 'required|string',
            'strPwd'           => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'strNombreUsuario.required' => 'El nombre de usuario es obligatorio.',
            'strPwd.required'           => 'La contraseña es obligatoria.',
            'g-recaptcha-response.required' => 'Debes completar el captcha.',
            'g-recaptcha-response.captcha'  => 'Captcha incorrecto. Inténtalo de nuevo.',
        ]);

        // Buscar usuario
        $usuario = Usuario::where('strNombreUsuario', $request->strNombreUsuario)->first();

        if (!$usuario || !Hash::check($request->strPwd, $usuario->strPwd)) {
            return back()->withErrors(['login' => 'Usuario o contraseña incorrectos.'])->withInput(['strNombreUsuario' => $request->strNombreUsuario]);
        }

        // Verificar estado activo
        if ($usuario->idEstadoUsuario != 1) {
            return back()->withErrors(['login' => 'El usuario no existe o su estado es inactivo.'])->withInput(['strNombreUsuario' => $request->strNombreUsuario]);
        }

        // Generar JWT
        $token = JWTAuth::fromUser($usuario);

        // Guardar token en cookie segura (httpOnly)
        return redirect()->route('dashboard')
            ->cookie('jwt_token', $token, config('jwt.ttl'), '/', null, false, true);
    }

    /**
     * Logout: invalida el token JWT
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::parseToken()->invalidate();
        } catch (\Exception $e) {
            // Token ya inválido
        }

        return redirect()->route('login')
            ->withCookie(\Cookie::forget('jwt_token'))
            ->with('success', 'Sesión cerrada correctamente.');
    }
}
