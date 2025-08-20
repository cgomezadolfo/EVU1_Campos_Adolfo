<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para manejar autenticación y autorización de usuarios
 * Implementa métodos seguros para registro e inicio de sesión
 */
class AutenticacionController extends Controller
{
    /**
     * 1. Registro de Usuario
     * Crea un nuevo usuario con datos cifrados
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function registrarUsuario(Request $request): JsonResponse
    {
        try {
            // Validación de datos de entrada con reglas de seguridad
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
                    'email:rfc,dns', // Validación RFC y DNS
                    'max:255',
                    'unique:users,email' // Email único
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'max:255',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/' // Contraseña segura
                ],
                'confirmar_password' => [
                    'required',
                    'same:password' // Confirmación de contraseña
                ]
            ], [
                // Mensajes de error personalizados en español
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
                'nombre.regex' => 'El nombre solo puede contener letras y espacios',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'Debe proporcionar un correo electrónico válido',
                'email.unique' => 'Este correo electrónico ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.regex' => 'La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 símbolo',
                'confirmar_password.required' => 'La confirmación de contraseña es obligatoria',
                'confirmar_password.same' => 'La confirmación de contraseña no coincide'
            ]);

            if ($validador->fails()) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Errores de validación en los datos proporcionados',
                    'errores' => $validador->errors()
                ], 422);
            }

            // Crear usuario con datos cifrados
            $usuario = User::create([
                'name' => trim($request->nombre),
                'email' => strtolower(trim($request->email)),
                'password' => Hash::make($request->password), // Cifrado automático con bcrypt
                'email_verified_at' => now() // Por simplicidad, verificamos automáticamente
            ]);

            // Generar token de acceso seguro
            $token = $usuario->createToken(
                'token_acceso_' . $usuario->id,
                ['*'], // Permisos completos
                now()->addDays(30) // Token válido por 30 días
            )->plainTextToken;

            // Registrar evento de registro exitoso
            \Log::info('Nuevo usuario registrado', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
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
                    'token' => $token,
                    'tipo_token' => 'Bearer',
                    'expira_en' => '30 días'
                ]
            ], 201);

        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error en registro de usuario', [
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
     * 2. Inicio de Sesión de Usuario
     * Autentica usuario y genera token de acceso
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function iniciarSesion(Request $request): JsonResponse
    {
        try {
            // Validación de credenciales
            $validador = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'email:rfc',
                    'exists:users,email' // Verificar que el email exista
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

            // Normalizar email
            $email = strtolower(trim($request->email));
            $password = $request->password;

            // Buscar usuario por email
            $usuario = User::where('email', $email)->first();

            // Verificar contraseña con Hash seguro
            if (!$usuario || !Hash::check($password, $usuario->password)) {
                // Log de intento fallido por seguridad
                \Log::warning('Intento de acceso fallido', [
                    'email' => $email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now()
                ]);

                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Credenciales incorrectas',
                    'error' => 'Email o contraseña incorrectos'
                ], 401);
            }

            // Revocar tokens anteriores por seguridad (opcional)
            if ($request->has('revocar_otros_tokens') && $request->revocar_otros_tokens) {
                $usuario->tokens()->delete();
            }

            // Generar nuevo token de acceso
            $token = $usuario->createToken(
                'sesion_' . $usuario->id . '_' . time(),
                ['*'], // Permisos completos
                now()->addHours(24) // Token válido por 24 horas
            )->plainTextToken;

            // Actualizar última conexión
            $usuario->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip()
            ]);

            // Log de acceso exitoso
            \Log::info('Usuario inició sesión exitosamente', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

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
                    'token' => $token,
                    'tipo_token' => 'Bearer',
                    'expira_en' => '24 horas'
                ]
            ], 200);

        } catch (\Exception $e) {
            // Log del error
            \Log::error('Error en inicio de sesión', [
                'error' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'email' => $request->email
            ]);

            return response()->json([
                'exito' => false,
                'mensaje' => 'Error interno del servidor durante el inicio de sesión',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Cerrar sesión (revocar token actual)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function cerrarSesion(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();
            
            // Revocar token actual
            $request->user()->currentAccessToken()->delete();

            // Log de cierre de sesión
            \Log::info('Usuario cerró sesión', [
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Sesión cerrada exitosamente'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al cerrar sesión',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function perfilUsuario(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();

            return response()->json([
                'exito' => true,
                'mensaje' => 'Información del usuario obtenida',
                'datos' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->name,
                    'email' => $usuario->email,
                    'fecha_registro' => $usuario->created_at->format('d/m/Y H:i:s'),
                    'ultimo_acceso' => $usuario->last_login_at?->format('d/m/Y H:i:s'),
                    'tokens_activos' => $usuario->tokens()->count()
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener perfil de usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
