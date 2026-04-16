<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usuario;

class LoginController extends Controller
{
    // Muestra el formulario de login (puro HTML, sin verificar token)
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // API JSON: recibe credenciales, devuelve token
    public function login(Request $request)
    {
        $request->validate([
            'strNombreUsuario'     => 'required|string',
            'strPwd'               => 'required|string',
            'g-recaptcha-response' => 'required|captcha',
        ], [
            'strNombreUsuario.required'     => 'El nombre de usuario es obligatorio.',
            'strPwd.required'               => 'La contraseña es obligatoria.',
            'g-recaptcha-response.required' => 'Debes completar el captcha.',
            'g-recaptcha-response.captcha'  => 'Captcha incorrecto.',
        ]);

        $usuario = Usuario::with('perfil')
            ->where('strNombreUsuario', $request->strNombreUsuario)
            ->first();

        if (!$usuario || !Hash::check($request->strPwd, $usuario->strPwd)) {
            return response()->json(['error' => 'Usuario o contraseña incorrectos.'], 401);
        }

        if ($usuario->idEstadoUsuario != 1) {
            return response()->json(['error' => 'El usuario no existe o su estado es inactivo.'], 403);
        }

        try {
            $token = JWTAuth::fromUser($usuario);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno de autenticación.'], 500);
        }

        return response()->json([
            'token'      => $token,
            'nombre'     => $usuario->strNombreUsuario,
            'perfil_id'  => $usuario->idPerfil,
            'imagen'     => $usuario->strImagen,
            'imagen_url' => $usuario->strImagen ? url('files/' . $usuario->strImagen) : null,
        ]);
    }

    // Logout: solo redirige a login (el token se borra en localStorage desde JS)
    public function logout()
    {
        return redirect()->route('login');
    }
}
