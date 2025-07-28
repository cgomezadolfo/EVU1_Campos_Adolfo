<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ObtenerProyectosController extends Controller
{
    /**
     * Obtener todos los proyectos con filtros opcionales
     */
    public function obtenerTodos(Request $request): JsonResponse
    {
        try {
            $query = Proyecto::query();

            // Filtro por responsable si se proporciona
            if ($request->has('responsable') && !empty($request->responsable)) {
                $query->porResponsable($request->responsable);
            }

            // Filtro por rango de monto si se proporciona
            if ($request->has('monto_min') && is_numeric($request->monto_min)) {
                $query->where('monto', '>=', $request->monto_min);
            }

            if ($request->has('monto_max') && is_numeric($request->monto_max)) {
                $query->where('monto', '<=', $request->monto_max);
            }

            // Filtro por estado si se proporciona
            if ($request->has('estado') && !empty($request->estado)) {
                $estadosValidos = Proyecto::getEstadosValidos();
                if (in_array($request->estado, $estadosValidos)) {
                    $query->porEstado($request->estado);
                }
            }

            // Filtro por rango de fechas si se proporciona
            if ($request->has('fecha_desde') && !empty($request->fecha_desde)) {
                $query->where('fecha_inicio', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta') && !empty($request->fecha_hasta)) {
                $query->where('fecha_inicio', '<=', $request->fecha_hasta);
            }

            // Ordenamiento
            $ordenamiento = $request->get('orden', 'created_at');
            $direccion = $request->get('direccion', 'desc');
            
            $columnasValidas = ['id', 'nombre', 'fecha_inicio', 'fecha_fin', 'estado', 'responsable', 'monto', 'created_at'];
            if (in_array($ordenamiento, $columnasValidas)) {
                $query->orderBy($ordenamiento, $direccion === 'asc' ? 'asc' : 'desc');
            }

            $proyectos = $query->get();

            // Estadísticas básicas
            $estadisticas = [
                'total' => $proyectos->count(),
                'pendientes' => $proyectos->where('estado', 'pendiente')->count(),
                'en_progreso' => $proyectos->where('estado', 'en_progreso')->count(),
                'completados' => $proyectos->where('estado', 'completado')->count(),
                'monto_total' => $proyectos->sum('monto'),
                'monto_promedio' => $proyectos->count() > 0 ? $proyectos->avg('monto') : 0,
                'monto_maximo' => $proyectos->max('monto'),
                'monto_minimo' => $proyectos->min('monto'),
                'responsables_unicos' => $proyectos->pluck('responsable')->unique()->count()
            ];

            return response()->json([
                'exito' => true,
                'mensaje' => 'Proyectos obtenidos correctamente',
                'datos' => $proyectos,
                'estadisticas' => $estadisticas,
                'filtros_aplicados' => [
                    'estado' => $request->get('estado'),
                    'responsable' => $request->get('responsable'),
                    'monto_min' => $request->get('monto_min'),
                    'monto_max' => $request->get('monto_max'),
                    'fecha_desde' => $request->get('fecha_desde'),
                    'fecha_hasta' => $request->get('fecha_hasta'),
                    'orden' => $ordenamiento,
                    'direccion' => $direccion
                ],
                'codigo' => 'PROYECTOS_OBTENIDOS'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'exito' => false,
                'mensaje' => 'Error al obtener los proyectos',
                'error' => $e->getMessage(),
                'codigo' => 'ERROR_OBTENER_PROYECTOS'
            ], 500);
        }
    }
}
