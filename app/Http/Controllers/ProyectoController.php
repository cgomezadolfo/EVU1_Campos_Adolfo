<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ProyectoController extends Controller
{
    /**
     * Listar todos los proyectos
     */
    public function index(): JsonResponse
    {
        try {
            $proyectos = Proyecto::all();
            
            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyectos obtenidos correctamente',
                'datos' => $proyectos,
                'total' => $proyectos->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener los proyectos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un nuevo proyecto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $datosValidados = $request->validate([
                'nombre' => 'required|string|max:255',
                'descripcion' => 'required|string',
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'nullable|date|after:fecha_inicio',
                'estado' => ['required', Rule::in(Proyecto::getEstadosValidos())]
            ], [
                'nombre.required' => 'El nombre del proyecto es obligatorio',
                'descripcion.required' => 'La descripción del proyecto es obligatoria',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completado'
            ]);

            $proyecto = Proyecto::create($datosValidados);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto creado exitosamente',
                'datos' => $proyecto
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Datos de validación incorrectos',
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
     * Obtener un proyecto específico por ID
     */
    public function show($id): JsonResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto encontrado',
                'datos' => $proyecto
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => 'No existe un proyecto con el ID proporcionado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un proyecto existente
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);

            $datosValidados = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|nullable|date|after:fecha_inicio',
                'estado' => ['sometimes', 'required', Rule::in(Proyecto::getEstadosValidos())]
            ], [
                'nombre.required' => 'El nombre del proyecto es obligatorio',
                'descripcion.required' => 'La descripción del proyecto es obligatoria',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completado'
            ]);

            $proyecto->update($datosValidados);

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyecto actualizado exitosamente',
                'datos' => $proyecto->fresh()
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => 'No existe un proyecto con el ID proporcionado'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Datos de validación incorrectos',
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
     * Eliminar un proyecto
     */
    public function destroy($id): JsonResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);
            $nombreProyecto = $proyecto->nombre;
            
            $proyecto->delete();

            return response()->json([
                'exito' => true,
                'mensaje' => "Proyecto '{$nombreProyecto}' eliminado exitosamente"
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => 'No existe un proyecto con el ID proporcionado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al eliminar el proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
