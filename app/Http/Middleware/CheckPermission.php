<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PermisoPerfil;
use App\Models\Usuario;

class CheckPermission
{
    /**
     * Verifica que el usuario tenga el permiso requerido para el módulo.
     * Uso: ->middleware('permission:ID_MODULO,CAMPO_PERMISO')
     */
    public function handle(Request $request, Closure $next, $idModulo, $accion = 'bitConsulta')
    {
        try {
           $rawCookie = $_COOKIE['jwt_token'] ?? null;
$token = $request->bearerToken() ?? $rawCookie;

            if (!$token) {
                return $this->redirectOrJson($request, 'No autenticado.');
            }

            $user    = JWTAuth::setToken($token)->authenticate();
            $usuario = Usuario::with('perfil')->find($user->id);

            if (!$usuario) {
                return $this->redirectOrJson($request, 'Usuario no encontrado.');
            }

            // Administradores tienen acceso total
            if ($usuario->perfil?->bitAdministrador) {
                return $next($request);
            }

            // Verificar permiso específico
            $permiso = PermisoPerfil::where('idPerfil', $usuario->idPerfil)
                ->where('idModulo', $idModulo)
                ->first();

            if (!$permiso || !$permiso->$accion) {
                return $this->redirectOrJson($request, 'No tienes permiso para esta acción.', 403);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->redirectOrJson($request, 'Sesión expirada.');
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return $this->redirectOrJson($request, 'Token inválido.');
        } catch (\Exception $e) {
            return $this->redirectOrJson($request, 'No autenticado.');
        }

        return $next($request);
    }

    private function redirectOrJson(Request $request, string $message, int $code = 401)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['error' => $message], $code);
        }
        return redirect()->route('login')->with('error', $message);
    }
}
