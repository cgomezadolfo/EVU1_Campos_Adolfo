<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class ObtenerProyectoPorIdController extends Controller
{
    /**
     * Obtener un proyecto específico por su ID
     */
    public function obtenerPorId(Request $request, $id): JsonResponse
    {
        try {
            // Buscar el proyecto
            $proyecto = Proyecto::findOrFail($id);

            // Calcular información adicional
            $informacionAdicional = $this->calcularInformacionAdicional($proyecto);

            // Determinar si se debe incluir información detallada
            $incluirDetalle = $request->boolean('detalle', false);

            $respuesta = [
                'exito' => true,
                'mensaje' => 'Proyecto encontrado exitosamente',
                'datos' => $proyecto,
                'codigo' => 'PROYECTO_ENCONTRADO'
            ];

            // Agregar información adicional si se solicita
            if ($incluirDetalle) {
                $respuesta['informacion_adicional'] = $informacionAdicional;
            }

            return response()->json($respuesta, 200);
            
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
                'mensaje' => 'Error interno al obtener el proyecto',
                'error' => $e->getMessage(),
                'codigo' => 'ERROR_INTERNO'
            ], 500);
        }
    }

    /**
     * Calcular información adicional del proyecto
     */
    private function calcularInformacionAdicional($proyecto): array
    {
        $ahora = Carbon::now();
        $fechaInicio = Carbon::parse($proyecto->fecha_inicio);
        $fechaFin = $proyecto->fecha_fin ? Carbon::parse($proyecto->fecha_fin) : null;

        $informacion = [
            'duracion_planificada' => null,
            'dias_transcurridos' => null,
            'dias_restantes' => null,
            'porcentaje_tiempo_transcurrido' => null,
            'estado_temporal' => null,
            'tiempo_desde_creacion' => $proyecto->created_at ? 
                Carbon::parse($proyecto->created_at)->diffForHumans() : null,
            'tiempo_desde_actualizacion' => $proyecto->updated_at ? 
                Carbon::parse($proyecto->updated_at)->diffForHumans() : null
        ];

        // Calcular duración planificada
        if ($fechaFin) {
            $informacion['duracion_planificada'] = $fechaInicio->diffInDays($fechaFin) . ' días';
        }

        // Calcular días transcurridos desde el inicio
        if ($fechaInicio->isPast()) {
            $informacion['dias_transcurridos'] = $fechaInicio->diffInDays($ahora);
        } else {
            $informacion['dias_transcurridos'] = 0;
            $informacion['dias_para_inicio'] = $ahora->diffInDays($fechaInicio);
        }

        // Calcular días restantes hasta el fin
        if ($fechaFin) {
            if ($fechaFin->isFuture()) {
                $informacion['dias_restantes'] = $ahora->diffInDays($fechaFin);
            } else {
                $informacion['dias_restantes'] = 0;
                $informacion['dias_vencido'] = $fechaFin->diffInDays($ahora);
            }

            // Calcular porcentaje de tiempo transcurrido
            $duracionTotal = $fechaInicio->diffInDays($fechaFin);
            if ($duracionTotal > 0) {
                $tiempoTranscurrido = max(0, $fechaInicio->diffInDays($ahora));
                $informacion['porcentaje_tiempo_transcurrido'] = 
                    round(($tiempoTranscurrido / $duracionTotal) * 100, 2);
            }
        }

        // Determinar estado temporal
        if ($fechaInicio->isFuture()) {
            $informacion['estado_temporal'] = 'no_iniciado';
        } elseif (!$fechaFin || $fechaFin->isFuture()) {
            $informacion['estado_temporal'] = 'en_curso';
        } else {
            $informacion['estado_temporal'] = 'vencido';
        }

        return $informacion;
    }

    /**
     * Verificar existencia del proyecto sin retornar datos completos
     */
    public function verificarExistencia($id): JsonResponse
    {
        try {
            $existe = Proyecto::where('id', $id)->exists();

            return response()->json([
                'exito' => true,
                'existe' => $existe,
                'mensaje' => $existe ? 'El proyecto existe' : 'El proyecto no existe',
                'codigo' => $existe ? 'PROYECTO_EXISTE' : 'PROYECTO_NO_EXISTE'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al verificar la existencia del proyecto',
                'error' => $e->getMessage(),
                'codigo' => 'ERROR_VERIFICACION'
            ], 500);
        }
    }
}
