<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UFService;
use Carbon\Carbon;

class TestUFCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uf:test 
                            {--fecha= : Fecha específica para obtener UF (Y-m-d)}
                            {--convertir= : Monto a convertir de CLP a UF}
                            {--historial : Obtener historial de últimos 7 días}
                            {--estadisticas : Mostrar estadísticas del servicio}
                            {--limpiar-cache : Limpiar cache de UF}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para testear y probar el servicio de UF';

    private UFService $ufService;

    public function __construct(UFService $ufService)
    {
        parent::__construct();
        $this->ufService = $ufService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Iniciando pruebas del Servicio UF...');
        $this->newLine();

        // Limpiar cache si se solicitó
        if ($this->option('limpiar-cache')) {
            $this->limpiarCache();
        }

        // Obtener estadísticas si se solicitó
        if ($this->option('estadisticas')) {
            $this->mostrarEstadisticas();
        }

        // Obtener historial si se solicitó
        if ($this->option('historial')) {
            $this->mostrarHistorial();
        }

        // Convertir monto si se proporcionó
        if ($this->option('convertir')) {
            $this->convertirMonto();
        }

        // Obtener UF del día o fecha específica
        $this->obtenerUF();

        $this->newLine();
        $this->info('✅ Pruebas del Servicio UF completadas');
    }

    private function obtenerUF()
    {
        $fecha = $this->option('fecha');
        
        $this->info('📊 Obteniendo valor de UF...');
        
        try {
            $ufData = $this->ufService->obtenerUF($fecha);
            
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Fecha', $ufData['fecha']],
                    ['Valor UF', '$' . number_format($ufData['valor'], 2, ',', '.')],
                    ['Fuente', ucfirst($ufData['fuente'])],
                    ['Exitoso', $ufData['success'] ? 'Sí' : 'No'],
                    ['Timestamp', $ufData['timestamp']],
                ]
            );

            if (!$ufData['success']) {
                $this->warn('⚠️  Se usó valor por defecto debido a error en APIs externas');
                if (isset($ufData['error'])) {
                    $this->error('Error: ' . $ufData['error']);
                }
            } else {
                $this->info('✅ UF obtenida exitosamente desde: ' . $ufData['fuente']);
            }

        } catch (\Exception $e) {
            $this->error('❌ Error obteniendo UF: ' . $e->getMessage());
        }
    }

    private function convertirMonto()
    {
        $monto = (float) $this->option('convertir');
        $fecha = $this->option('fecha');
        
        $this->info("💱 Convirtiendo $" . number_format($monto, 0, ',', '.') . " CLP a UF...");
        
        try {
            $conversion = $this->ufService->convertirCLPaUF($monto, $fecha);
            
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Monto CLP', '$' . number_format($conversion['monto_clp'], 0, ',', '.')],
                    ['Monto UF', number_format($conversion['monto_uf'], 4, ',', '.') . ' UF'],
                    ['Valor UF usado', '$' . number_format($conversion['valor_uf_usado'], 2, ',', '.')],
                    ['Fecha UF', $conversion['fecha_uf']],
                    ['Fuente', ucfirst($conversion['fuente_uf'])],
                ]
            );

            $this->info('✅ Conversión completada exitosamente');

        } catch (\Exception $e) {
            $this->error('❌ Error en conversión: ' . $e->getMessage());
        }
    }

    private function mostrarHistorial()
    {
        $fechaFin = Carbon::now()->format('Y-m-d');
        $fechaInicio = Carbon::now()->subDays(6)->format('Y-m-d');
        
        $this->info("📈 Obteniendo historial UF (últimos 7 días: {$fechaInicio} a {$fechaFin})...");
        
        try {
            $historial = $this->ufService->obtenerHistorialUF($fechaInicio, $fechaFin);
            
            $datos = collect($historial['historial'])->map(function ($item) {
                return [
                    'fecha' => Carbon::parse($item['fecha'])->format('d/m/Y'),
                    'valor' => '$' . number_format($item['valor'], 2, ',', '.'),
                    'fuente' => ucfirst($item['fuente'])
                ];
            })->toArray();
            
            $this->table(['Fecha', 'Valor UF', 'Fuente'], $datos);
            
            $stats = $historial['estadisticas'];
            $this->info('📊 Estadísticas del período:');
            $this->line("   • Promedio: $" . number_format($stats['promedio'], 2, ',', '.'));
            $this->line("   • Máximo: $" . number_format($stats['maximo'], 2, ',', '.'));
            $this->line("   • Mínimo: $" . number_format($stats['minimo'], 2, ',', '.'));

        } catch (\Exception $e) {
            $this->error('❌ Error obteniendo historial: ' . $e->getMessage());
        }
    }

    private function mostrarEstadisticas()
    {
        $this->info('📊 Obteniendo estadísticas del servicio UF...');
        
        try {
            $stats = $this->ufService->obtenerEstadisticas();
            
            $this->info('📈 UF Actual vs Anterior:');
            $this->table(
                ['Período', 'Valor', 'Fuente'],
                [
                    [
                        'Hoy', 
                        '$' . number_format($stats['uf_actual']['valor'], 2, ',', '.'),
                        ucfirst($stats['uf_actual']['fuente'])
                    ],
                    [
                        'Ayer', 
                        '$' . number_format($stats['uf_anterior']['valor'], 2, ',', '.'),
                        ucfirst($stats['uf_anterior']['fuente'])
                    ]
                ]
            );

            $variacion = $stats['variacion'];
            $this->info('📊 Variación:');
            $this->line("   • Absoluta: $" . number_format($variacion['absoluta'], 2, ',', '.'));
            $this->line("   • Porcentual: " . number_format($variacion['porcentual'], 4) . "%");

            $this->info('🔧 Estado de servicios:');
            foreach ($stats['servicios_disponibles'] as $servicio => $disponible) {
                $estado = $disponible ? '✅' : '❌';
                $this->line("   • " . ucfirst($servicio) . ": {$estado}");
            }

            $cache = $stats['cache_info'];
            $this->info('💾 Cache:');
            $this->line("   • Activo: " . ($cache['activo'] ? 'Sí' : 'No'));
            $this->line("   • TTL: {$cache['ttl_minutos']} minutos");

        } catch (\Exception $e) {
            $this->error('❌ Error obteniendo estadísticas: ' . $e->getMessage());
        }
    }

    private function limpiarCache()
    {
        $this->info('🧹 Limpiando cache de UF...');
        
        try {
            $resultado = $this->ufService->limpiarCache();
            
            if ($resultado) {
                $this->info('✅ Cache limpiado exitosamente');
            } else {
                $this->warn('⚠️  No se pudo limpiar el cache completamente');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error limpiando cache: ' . $e->getMessage());
        }
    }
}
