<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Servicio para obtener el valor de la Unidad de Fomento (UF) desde APIs externas
 * 
 * Consume servicios como:
 * - API del Banco Central de Chile
 * - APIs alternativas de indicadores económicos
 * 
 * Características:
 * - Cache inteligente para evitar consultas innecesarias
 * - Múltiples fuentes de datos como respaldo
 * - Conversión automática de montos CLP a UF
 * - Manejo de errores y fallbacks
 */
class UFService
{
    /**
     * URL principal de la API del Banco Central de Chile
     */
    private const API_BANCO_CENTRAL = 'https://si3.bcentral.cl/SieteRestWS/SieteRestWS.ashx';
    
    /**
     * URL alternativa para obtener UF desde MinHacienda
     */
    private const API_MINHACIENDA = 'https://mindicador.cl/api/uf';
    
    /**
     * URL alternativa adicional
     */
    private const API_SBIF = 'https://api.sbif.cl/api-sbifv3/recursos_api/uf';
    
    /**
     * Tiempo de cache en minutos (24 horas)
     */
    private const CACHE_DURATION = 1440;
    
    /**
     * Valor de UF por defecto en caso de error
     */
    private const UF_DEFAULT = 37000.0;

    /**
     * Obtiene el valor actual de la UF
     * 
     * @param string|null $fecha Fecha específica (Y-m-d), null para hoy
     * @return array Datos de la UF con valor, fecha y fuente
     */
    public function obtenerUF($fecha = null): array
    {
        $fecha = $fecha ?? Carbon::now()->format('Y-m-d');
        $cacheKey = "uf_valor_{$fecha}";
        
        // Intentar obtener desde cache
        $cached = Cache::get($cacheKey);
        if ($cached) {
            Log::info("UF obtenida desde cache para fecha: {$fecha}", $cached);
            return $cached;
        }
        
        // Intentar obtener desde APIs externas
        $resultado = $this->consultarAPIsExternas($fecha);
        
        if ($resultado['success']) {
            // Guardar en cache
            Cache::put($cacheKey, $resultado, self::CACHE_DURATION);
            Log::info("UF obtenida y guardada en cache", $resultado);
        }
        
        return $resultado;
    }

    /**
     * Consulta múltiples APIs externas para obtener la UF
     * 
     * @param string $fecha
     * @return array
     */
    private function consultarAPIsExternas($fecha): array
    {
        $apis = [
            'minhacienda' => [$this, 'consultarMinHacienda'],
            'sbif' => [$this, 'consultarSBIF'],
            'banco_central' => [$this, 'consultarBancoCentral']
        ];
        
        foreach ($apis as $fuente => $metodo) {
            try {
                Log::info("Consultando UF desde: {$fuente}");
                $resultado = call_user_func($metodo, $fecha);
                
                if ($resultado && $resultado['valor'] > 0) {
                    return [
                        'success' => true,
                        'valor' => $resultado['valor'],
                        'fecha' => $fecha,
                        'fuente' => $fuente,
                        'timestamp' => Carbon::now()->toISOString(),
                        'moneda' => 'CLP',
                        'unidad' => 'UF'
                    ];
                }
            } catch (\Exception $e) {
                Log::warning("Error consultando {$fuente}: " . $e->getMessage());
                continue;
            }
        }
        
        // Si todas las APIs fallan, retornar valor por defecto
        Log::error("Todas las APIs de UF fallaron, usando valor por defecto");
        return [
            'success' => false,
            'valor' => self::UF_DEFAULT,
            'fecha' => $fecha,
            'fuente' => 'default',
            'timestamp' => Carbon::now()->toISOString(),
            'moneda' => 'CLP',
            'unidad' => 'UF',
            'error' => 'No se pudo obtener UF desde APIs externas'
        ];
    }

