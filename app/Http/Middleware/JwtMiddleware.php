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
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Sesión inválida. Inicia sesión.');
            }

            if ($user->idEstadoUsuario != 1) {
                JWTAuth::invalidate();
                return redirect()->route('login')->with('error', 'Tu cuenta ha sido desactivada.');
            }

        } catch (TokenExpiredException $e) {
            return redirect()->route('login')->with('error', 'Sesión expirada. Inicia sesión nuevamente.');
        } catch (TokenInvalidException $e) {
            return redirect()->route('login')->with('error', 'Token inválido.');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'No autenticado.');
        }

        return $next($request);
    }
}