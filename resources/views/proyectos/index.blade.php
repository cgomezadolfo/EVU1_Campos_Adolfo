@extends('layout.app')

@section('title', 'Lista de Proyectos')

@section('content')
<div class="card">
    <h2>📋 Lista de Proyectos</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filtros -->
    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
        <form method="GET" action="{{ route('proyectos.index') }}" id="filterForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="estado">Estado:</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>
                            🟡 Pendiente
                        </option>
                        <option value="en_progreso" {{ request('estado') == 'en_progreso' ? 'selected' : '' }}>
                            🔵 En Progreso
                        </option>
                        <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>
                            🟢 Completado
                        </option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="responsable">Responsable:</label>
                    <input type="text" 
                           class="form-control" 
                           id="responsable" 
                           name="responsable" 
                           value="{{ request('responsable') }}"
                           placeholder="Buscar por responsable">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="monto_min">Monto Mínimo:</label>
                    <input type="number" 
                           class="form-control" 
                           id="monto_min" 
                           name="monto_min" 
                           value="{{ request('monto_min') }}"
                           min="0" 
                           step="0.01"
                           placeholder="0.00">
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="monto_max">Monto Máximo:</label>
                    <input type="number" 
                           class="form-control" 
                           id="monto_max" 
                           name="monto_max" 
                           value="{{ request('monto_max') }}"
                           min="0" 
                           step="0.01"
                           placeholder="0.00">
                </div>
            </div>
            
            <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                <a href="{{ route('proyectos.index') }}" class="btn btn-primary">🔄 Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    @if(isset($estadisticas))
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: #e3f2fd; padding: 1rem; border-radius: 5px; text-align: center;">
            <h3 style="margin: 0; color: #1976d2;">{{ $estadisticas['total'] ?? 0 }}</h3>
            <p style="margin: 0; color: #666;">Total</p>
        </div>
        <div style="background: #fff3e0; padding: 1rem; border-radius: 5px; text-align: center;">
            <h3 style="margin: 0; color: #f57c00;">{{ $estadisticas['pendientes'] ?? 0 }}</h3>
            <p style="margin: 0; color: #666;">Pendientes</p>
        </div>
        <div style="background: #e1f5fe; padding: 1rem; border-radius: 5px; text-align: center;">
            <h3 style="margin: 0; color: #0277bd;">{{ $estadisticas['en_progreso'] ?? 0 }}</h3>
            <p style="margin: 0; color: #666;">En Progreso</p>
        </div>
        <div style="background: #e8f5e8; padding: 1rem; border-radius: 5px; text-align: center;">
            <h3 style="margin: 0; color: #388e3c;">{{ $estadisticas['completados'] ?? 0 }}</h3>
            <p style="margin: 0; color: #666;">Completados</p>
        </div>
    </div>
    @endif

    <!-- Tabla de proyectos -->
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Responsable</th>
                    <th>Fecha Inicio</th>
                    <th>Estado</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proyectos ?? [] as $proyecto)
                <tr>
                    <td><strong>#{{ $proyecto['id'] }}</strong></td>
                    <td>
                        <strong>{{ $proyecto['nombre'] }}</strong>
                        @if(isset($proyecto['descripcion']))
                            <br><small style="color: #666;">{{ Str::limit($proyecto['descripcion'], 50) }}</small>
                        @endif
                    </td>
                    <td>{{ $proyecto['responsable'] ?? 'Sin asignar' }}</td>
                    <td>{{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('d/m/Y') }}</td>
                    <td>
                        @switch($proyecto['estado'])
                            @case('pendiente')
                                <span class="badge badge-pendiente">🟡 Pendiente</span>
                                @break
                            @case('en_progreso')
                                <span class="badge badge-en_progreso">🔵 En Progreso</span>
                                @break
                            @case('completado')
                                <span class="badge badge-completado">🟢 Completado</span>
                                @break
                            @default
                                <span class="badge" style="background: #6c757d; color: white;">{{ $proyecto['estado'] }}</span>
                        @endswitch
                    </td>
                    <td class="monto">${{ number_format($proyecto['monto'] ?? 0, 2, ',', '.') }}</td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('proyectos.show', $proyecto['id']) }}" 
                               class="btn btn-info" 
                               style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                👁️ Ver
                            </a>
                            <a href="{{ route('proyectos.edit', $proyecto['id']) }}" 
                               class="btn btn-warning" 
                               style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                ✏️ Editar
                            </a>
                            <form method="POST" 
                                  action="{{ route('proyectos.destroy', $proyecto['id']) }}" 
                                  style="display: inline;"
                                  onsubmit="return confirmarEliminacion('{{ $proyecto['nombre'] }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-danger" 
                                        style="padding: 0.25rem 0.5rem; font-size: 0.875rem;">
                                    🗑️ Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: #666;">
                        <strong>No se encontraron proyectos</strong>
                        <br>
                        <a href="{{ route('proyectos.create') }}" class="btn btn-success" style="margin-top: 1rem;">
                            ➕ Crear Primer Proyecto
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Información adicional -->
    @if(isset($estadisticas) && $estadisticas['total'] > 0)
    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-top: 1.5rem;">
        <h4>📊 Resumen Financiero</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div>
                <strong>Monto Total:</strong> 
                <span class="monto">${{ number_format($estadisticas['monto_total'] ?? 0, 2, ',', '.') }}</span>
            </div>
            <div>
                <strong>Monto Promedio:</strong> 
                <span class="monto">${{ number_format($estadisticas['monto_promedio'] ?? 0, 2, ',', '.') }}</span>
            </div>
            <div>
                <strong>Responsables Únicos:</strong> 
                {{ $estadisticas['responsables_unicos'] ?? 0 }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit del formulario cuando cambian los filtros
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect) {
            estadoSelect.addEventListener('change', function() {
                document.getElementById('filterForm').submit();
            });
        }
        
        // Validación de montos
        const montoMin = document.getElementById('monto_min');
        const montoMax = document.getElementById('monto_max');
        
        if (montoMin && montoMax) {
            montoMin.addEventListener('input', function() {
                if (montoMax.value && parseFloat(this.value) > parseFloat(montoMax.value)) {
                    montoMax.value = this.value;
                }
            });
            
            montoMax.addEventListener('input', function() {
                if (montoMin.value && parseFloat(this.value) < parseFloat(montoMin.value)) {
                    montoMin.value = this.value;
                }
            });
        }
    });
</script>
@endsection
