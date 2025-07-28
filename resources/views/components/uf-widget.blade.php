{{-- 
Componente reutilizable para mostrar información de la UF
Uso: @include('components.uf-widget', ['mostrarConversion' => true, 'montoProyecto' => 150000])
--}}

@php
    $ufService = app(\App\Services\UFService::class);
    $ufData = $ufService->obtenerUF();
    $estadisticas = $ufService->obtenerEstadisticas();
    
    $mostrarConversion = $mostrarConversion ?? false;
    $montoProyecto = $montoProyecto ?? null;
    $conversion = null;
    
    if ($mostrarConversion && $montoProyecto) {
        $conversion = $ufService->convertirCLPaUF($montoProyecto);
    }
@endphp

<div class="uf-widget" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                              color: white; 
                              padding: 1.5rem; 
                              border-radius: 12px; 
                              box-shadow: 0 8px 32px rgba(0,0,0,0.1);
                              margin-bottom: 1rem;
                              position: relative;
                              overflow: hidden;">
    
    <!-- Patrón de fondo decorativo -->
    <div style="position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; 
                background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/></svg>') repeat;
                animation: float 20s infinite linear;"></div>

    <div style="position: relative; z-index: 2;">
        <!-- Header del widget -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.5rem; font-weight: 600; display: flex; align-items: center;">
                💰 Unidad de Fomento (UF)
            </h3>
            <div style="font-size: 0.875rem; opacity: 0.9; text-align: right;">
                <div>📅 {{ \Carbon\Carbon::parse($ufData['fecha'])->format('d/m/Y') }}</div>
                <div style="font-size: 0.75rem; margin-top: 0.25rem;">
                    🔄 {{ ucfirst($ufData['fuente']) }}
                </div>
            </div>
        </div>

        <!-- Valor principal de la UF -->
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem;">
                Valor UF Hoy
            </div>
            <div style="font-size: 2.5rem; font-weight: bold; line-height: 1; margin-bottom: 0.5rem;">
                ${{ number_format($ufData['valor'], 2, ',', '.') }}
            </div>
            <div style="font-size: 0.875rem; opacity: 0.8;">
                {{ $ufData['success'] ? 'Actualizado' : 'Valor estimado' }}
                @if(isset($estadisticas['variacion']))
                    @if($estadisticas['variacion']['absoluta'] > 0)
                        <span style="color: #4ade80; margin-left: 0.5rem;">
                            ↗️ +${{ number_format($estadisticas['variacion']['absoluta'], 2, ',', '.') }}
                        </span>
                    @elseif($estadisticas['variacion']['absoluta'] < 0)
                        <span style="color: #f87171; margin-left: 0.5rem;">
                            ↘️ ${{ number_format($estadisticas['variacion']['absoluta'], 2, ',', '.') }}
                        </span>
                    @else
                        <span style="color: #fbbf24; margin-left: 0.5rem;">→ Sin cambios</span>
                    @endif
                @endif
            </div>
        </div>

        @if($mostrarConversion && $conversion)
        <!-- Sección de conversión -->
        <div style="background: rgba(255,255,255,0.15); 
                    padding: 1rem; 
                    border-radius: 8px; 
                    margin-bottom: 1rem;
                    backdrop-filter: blur(10px);">
            <h4 style="margin: 0 0 0.75rem 0; font-size: 1rem; display: flex; align-items: center;">
                🔄 Conversión del Proyecto
            </h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.875rem;">
                <div>
                    <div style="opacity: 0.9; margin-bottom: 0.25rem;">Monto en Pesos:</div>
                    <div style="font-weight: bold; font-size: 1.1rem;">
                        ${{ number_format($conversion['monto_clp'], 0, ',', '.') }}
                    </div>
                </div>
                <div>
                    <div style="opacity: 0.9; margin-bottom: 0.25rem;">Equivalente en UF:</div>
                    <div style="font-weight: bold; font-size: 1.1rem; color: #fbbf24;">
                        {{ number_format($conversion['monto_uf'], 4, ',', '.') }} UF
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Estadísticas adicionales -->
        @if(isset($estadisticas['uf_anterior']))
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.75rem; opacity: 0.9;">
            <div>
                <div style="margin-bottom: 0.25rem;">UF Anterior:</div>
                <div style="font-weight: 600;">
                    ${{ number_format($estadisticas['uf_anterior']['valor'], 2, ',', '.') }}
                </div>
            </div>
            <div>
                <div style="margin-bottom: 0.25rem;">Variación %:</div>
                <div style="font-weight: 600;">
                    @if($estadisticas['variacion']['porcentual'] > 0)
                        <span style="color: #4ade80;">+{{ number_format($estadisticas['variacion']['porcentual'], 4) }}%</span>
                    @elseif($estadisticas['variacion']['porcentual'] < 0)
                        <span style="color: #f87171;">{{ number_format($estadisticas['variacion']['porcentual'], 4) }}%</span>
                    @else
                        <span style="color: #fbbf24;">0.0000%</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- Footer con acciones -->
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid rgba(255,255,255,0.2);">
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem;">
                <div style="opacity: 0.8;">
                    Última actualización: {{ \Carbon\Carbon::parse($ufData['timestamp'])->diffForHumans() }}
                </div>
                <div>
                    <button onclick="actualizarUF()" 
                            style="background: rgba(255,255,255,0.2); 
                                   border: 1px solid rgba(255,255,255,0.3); 
                                   color: white; 
                                   padding: 0.25rem 0.75rem; 
                                   border-radius: 4px; 
                                   font-size: 0.75rem;
                                   cursor: pointer;
                                   transition: all 0.3s ease;">
                        🔄 Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para funcionalidad del widget -->
<script>
function actualizarUF() {
    const widget = document.querySelector('.uf-widget');
    const boton = event.target;
    
    // Mostrar estado de carga
    boton.innerHTML = '⏳ Actualizando...';
    boton.disabled = true;
    
    // Simular carga
    fetch('/api/uf/actual?estadisticas=true')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar la página para mostrar nuevos datos
                location.reload();
            } else {
                throw new Error(data.error || 'Error desconocido');
            }
        })
        .catch(error => {
            console.error('Error actualizando UF:', error);
            alert('Error al actualizar la UF: ' + error.message);
        })
        .finally(() => {
            boton.innerHTML = '🔄 Actualizar';
            boton.disabled = false;
        });
}

// Animación de flotación
const style = document.createElement('style');
style.textContent = `
    @keyframes float {
        0% { transform: translateX(-100px) translateY(-100px); }
        100% { transform: translateX(100px) translateY(100px); }
    }
    
    .uf-widget button:hover {
        background: rgba(255,255,255,0.3) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
`;
document.head.appendChild(style);
</script>

<style>
.uf-widget {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>
