<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/proyectos
     * Código respuesta: 200
     */
    public function index(): JsonResponse
    {
        try {
            $proyectos = Proyecto::with('creador:id,name,email')->get();
            
            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyectos obtenidos exitosamente',
                'datos' => $proyectos,
                'total' => $proyectos->count()
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener proyectos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/proyectos
     * Código respuesta: 201
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validación: todos los campos son requeridos y no deben estar vacíos
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255|min:3',
                'descripcion' => 'required|string|min:10',
                'fecha_inicio' => 'required|date|after_or_equal:today',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'estado' => ['required', Rule::in(['pendiente', 'en_progreso', 'completado'])],
                'responsable' => 'required|string|max:255|min:3',
                'monto' => 'required|numeric|min:0|max:999999999.99'
            ], [
                'nombre.required' => 'El nombre del proyecto es requerido',
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
                'descripcion.required' => 'La descripción es requerida',
                'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
                'fecha_inicio.required' => 'La fecha de inicio es requerida',
                'fecha_inicio.after_or_equal' => 'La fecha de inicio no puede ser anterior a hoy',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'estado.required' => 'El estado es requerido',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completado',
                'responsable.required' => 'El responsable es requerido',
                'responsable.min' => 'El responsable debe tener al menos 3 caracteres',
                'monto.required' => 'El monto es requerido',
                'monto.numeric' => 'El monto debe ser un número válido',
                'monto.min' => 'El monto no puede ser negativo'
            ]);

            // Obtener usuario autenticado
            $user = JWTAuth::user();
            $validatedData['created_by'] = $user->id;

            // Crear el proyecto
            $proyecto = Proyecto::create($validatedData);
            
            // Cargar la relación con el creador
            $proyecto->load('creador:id,name,email');

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto creado exitosamente',
                'datos' => $proyecto
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al crear el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * GET /api/proyectos/{id}
     * Código respuesta: 200 si existe, 404 si no existe
     */
    public function show(string $id): JsonResponse
    {
        try {
            $proyecto = Proyecto::with('creador:id,name,email')->find($id);
            
            if (!$proyecto) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado',
                    'datos' => null
                ], 404);
            }

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto obtenido exitosamente',
                'datos' => $proyecto
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/proyectos/{id}
     * Código respuesta: 200 si se actualiza, 404 si no existe
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $proyecto = Proyecto::find($id);
            
            if (!$proyecto) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado',
                    'datos' => null
                ], 404);
            }

            // Validación para actualización (campos opcionales)
            $validatedData = $request->validate([
                'nombre' => 'sometimes|required|string|max:255|min:3',
                'descripcion' => 'sometimes|required|string|min:10',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|nullable|date|after:fecha_inicio',
                'estado' => ['sometimes', 'required', Rule::in(['pendiente', 'en_progreso', 'completado'])],
                'responsable' => 'sometimes|required|string|max:255|min:3',
                'monto' => 'sometimes|required|numeric|min:0|max:999999999.99'
            ], [
                'nombre.min' => 'El nombre debe tener al menos 3 caracteres',
                'descripcion.min' => 'La descripción debe tener al menos 10 caracteres',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completado',
                'responsable.min' => 'El responsable debe tener al menos 3 caracteres',
                'monto.numeric' => 'El monto debe ser un número válido',
                'monto.min' => 'El monto no puede ser negativo'
            ]);

            // Actualizar el proyecto
            $proyecto->update($validatedData);
            
            // Recargar con relaciones
            $proyecto->load('creador:id,name,email');

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto actualizado exitosamente',
                'datos' => $proyecto
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Errores de validación',
                'errores' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al actualizar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/proyectos/{id}
     * Código respuesta: 204 si se elimina, 404 si no existe
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $proyecto = Proyecto::find($id);
            
            if (!$proyecto) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado',
                    'datos' => null
                ], 404);
            }

            $proyecto->delete();

            // Respuesta vacía con código 204
            return response()->json(null, 204);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al eliminar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
