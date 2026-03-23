<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\PermisoPerfil;
use App\Models\Usuario;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $idModulo, $accion = 'bitConsulta')
    {
        try {
            $user    = JWTAuth::parseToken()->authenticate();
            $usuario = Usuario::with('perfil')->find($user->id);

            if ($usuario->perfil->bitAdministrador) {
                return $next($request);
            }

            $permiso = PermisoPerfil::where('idPerfil', $usuario->idPerfil)
                ->where('idModulo', $idModulo)
                ->first();

            if (!$permiso || !$permiso->$accion) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Sin permisos para esta acción'], 403);
                }
                return redirect()->route('login')->with('error', 'No tienes permiso para acceder a este módulo.');
            }

        } catch (\Exception $e) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}