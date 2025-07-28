<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ActualizarProyectoController extends Controller
{
    /**
     * Actualizar un proyecto existente por su ID
     */
    public function actualizar(Request $request, $id): JsonResponse
    {
        try {
            // Buscar el proyecto
            $proyecto = Proyecto::findOrFail($id);

            // Validar los datos de entrada
            $datosValidados = $request->validate([
                'nombre' => 'sometimes|required|string|max:255',
                'descripcion' => 'sometimes|required|string',
                'fecha_inicio' => 'sometimes|required|date',
                'fecha_fin' => 'sometimes|nullable|date|after:fecha_inicio',
                'estado' => ['sometimes', 'required', Rule::in(Proyecto::getEstadosValidos())],
                'responsable' => 'sometimes|required|string|max:255',
                'monto' => 'sometimes|required|numeric|min:0|max:9999999999.99'
            ], [
                'nombre.required' => 'El nombre del proyecto es obligatorio',
                'nombre.max' => 'El nombre del proyecto no puede exceder los 255 caracteres',
                'descripcion.required' => 'La descripción del proyecto es obligatoria',
                'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
                'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida',
                'fecha_fin.date' => 'La fecha de fin debe ser una fecha válida',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'estado.required' => 'El estado del proyecto es obligatorio',
                'estado.in' => 'El estado debe ser: pendiente, en_progreso o completado',
                'responsable.required' => 'El responsable del proyecto es obligatorio',
                'responsable.max' => 'El nombre del responsable no puede exceder los 255 caracteres',
                'monto.required' => 'El monto del proyecto es obligatorio',
                'monto.numeric' => 'El monto debe ser un valor numérico',
                'monto.min' => 'El monto no puede ser menor a 0',
                'monto.max' => 'El monto no puede exceder $9,999,999,999.99'
            ]);

            // Guardar datos anteriores para el historial
            $datosAnteriores = $proyecto->toArray();

            // Actualizar el proyecto
            $proyecto->update($datosValidados);

            // Obtener los datos actualizados
            $proyectoActualizado = $proyecto->fresh();

            return response()->json([
                'exito' => true,
                'mensaje' => "Proyecto '{$proyecto->nombre}' actualizado exitosamente",
                'datos' => $proyectoActualizado,
                'campos_actualizados' => array_keys($datosValidados),
                'datos_anteriores' => $datosAnteriores,
                'codigo' => 'PROYECTO_ACTUALIZADO'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => "No existe un proyecto con el ID: {$id}",
                'codigo' => 'PROYECTO_NO_ENCONTRADO'
            ], 404);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Datos de validación incorrectos',
                'errores' => $e->errors(),
                'codigo' => 'VALIDACION_ERROR'
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error interno al actualizar el proyecto',
                'error' => $e->getMessage(),
                'codigo' => 'ERROR_INTERNO'
            ], 500);
        }
    }
}
