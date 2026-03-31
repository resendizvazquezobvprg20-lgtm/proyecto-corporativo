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
            // 1. Intentar obtener token del header Bearer primero
            $token = $request->bearerToken();

            // 2. Si no hay header, leer de la cookie (httpOnly — sólo accesible server-side)
            //    encryptCookiesExcept(['jwt_token']) en bootstrap/app.php asegura que
            //    el valor que leemos aquí es el JWT crudo, no cifrado por Laravel.
            if (!$token) {
                $token = $request->cookie('jwt_token');
            }

            if (!$token) {
                return redirect()->route('login')->with('error', 'No autenticado.');
            }

            // 3. Autenticar con el token obtenido
            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Sesión inválida.');
            }

            // 4. Verificar estado activo
            if ($user->idEstadoUsuario != 1) {
                JWTAuth::invalidate();
                return redirect()->route('login')
                    ->withCookie(\Cookie::forget('jwt_token'))
                    ->with('error', 'Cuenta desactivada.');
            }

        } catch (TokenExpiredException $e) {
            return redirect()->route('login')
                ->withCookie(\Cookie::forget('jwt_token'))
                ->with('error', 'Sesión expirada. Inicia sesión nuevamente.');
        } catch (TokenInvalidException $e) {
            return redirect()->route('login')
                ->withCookie(\Cookie::forget('jwt_token'))
                ->with('error', 'Token inválido.');
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'No autenticado.');
        }

        return $next($request);
    }
}
