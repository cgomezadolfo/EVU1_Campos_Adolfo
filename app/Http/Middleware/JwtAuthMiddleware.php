<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Intentar obtener el usuario desde el token JWT
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user) {
                return $this->unauthorizedResponse($request, 'Usuario no encontrado');
            }
            
        } catch (TokenExpiredException $e) {
            return $this->unauthorizedResponse($request, 'Token expirado', 'token_expired');
            
        } catch (TokenInvalidException $e) {
            return $this->unauthorizedResponse($request, 'Token inválido', 'token_invalid');
            
        } catch (JWTException $e) {
            return $this->unauthorizedResponse($request, 'Token no proporcionado', 'token_absent');
            
        } catch (\Exception $e) {
            return $this->unauthorizedResponse($request, 'Error de autenticación', 'auth_error');
        }

        return $next($request);
    }

    /**
     * Devolver respuesta no autorizada basada en el tipo de petición
     */
    private function unauthorizedResponse(Request $request, string $mensaje, string $codigo = 'unauthorized')
    {
        // Si es una petición AJAX o API, devolver JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'exito' => false,
                'mensaje' => $mensaje,
                'codigo_error' => $codigo,
                'datos' => null
            ], 401);
        }

        // Si es una petición web, redirigir al login
        return redirect()->route('auth.login')->with('error', $mensaje);
    }
}
