@extends('layout.app')

@section('title', 'Eliminar Proyecto')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>🗑️ Eliminar Proyecto #{{ $proyecto['id'] ?? 'N/A' }}</h2>
        <div class="actions">
            <a href="{{ route('proyectos.show', $proyecto['id']) }}" class="btn btn-info">
                👁️ Ver Detalle
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

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if(isset($proyecto))
    <!-- Advertencia de eliminación -->
    <div style="background: #ffebee; border: 2px solid #f44336; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="color: #d32f2f; margin-bottom: 1rem;">⚠️ ADVERTENCIA</h3>
        <p style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 1rem;">
            <strong>Está a punto de eliminar permanentemente este proyecto.</strong>
        </p>
        <p style="color: #666; margin-bottom: 0;">
            Esta acción no se puede deshacer. Se perderá toda la información asociada al proyecto.
        </p>
    </div>

    <!-- Información del proyecto a eliminar -->
    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="color: #495057; margin-bottom: 1rem;">📋 Información del Proyecto a Eliminar</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <div>
                <div class="form-group">
                    <label><strong>Nombre del Proyecto:</strong></label>
                    <p style="background: white; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; border: 1px solid #dee2e6;">
                        {{ $proyecto['nombre'] }}
                    </p>
                </div>

                <div class="form-group">
                    <label><strong>Responsable:</strong></label>
                    <p style="background: white; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; border: 1px solid #dee2e6;">
                        👤 {{ $proyecto['responsable'] ?? 'Sin asignar' }}
                    </p>
                </div>

                <div class="form-group">
                    <label><strong>Fecha de Inicio:</strong></label>
                    <p style="background: white; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; border: 1px solid #dee2e6;">
                        📅 {{ \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label><strong>Estado Actual:</strong></label>
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
                    <p style="background: #ffebee; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; color: #d32f2f; font-size: 1.25rem; font-weight: bold; border: 1px solid #f5c6cb;">
                        💰 ${{ number_format($proyecto['monto'] ?? 0, 2, ',', '.') }}
                    </p>
                </div>

                <div class="form-group">
                    <label><strong>Creado:</strong></label>
                    <p style="background: white; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; border: 1px solid #dee2e6;">
                        🕒 {{ \Carbon\Carbon::parse($proyecto['created_at'])->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label><strong>Descripción:</strong></label>
            <p style="background: white; padding: 0.75rem; border-radius: 5px; margin: 0.5rem 0; border: 1px solid #dee2e6; line-height: 1.6;">
                {{ $proyecto['descripcion'] ?? 'Sin descripción disponible' }}
            </p>
        </div>
    </div>

    <!-- Verificaciones de seguridad -->
    <div style="background: #fff3e0; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <h3 style="color: #f57c00; margin-bottom: 1rem;">🔍 Verificaciones de Seguridad</h3>
        
        <div id="verificacionesSeguridad">
            <!-- Se llenará con JavaScript -->
        </div>
    </div>

    <!-- Formulario de eliminación -->
    <div style="background: white; border: 2px solid #f44336; padding: 1.5rem; border-radius: 8px;">
        <h3 style="color: #d32f2f; margin-bottom: 1rem;">✋ Confirmación de Eliminación</h3>
        
        <div class="form-group">
            <label for="confirmacion" style="color: #d32f2f;">
                <strong>Para confirmar la eliminación, escriba exactamente: "ELIMINAR PROYECTO"</strong>
            </label>
            <input type="text" 
                   class="form-control" 
                   id="confirmacion" 
                   placeholder="Escriba: ELIMINAR PROYECTO"
                   style="border-color: #f44336;">
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" id="entiendeRiesgos" style="margin-right: 0.5rem;">
                <strong style="color: #d32f2f;">Entiendo que esta acción es irreversible y que se perderán todos los datos del proyecto</strong>
            </label>
        </div>

        <div class="form-group" id="opcionForzar" style="display: none;">
            <label>
                <input type="checkbox" id="forzarEliminacion" style="margin-right: 0.5rem;">
                <strong style="color: #ff5722;">Forzar eliminación (incluso si el proyecto está en progreso)</strong>
            </label>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: space-between;">
            <div>
                <form method="POST" 
                      action="{{ route('proyectos.destroy', $proyecto['id']) }}" 
                      id="deleteForm"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="forzar" id="forzarInput" value="false">
                    <button type="submit" 
                            class="btn btn-danger" 
                            id="btnEliminar"
                            disabled
                            style="font-size: 1.1rem; padding: 1rem 2rem;">
                        🗑️ ELIMINAR PROYECTO PERMANENTEMENTE
                    </button>
                </form>
            </div>
            
            <div>
                <a href="{{ route('proyectos.show', $proyecto['id']) }}" class="btn btn-success" style="font-size: 1.1rem; padding: 1rem 2rem;">
                    🛡️ CANCELAR Y MANTENER PROYECTO
                </a>
            </div>
        </div>
    </div>

    @else
    <div class="alert alert-error">
        <strong>Error:</strong> No se pudo cargar la información del proyecto para eliminar.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const confirmacion = document.getElementById('confirmacion');
        const entiendeRiesgos = document.getElementById('entiendeRiesgos');
        const btnEliminar = document.getElementById('btnEliminar');
        const forzarEliminacion = document.getElementById('forzarEliminacion');
        const forzarInput = document.getElementById('forzarInput');
        const opcionForzar = document.getElementById('opcionForzar');

        // Información del proyecto
        const proyecto = {
            estado: '{{ $proyecto['estado'] ?? '' }}',
            nombre: '{{ $proyecto['nombre'] ?? '' }}',
            monto: {{ $proyecto['monto'] ?? 0 }}
        };

        // Verificaciones de seguridad
        function realizarVerificaciones() {
            const verificaciones = [];
            
            if (proyecto.estado === 'en_progreso') {
                verificaciones.push({
                    tipo: 'warning',
                    mensaje: '⚠️ El proyecto está actualmente EN PROGRESO',
                    descripcion: 'Eliminar un proyecto en progreso puede afectar operaciones en curso.'
                });
                opcionForzar.style.display = 'block';
            }

            if (proyecto.monto > 100000) {
                verificaciones.push({
                    tipo: 'warning',
                    mensaje: '💰 El proyecto tiene un monto elevado (' + formatCurrency(proyecto.monto) + ')',
                    descripcion: 'Se recomienda revisar el impacto financiero antes de eliminar.'
                });
            }

            if (proyecto.estado === 'completado') {
                verificaciones.push({
                    tipo: 'info',
                    mensaje: '✅ El proyecto está COMPLETADO',
                    descripcion: 'Considera archivar en lugar de eliminar para mantener historial.'
                });
            }

            const contenedor = document.getElementById('verificacionesSeguridad');
            if (verificaciones.length === 0) {
                contenedor.innerHTML = '<p style="color: #28a745;">✅ No se detectaron problemas de seguridad para la eliminación.</p>';
            } else {
                contenedor.innerHTML = verificaciones.map(v => 
                    `<div style="padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 5px; background: ${v.tipo === 'warning' ? '#fff3e0' : '#e3f2fd'}; border: 1px solid ${v.tipo === 'warning' ? '#ffcc02' : '#2196f3'};">
                        <strong>${v.mensaje}</strong><br>
                        <small>${v.descripcion}</small>
                    </div>`
                ).join('');
            }
        }

        // Validar condiciones para habilitar botón
        function validarCondiciones() {
            const textoConfirmacion = confirmacion.value.trim();
            const riesgosEntendidos = entiendeRiesgos.checked;
            const textoValido = textoConfirmacion === 'ELIMINAR PROYECTO';

            btnEliminar.disabled = !(textoValido && riesgosEntendidos);
            
            if (textoValido && riesgosEntendidos) {
                btnEliminar.style.background = '#d32f2f';
                btnEliminar.style.borderColor = '#d32f2f';
            } else {
                btnEliminar.style.background = '#ccc';
                btnEliminar.style.borderColor = '#ccc';
            }
        }

        // Event listeners
        confirmacion.addEventListener('input', validarCondiciones);
        entiendeRiesgos.addEventListener('change', validarCondiciones);

        if (forzarEliminacion) {
            forzarEliminacion.addEventListener('change', function() {
                forzarInput.value = this.checked ? 'true' : 'false';
            });
        }

        // Validación final del formulario
        document.getElementById('deleteForm').addEventListener('submit', function(e) {
            const textoConfirmacion = confirmacion.value.trim();
            
            if (textoConfirmacion !== 'ELIMINAR PROYECTO') {
                e.preventDefault();
                alert('Debe escribir exactamente "ELIMINAR PROYECTO" para confirmar.');
                return;
            }

            if (!entiendeRiesgos.checked) {
                e.preventDefault();
                alert('Debe confirmar que entiende que esta acción es irreversible.');
                return;
            }

            const mensaje = proyecto.estado === 'en_progreso' && forzarEliminacion.checked
                ? `¿Está ABSOLUTAMENTE SEGURO que desea eliminar el proyecto "${proyecto.nombre}"?\n\nEste proyecto está EN PROGRESO y la eliminación será FORZADA.\n\nEsta acción NO SE PUEDE DESHACER.`
                : `¿Está seguro que desea eliminar permanentemente el proyecto "${proyecto.nombre}"?\n\nEsta acción NO SE PUEDE DESHACER.`;

            if (!confirm(mensaje)) {
                e.preventDefault();
                return;
            }

            // Doble confirmación para proyectos importantes
            if (proyecto.monto > 100000 || proyecto.estado === 'en_progreso') {
                if (!confirm('ÚLTIMA CONFIRMACIÓN:\n\n¿Realmente desea continuar con la eliminación?')) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Función para formatear moneda
        function formatCurrency(amount) {
            return new Intl.NumberFormat('es-CL', {
                style: 'currency',
                currency: 'CLP'
            }).format(amount);
        }

        // Ejecutar verificaciones al cargar
        realizarVerificaciones();
        validarCondiciones();
    });
</script>
@endsection
