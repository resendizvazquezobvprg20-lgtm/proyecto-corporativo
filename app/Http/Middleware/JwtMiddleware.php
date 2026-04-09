<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken() ?? $request->cookie('jwt_token');

            if (!$token) {
                Log::warning('[JWT-MW] Sin token en header ni cookie. Cookies: ' . implode(', ', array_keys($request->cookies->all())));
                return redirect()->route('login')->with('error', 'No autenticado.');
            }

            Log::info('[JWT-MW] Token encontrado, len=' . strlen($token) . ', primeros20=' . substr($token, 0, 20));

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                Log::warning('[JWT-MW] authenticate() devolvió null');
                return redirect()->route('login')->with('error', 'Sesión inválida.');
            }

            if ($user->idEstadoUsuario != 1) {
                JWTAuth::invalidate();
                return redirect()->route('login')
                    ->withCookie(\Cookie::forget('jwt_token'))
                    ->with('error', 'Cuenta desactivada.');
            }

            Log::info('[JWT-MW] Auth OK: ' . $user->strNombreUsuario);

        } catch (TokenExpiredException $e) {
            Log::warning('[JWT-MW] TokenExpired');
            return redirect()->route('login')
                ->withCookie(\Cookie::forget('jwt_token'))
                ->with('error', 'Sesión expirada. Inicia sesión nuevamente.');
        } catch (TokenInvalidException $e) {
            Log::warning('[JWT-MW] TokenInvalid: ' . $e->getMessage());
            return redirect()->route('login')
                ->withCookie(\Cookie::forget('jwt_token'))
                ->with('error', 'Token inválido.');
        } catch (Exception $e) {
            Log::error('[JWT-MW] Exception: ' . get_class($e) . ': ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'No autenticado.');
        }

        return $next($request);
    }
}
