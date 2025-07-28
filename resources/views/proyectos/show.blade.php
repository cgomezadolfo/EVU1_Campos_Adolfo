@extends('layout.app')

@section('title', 'Detalle del Proyecto')

@section('content')
<!-- Widget UF con conversión del monto del proyecto -->
@include('components.uf-widget', [
    'mostrarConversion' => true, 
    'montoProyecto' => $proyecto['monto'] ?? 0
])

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>👁️ Detalle del Proyecto #{{ $proyecto['id'] ?? 'N/A' }}</h2>
        <div class="actions">
            <a href="{{ route('proyectos.edit', $proyecto['id']) }}" class="btn btn-warning">
                ✏️ Editar
            </a>
            <a href="{{ route('proyectos.index') }}" class="btn btn-primary">
                🔙 Volver a la Lista
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(isset($proyecto))
    <!-- Información Principal -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h3 style="color: #667eea; margin-bottom: 1rem;">📝 Información General</h3>
            
            <div class="form-group">
                <label><strong>Nombre del Proyecto:</strong></label>
                <p style="background: #f8f9fa; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0;">
                    {{ $proyecto['nombre'] }}
                </p>
            </div>

            <div class="form-group">
                <label><strong>Descripción:</strong></label>
                <p style="background: #f8f9fa; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; line-height: 1.6;">
                    {{ $proyecto['descripcion'] ?? 'Sin descripción disponible' }}
                </p>
            </div>

            <div class="form-group">
                <label><strong>Responsable:</strong></label>
                <p style="background: #f8f9fa; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0;">
                    👤 {{ $proyecto['responsable'] ?? 'Sin asignar' }}
                </p>
            </div>
        </div>

        <div>
            <h3 style="color: #667eea; margin-bottom: 1rem;">📊 Estado y Datos</h3>
            
            <div class="form-group">
                <label><strong>Estado:</strong></label>
                <div style="margin: 0.5rem 0;">
                    @switch($proyecto['estado'])
                        @case('pendiente')
                            <span class="badge badge-pendiente" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                🟡 Pendiente
                            </span>
                            @break
                        @case('en_progreso')
                            <span class="badge badge-en_progreso" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                🔵 En Progreso
                            </span>
                            @break
                        @case('completado')
                            <span class="badge badge-completado" style="font-size: 1rem; padding: 0.5rem 1rem;">
                                🟢 Completado
                            </span>
                            @break
                        @default
                            <span class="badge" style="background: #6c757d; color: white; font-size: 1rem; padding: 0.5rem 1rem;">
                                {{ $proyecto['estado'] }}
                            </span>
                    @endswitch
                </div>
            </div>

            <div class="form-group">
                <label><strong>Monto del Proyecto:</strong></label>
                <p style="background: #e8f5e8; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; color: #2e7d32; font-size: 1.25rem; font-weight: bold;">
                    💰 ${{ number_format($proyecto['monto'] ?? 0, 2, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Fechas -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="color: #667eea; margin-bottom: 1rem;">📅 Cronograma</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <label><strong>Fecha de Inicio:</strong></label>
                <p style="margin: 0.5rem 0;">
                    🚀 {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('d/m/Y') }}
                    <br><small style="color: #666;">
                        {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('l, F j, Y') }}
                    </small>
                </p>
            </div>

            @if(isset($proyecto['fecha_fin']) && $proyecto['fecha_fin'])
            <div>
                <label><strong>Fecha de Finalización:</strong></label>
                <p style="margin: 0.5rem 0;">
                    🏁 {{ \Carbon\Carbon::parse($proyecto['fecha_fin'])->format('d/m/Y') }}
                    <br><small style="color: #666;">
                        {{ \Carbon\Carbon::parse($proyecto['fecha_fin'])->format('l, F j, Y') }}
                    </small>
                </p>
            </div>

            <div>
                <label><strong>Duración Estimada:</strong></label>
                <p style="margin: 0.5rem 0;">
                    ⏱️ {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->diffInDays(\Carbon\Carbon::parse($proyecto['fecha_fin'])) }} días
                </p>
            </div>
            @endif

            <div>
                <label><strong>Días Transcurridos:</strong></label>
                <p style="margin: 0.5rem 0;">
                    📈 {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->diffInDays(\Carbon\Carbon::now()) }} días
                    @if(\Carbon\Carbon::parse($proyecto['fecha_inicio'])->isFuture())
                        <br><small style="color: #ff9800;">(Proyecto aún no ha iniciado)</small>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Información Adicional -->
    @if(isset($informacion_adicional))
    <div style="background: #e3f2fd; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="color: #1976d2; margin-bottom: 1rem;">📈 Información Adicional</h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            @if(isset($informacion_adicional['duracion_planificada']))
            <div>
                <strong>Duración Planificada:</strong>
                <p>{{ $informacion_adicional['duracion_planificada'] }}</p>
            </div>
            @endif

            @if(isset($informacion_adicional['porcentaje_tiempo_transcurrido']))
            <div>
                <strong>Progreso Temporal:</strong>
                <p>{{ number_format($informacion_adicional['porcentaje_tiempo_transcurrido'], 1) }}%</p>
            </div>
            @endif

            @if(isset($informacion_adicional['estado_temporal']))
            <div>
                <strong>Estado Temporal:</strong>
                <p>
                    @switch($informacion_adicional['estado_temporal'])
                        @case('no_iniciado')
                            🔴 No Iniciado
                            @break
                        @case('en_curso')
                            🟡 En Curso
                            @break
                        @case('vencido')
                            🔴 Vencido
                            @break
                        @default
                            {{ $informacion_adicional['estado_temporal'] }}
                    @endswitch
                </p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Metadatos -->
    <div style="background: #f5f5f5; padding: 1rem; border-radius: 5px; color: #666; font-size: 0.875rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <strong>Fecha de Creación:</strong>
                {{ \Carbon\Carbon::parse($proyecto['created_at'])->format('d/m/Y H:i') }}
            </div>
            <div>
                <strong>Última Actualización:</strong>
                {{ \Carbon\Carbon::parse($proyecto['updated_at'])->format('d/m/Y H:i') }}
            </div>
            <div>
                <strong>ID del Proyecto:</strong>
                #{{ $proyecto['id'] }}
            </div>
        </div>
    </div>

    @else
    <div class="alert alert-error">
        <strong>Error:</strong> No se pudo cargar la información del proyecto.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Función para copiar ID del proyecto
        const projectId = '{{ $proyecto['id'] ?? '' }}';
        
        if (projectId) {
            console.log('Proyecto cargado:', {
                id: projectId,
                nombre: '{{ $proyecto['nombre'] ?? '' }}',
                estado: '{{ $proyecto['estado'] ?? '' }}',
                monto: {{ $proyecto['monto'] ?? 0 }}
            });
        }
    });
</script>
@endsection
