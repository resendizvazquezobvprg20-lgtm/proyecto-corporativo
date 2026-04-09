<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // El token viene en el header Authorization: Bearer <token>
        // igual que en el proyecto Rust - localStorage + fetch con header
        $token = $request->bearerToken();

        if (!$token) {
            // Si es petición AJAX/JSON → 401
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'No autenticado.'], 401);
            }
            // Si es navegación normal → mostrar login (JS redirigirá)
            return redirect()->route('login');
        }

        try {
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Sesión inválida.'], 401);
                }
                return redirect()->route('login');
            }

            if ($user->idEstadoUsuario != 1) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Cuenta desactivada.'], 403);
                }
                return redirect()->route('login');
            }

        } catch (TokenExpiredException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Sesión expirada.'], 401);
            }
            return redirect()->route('login');
        } catch (TokenInvalidException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Token inválido.'], 401);
            }
            return redirect()->route('login');
        } catch (Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'No autenticado.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
