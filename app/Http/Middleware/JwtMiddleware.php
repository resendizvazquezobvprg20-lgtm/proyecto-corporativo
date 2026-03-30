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
        // 1. EXTRAER EL TOKEN DE LA COOKIE SI NO VIENE EN EL HEADER
        if (!$request->bearerToken() && $request->cookie('jwt_token')) {
            $token = $request->cookie('jwt_token');
            // Le decimos a JWTAuth que use este token específicamente
            JWTAuth::setToken($token);
        }

        try {
            // 2. Intentar autenticar
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Sesión inválida.');
            }

            if ($user->idEstadoUsuario != 1) {
                JWTAuth::invalidate();
                return redirect()->route('login')->with('cookie', \Cookie::forget('jwt_token'))->with('error', 'Cuenta desactivada.');
            }

        } catch (TokenExpiredException $e) {
            return redirect()->route('login')->with('error', 'Sesión expirada.');
        } catch (TokenInvalidException $e) {
            return redirect()->route('login')->with('error', 'Token inválido.');
        } catch (Exception $e) {
            // Si llegas aquí, es porque no encontró el token ni en Header ni en Cookie
            return redirect()->route('login')->with('error', 'No autenticado.');
        }

        return $next($request);
    }
}