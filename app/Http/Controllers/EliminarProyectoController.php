<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EliminarProyectoController extends Controller
{
    /**
     * Eliminar un proyecto por su ID
     */
    public function eliminar(Request $request, $id): JsonResponse
    {
        try {
            // Buscar el proyecto
            $proyecto = Proyecto::findOrFail($id);

            // Guardar información del proyecto antes de eliminarlo
            $informacionProyecto = [
                'id' => $proyecto->id,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
                'fecha_inicio' => $proyecto->fecha_inicio,
                'fecha_fin' => $proyecto->fecha_fin,
                'estado' => $proyecto->estado,
                'fecha_creacion' => $proyecto->created_at,
                'fecha_eliminacion' => now()
            ];

            // Validar si se debe forzar la eliminación
            $forzarEliminacion = $request->boolean('forzar', false);

            // Verificar si el proyecto está en progreso y no se fuerza la eliminación
            if ($proyecto->estado === 'en_progreso' && !$forzarEliminacion) {
                return response()->json([
                    'exito' => false,
                    'mensaje' => 'No se puede eliminar un proyecto en progreso',
                    'error' => 'El proyecto está actualmente en progreso. Use el parámetro "forzar=true" para eliminarlo de todas formas.',
                    'datos_proyecto' => $informacionProyecto,
                    'codigo' => 'PROYECTO_EN_PROGRESO'
                ], 409); // Conflict
            }

            // Eliminar el proyecto
            $proyecto->delete();

            return response()->json([
                'exito' => true,
                'mensaje' => "Proyecto '{$informacionProyecto['nombre']}' eliminado exitosamente",
                'proyecto_eliminado' => $informacionProyecto,
                'eliminacion_forzada' => $forzarEliminacion,
                'codigo' => 'PROYECTO_ELIMINADO'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => "No existe un proyecto con el ID: {$id}",
                'codigo' => 'PROYECTO_NO_ENCONTRADO'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error interno al eliminar el proyecto',
                'error' => $e->getMessage(),
                'codigo' => 'ERROR_INTERNO'
            ], 500);
        }
    }

    /**
     * Obtener confirmación antes de eliminar (soft check)
     */
    public function confirmarEliminacion($id): JsonResponse
    {
        try {
            $proyecto = Proyecto::findOrFail($id);

            $advertencias = [];
            $puedeEliminar = true;

            // Verificar estado del proyecto
            if ($proyecto->estado === 'en_progreso') {
                $advertencias[] = 'El proyecto está actualmente en progreso';
                $puedeEliminar = false;
            }

            // Verificar si tiene fecha de fin futura
            if ($proyecto->fecha_fin && $proyecto->fecha_fin > now()) {
                $advertencias[] = 'El proyecto tiene fecha de finalización futura';
            }

            return response()->json([
                'exito' => true,
                'puede_eliminar' => $puedeEliminar,
                'proyecto' => $proyecto,
                'advertencias' => $advertencias,
                'mensaje' => $puedeEliminar ? 
                    'El proyecto puede ser eliminado' : 
                    'El proyecto requiere confirmación para ser eliminado',
                'codigo' => 'CONFIRMACION_ELIMINACION'
            ], 200);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Proyecto no encontrado',
                'error' => "No existe un proyecto con el ID: {$id}",
                'codigo' => 'PROYECTO_NO_ENCONTRADO'
            ], 404);
        }
    }
}
