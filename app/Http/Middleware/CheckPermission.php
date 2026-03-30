<?php
// ============================================================
// ARCHIVO: app/Http/Middleware/CheckPermission.php
// ============================================================
// NOTA: El alias en web.php es 'permission', NO 'check.permission'
// Asegúrate de que en bootstrap/app.php o Kernel.php esté:
//   'permission' => \App\Http\Middleware\CheckPermission::class,
// ============================================================

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
     *
     * Uso en rutas: ->middleware('permission:ID_MODULO,CAMPO_PERMISO')
     * Ejemplo:      ->middleware('permission:1,bitConsulta')
     */
    public function handle(Request $request, Closure $next, $idModulo, $accion = 'bitConsulta')
    {
        try {
            // Intentar obtener el token desde cookie si no viene en el header
            if (!$request->bearerToken() && $request->cookie('jwt_token')) {
                JWTAuth::setToken($request->cookie('jwt_token'));
            }

            $user    = JWTAuth::parseToken()->authenticate();
            $usuario = Usuario::with('perfil')->find($user->id);

            if (!$usuario) {
                return $this->redirectOrJson($request, 'Usuario no encontrado.');
            }

            // Los administradores tienen acceso total
            if ($usuario->perfil?->bitAdministrador) {
                return $next($request);
            }

            // Verificar permiso específico
            $permiso = PermisoPerfil::where('idPerfil', $usuario->idPerfil)
                ->where('idModulo', $idModulo)
                ->first();

            if (!$permiso || !$permiso->$accion) {
                return $this->redirectOrJson(
                    $request,
                    'No tienes permiso para realizar esta acción.',
                    403
                );
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return $this->redirectOrJson($request, 'Sesión expirada. Inicia sesión nuevamente.');
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

        $flashKey = $code === 403 ? 'error' : 'error';
        return redirect()->route('login')->with($flashKey, $message);
    }
}