    /**
     * Consulta la API de MinHacienda
     * 
     * @param string $fecha
     * @return array|null
     */
    private function consultarMinHacienda($fecha): ?array
    {
        $url = self::API_MINHACIENDA;
        if ($fecha !== Carbon::now()->format('Y-m-d')) {
            $url .= "/{$fecha}";
        }
        
        $response = Http::timeout(10)->get($url);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['serie']) && is_array($data['serie']) && count($data['serie']) > 0) {
                return ['valor' => (float) $data['serie'][0]['valor']];
            } elseif (isset($data['valor'])) {
                return ['valor' => (float) $data['valor']];
            }
        }
        
        return null;
    }

    /**
     * Consulta la API de SBIF (Superintendencia de Bancos)
     * 
     * @param string $fecha
     * @return array|null
     */
    private function consultarSBIF($fecha): ?array
    {
        try {
            $year = Carbon::parse($fecha)->year;
            $month = Carbon::parse($fecha)->month;
            
            $response = Http::timeout(10)
                ->withHeaders(['apikey' => config('services.sbif.key', '')])
                ->get(self::API_SBIF . "/{$year}/{$month}");
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['UFs']) && is_array($data['UFs'])) {
                    foreach ($data['UFs'] as $uf) {
                        if ($uf['Fecha'] === $fecha) {
                            return ['valor' => (float) str_replace(',', '', $uf['Valor'])];
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error en SBIF API: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Consulta la API del Banco Central de Chile
     * 
     * @param string $fecha
     * @return array|null
     */
    private function consultarBancoCentral($fecha): ?array
    {
        try {
            // Nota: La API del Banco Central requiere autenticación
            // Por ahora retornamos null, pero se puede implementar
            // con las credenciales apropiadas
            
            $user = config('services.banco_central.user');
            $password = config('services.banco_central.password');
            
            if (!$user || !$password) {
                return null;
            }
            
            // Implementación de la API del Banco Central
            // (requiere configuración adicional)
            
        } catch (\Exception $e) {
            Log::warning("Error en Banco Central API: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Convierte un monto en pesos chilenos a UF
     * 
     * @param float $montoCLP Monto en pesos chilenos
     * @param string|null $fecha Fecha para obtener UF, null para hoy
     * @return array Resultado de la conversión
     */
    public function convertirCLPaUF($montoCLP, $fecha = null): array
    {
        $ufData = $this->obtenerUF($fecha);
        $valorUF = $ufData['valor'];
        
        $montoUF = $montoCLP / $valorUF;
        
        return [
            'monto_clp' => $montoCLP,
            'monto_uf' => round($montoUF, 4),
            'valor_uf_usado' => $valorUF,
            'fecha_uf' => $ufData['fecha'],
            'fuente_uf' => $ufData['fuente'],
            'timestamp' => Carbon::now()->toISOString()
        ];
    }

    /**
     * Convierte un monto en UF a pesos chilenos
     * 
     * @param float $montoUF Monto en UF
     * @param string|null $fecha Fecha para obtener UF, null para hoy
     * @return array Resultado de la conversión
     */
    public function convertirUFaCLP($montoUF, $fecha = null): array
    {
        $ufData = $this->obtenerUF($fecha);
        $valorUF = $ufData['valor'];
        
        $montoCLP = $montoUF * $valorUF;
        
        return [
            'monto_uf' => $montoUF,
            'monto_clp' => round($montoCLP, 0),
            'valor_uf_usado' => $valorUF,
            'fecha_uf' => $ufData['fecha'],
            'fuente_uf' => $ufData['fuente'],
            'timestamp' => Carbon::now()->toISOString()
        ];
    }

    /**
     * Obtiene el historial de UF para un rango de fechas
     * 
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function obtenerHistorialUF($fechaInicio, $fechaFin): array
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $historial = [];
        
        while ($inicio->lte($fin)) {
            $fecha = $inicio->format('Y-m-d');
            $ufData = $this->obtenerUF($fecha);
            
            $historial[] = [
                'fecha' => $fecha,
                'valor' => $ufData['valor'],
                'fuente' => $ufData['fuente']
            ];
            
            $inicio->addDay();
        }
        
        return [
            'historial' => $historial,
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin,
                'dias' => count($historial)
            ],
            'estadisticas' => [
                'promedio' => round(collect($historial)->avg('valor'), 2),
                'maximo' => collect($historial)->max('valor'),
                'minimo' => collect($historial)->min('valor')
            ]
        ];
    }

    /**
     * Limpia el cache de UF
     * 
     * @param string|null $fecha Fecha específica, null para limpiar todo
     * @return bool
     */
    public function limpiarCache($fecha = null): bool
    {
        if ($fecha) {
            $cacheKey = "uf_valor_{$fecha}";
            return Cache::forget($cacheKey);
        }
        
        // Limpiar todo el cache de UF
        $keys = Cache::getStore()->getRedis()->keys('*uf_valor_*');
        foreach ($keys as $key) {
            Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
        }
        
        return true;
    }

    /**
     * Obtiene estadísticas del servicio UF
     * 
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        $ufHoy = $this->obtenerUF();
        $ufAyer = $this->obtenerUF(Carbon::yesterday()->format('Y-m-d'));
        
        $variacion = $ufHoy['valor'] - $ufAyer['valor'];
        $porcentajeVariacion = ($variacion / $ufAyer['valor']) * 100;
        
        return [
            'uf_actual' => $ufHoy,
            'uf_anterior' => $ufAyer,
            'variacion' => [
                'absoluta' => round($variacion, 2),
                'porcentual' => round($porcentajeVariacion, 4)
            ],
            'servicios_disponibles' => [
                'minhacienda' => $this->verificarServicio('minhacienda'),
                'sbif' => $this->verificarServicio('sbif'),
                'banco_central' => $this->verificarServicio('banco_central')
            ],
            'cache_info' => [
                'activo' => Cache::has("uf_valor_" . Carbon::now()->format('Y-m-d')),
                'ttl_minutos' => self::CACHE_DURATION
            ]
        ];
    }

    /**
     * Verifica si un servicio está disponible
     * 
     * @param string $servicio
     * @return bool
     */
    private function verificarServicio($servicio): bool
    {
        try {
            switch ($servicio) {
                case 'minhacienda':
                    $response = Http::timeout(5)->get(self::API_MINHACIENDA);
                    return $response->successful();
                    
                case 'sbif':
                    // Verificar si hay API key configurada
                    return !empty(config('services.sbif.key'));
                    
                case 'banco_central':
                    // Verificar si hay credenciales configuradas
                    return !empty(config('services.banco_central.user'));
                    
                default:
                    return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
}
