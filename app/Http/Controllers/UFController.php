<?php

namespace App\Http\Controllers;

use App\Services\UFService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * Controlador para manejar operaciones relacionadas con la Unidad de Fomento (UF)
 */
class UFController extends Controller
{
    private UFService $ufService;

    public function __construct(UFService $ufService)
    {
        $this->ufService = $ufService;
    }

    /**
     * Obtiene el valor actual de la UF
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerUFActual(Request $request): JsonResponse
    {
        try {
            $fecha = $request->query('fecha');
            $incluirEstadisticas = $request->boolean('estadisticas', false);
            
            $ufData = $this->ufService->obtenerUF($fecha);
            
            $response = [
                'success' => true,
                'data' => $ufData,
                'timestamp' => Carbon::now()->toISOString()
            ];
            
            if ($incluirEstadisticas) {
                $response['estadisticas'] = $this->ufService->obtenerEstadisticas();
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener UF: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }

    /**
     * Convierte un monto de CLP a UF
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function convertirCLPaUF(Request $request): JsonResponse
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'fecha' => 'nullable|date_format:Y-m-d'
        ]);

        try {
            $monto = $request->input('monto');
            $fecha = $request->input('fecha');
            
            $conversion = $this->ufService->convertirCLPaUF($monto, $fecha);
            
            return response()->json([
                'success' => true,
                'data' => $conversion,
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error en conversión: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }

    /**
     * Convierte un monto de UF a CLP
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function convertirUFaCLP(Request $request): JsonResponse
    {
        $request->validate([
            'monto' => 'required|numeric|min:0',
            'fecha' => 'nullable|date_format:Y-m-d'
        ]);

        try {
            $monto = $request->input('monto');
            $fecha = $request->input('fecha');
            
            $conversion = $this->ufService->convertirUFaCLP($monto, $fecha);
            
            return response()->json([
                'success' => true,
                'data' => $conversion,
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error en conversión: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }

    /**
     * Obtiene el historial de UF para un rango de fechas
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function obtenerHistorial(Request $request): JsonResponse
    {
        $request->validate([
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'fecha_fin' => 'required|date_format:Y-m-d|after_or_equal:fecha_inicio'
        ]);

        try {
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');
            
            // Limitar a máximo 30 días para evitar consultas muy pesadas
            $inicio = Carbon::parse($fechaInicio);
            $fin = Carbon::parse($fechaFin);
            
            if ($inicio->diffInDays($fin) > 30) {
                return response()->json([
                    'success' => false,
                    'error' => 'El rango de fechas no puede ser mayor a 30 días',
                    'timestamp' => Carbon::now()->toISOString()
                ], 400);
            }
            
            $historial = $this->ufService->obtenerHistorialUF($fechaInicio, $fechaFin);
            
            return response()->json([
                'success' => true,
                'data' => $historial,
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener historial: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }

    /**
     * Obtiene estadísticas del servicio UF
     * 
     * @return JsonResponse
     */
    public function obtenerEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = $this->ufService->obtenerEstadisticas();
            
            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al obtener estadísticas: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }

    /**
     * Limpia el cache de UF
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function limpiarCache(Request $request): JsonResponse
    {
        try {
            $fecha = $request->query('fecha');
            $resultado = $this->ufService->limpiarCache($fecha);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'cache_limpiado' => $resultado,
                    'fecha' => $fecha ?? 'todas'
                ],
                'timestamp' => Carbon::now()->toISOString()
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al limpiar cache: ' . $e->getMessage(),
                'timestamp' => Carbon::now()->toISOString()
            ], 500);
        }
    }
}
