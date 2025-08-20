<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Carbon\Carbon;

/**
 * Controlador de Autenticación con JWT
 * 
 * Este controlador conecta las rutas de autenticación con los modelos definidos,
 * implementando funciones específicas para:
 * 1. Registro de Usuario con cifrado de clave
 * 2. Inicio de Sesión devolviendo JWT si las credenciales son correctas
 */
class AutenticacionJWTController extends Controller
{
    /**
     * 1. Función de Registro de Usuario
     * Implementa cifrado a la clave y conecta con el modelo User
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function registroUsuario(Request $request): JsonResponse
    {
        try {
            // Validación de datos con reglas de seguridad
            $validador = Validator::make($request->all(), [
                'nombre' => [
                    'required',
                    'string',
                    'max:255',
                    'min:2',
                    'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/' // Solo letras y espacios
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    'unique:users,email' // Verificar unicidad en modelo User
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    // Contraseña segura: mayúscula, minúscula, número y símbolo
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
                ],
                'confirmar_password' => [
                    'required',
                    'same:password'
                ]
            ], [
                // Mensajes de error en español
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
                'nombre.regex' => 'El nombre solo puede contener letras y espacios',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'Debe proporcionar un correo electrónico válido',
                'email.unique' => 'Este correo electrónico ya está registrado en el sistema',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.regex' => 'La contraseña debe contener: 1 mayúscula, 1 minúscula, 1 número y 1 símbolo especial',
                'confirmar_password.required' => 'La confirmación de contraseña es obligatoria',
                'confirmar_password.same' => 'La confirmación de contraseña no coincide'
            ]);

            // Si hay errores de validación
            if ($validador->fails()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Error en la validación de datos',
                    'errores' => $validador->errors()
                ], 422);
            }

            // Conectar con el modelo User para crear nuevo usuario
            // IMPLEMENTACIÓN DE CIFRADO DE CLAVE
            $usuario = User::create([
                'name' => trim($request->nombre),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password), // ✅ CIFRADO IMPLEMENTADO
                'email_verified_at' => now()
            ]);

            // Generar JWT automáticamente después del registro
            $token = JWTAuth::fromUser($usuario);

            // Log del registro exitoso
            \Log::info('Nuevo usuario registrado con JWT', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip(),
                'timestamp' => now()
            ]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Usuario registrado exitosamente',
                'datos' => [
                    'usuario' => [
                        'id' => $usuario->id,
                        'nombre' => $usuario->name,
                        'email' => $usuario->email,
                        'fecha_registro' => $usuario->created_at->format('d/m/Y H:i:s')
                    ],
                    'jwt_token' => $token,
                    'tipo_token' => 'Bearer',
                    'expira_en' => '60 minutos'
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error en registro de usuario con JWT', [
                'error' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'datos' => $request->except('password', 'confirmar_password')
            ]);

            return response()->json([
                'exito' => false,
                'mensaje' => 'Error interno del servidor durante el registro',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * 2. Función de Inicio de Sesión  
     * Devuelve un JWT si las credenciales son correctas
     * Conecta con el modelo User para validación
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function inicioSesion(Request $request): JsonResponse
    {
        try {
            // Validación de credenciales
            $validador = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'email:rfc',
                    'exists:users,email' // Verificar existencia en modelo User
                ],
                'password' => [
                    'required',
                    'string',
                    'min:1'
                ]
            ], [
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'Debe proporcionar un correo electrónico válido',
                'email.exists' => 'No existe una cuenta con este correo electrónico',
                'password.required' => 'La contraseña es obligatoria'
            ]);

            if ($validador->fails()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Credenciales inválidas',
                    'errores' => $validador->errors()
                ], 422);
            }

            // Preparar credenciales
            $credenciales = [
                'email' => strtolower(trim($request->email)),
                'password' => $request->password
            ];

            try {
                // ✅ GENERAR JWT SI LAS CREDENCIALES SON CORRECTAS
                if (!$token = JWTAuth::attempt($credenciales)) {
                    // Log de intento fallido
                    \Log::warning('Intento de acceso fallido con JWT', [
                        'email' => $credenciales['email'],
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'timestamp' => now()
                    ]);

                    return response()->json([
                        'exito' => false,
                        'mensaje' => 'credenciales incorrectas',
                        'error' => 'Email o contraseña incorrectos'
                    ], 401);
                }

            } catch (JWTException $e) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Error al crear el token JWT',
                    'error' => 'No se pudo generar el token de acceso'
                ], 500);
            }

            // Obtener usuario autenticado del modelo
            $usuario = User::where('email', $credenciales['email'])->first();

            // Actualizar campos de seguimiento en el modelo
            $usuario->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);

            // Log de acceso exitoso
            \Log::info('Usuario inició sesión exitosamente con JWT', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // ✅ DEVOLVER JWT SI LAS CREDENCIALES SON CORRECTAS
            return response()->json([
                'exito' => true,
                'mensaje' => 'Inicio de sesión exitoso',
                'datos' => [
                    'usuario' => [
                        'id' => $usuario->id,
                        'nombre' => $usuario->name,
                        'email' => $usuario->email,
                        'ultimo_acceso' => $usuario->last_login_at?->format('d/m/Y H:i:s'),
                        'fecha_registro' => $usuario->created_at->format('d/m/Y H:i:s')
                    ],
                    'jwt_token' => $token, // ✅ JWT DEVUELTO
                    'tipo_token' => 'Bearer',
                    'expira_en' => '60 minutos'
                ]
            ], 200);

        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en inicio de sesión con JWT', [
                'error' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'email' => $request->email ?? 'no_proporcionado'
            ]);

            return response()->json([
                'exito' => false,
                'mensaje' => 'Error interno del servidor durante el inicio de sesión',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Obtener usuario autenticado usando JWT
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerUsuario(Request $request): JsonResponse
    {
        try {
            // Obtener usuario del JWT
            $usuario = JWTAuth::parseToken()->authenticate();

            if (!$usuario) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Usuario no encontrado'
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Información del usuario obtenida',
                'datos' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->name,
                    'email' => $usuario->email,
                    'fecha_registro' => $usuario->created_at->format('d/m/Y H:i:s'),
                    'ultimo_acceso' => $usuario->last_login_at?->format('d/m/Y H:i:s')
                ]
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Token inválido',
                'error' => 'No se pudo autenticar el usuario'
            ], 401);
        }
    }

    /**
     * Cerrar sesión (invalidar JWT)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function cerrarSesion(Request $request): JsonResponse
    {
        try {
            // Obtener usuario antes de invalidar token
            $usuario = JWTAuth::parseToken()->authenticate();

            // Invalidar el JWT
            JWTAuth::invalidate(JWTAuth::getToken());

            // Log de cierre de sesión
            \Log::info('Usuario cerró sesión con JWT', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Sesión cerrada exitosamente'
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al cerrar sesión',
                'error' => 'Token inválido'
            ], 401);
        }
    }

    /**
     * Refrescar JWT (obtener nuevo token)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function refrescarToken(Request $request): JsonResponse
    {
        try {
            $nuevoToken = JWTAuth::refresh(JWTAuth::getToken());

            return response()->json([
                'exito' => true,
                'mensaje' => 'Token refrescado exitosamente',
                'datos' => [
                    'jwt_token' => $nuevoToken,
                    'tipo_token' => 'Bearer',
                    'expira_en' => '60 minutos'
                ]
            ], 200);

        } catch (JWTException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al refrescar token',
                'error' => 'Token inválido'
            ], 401);
        }
    }
}
