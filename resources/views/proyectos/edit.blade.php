@extends('layout.app')

@section('title', 'Editar Proyecto')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h2>✏️ Editar Proyecto #{{ $proyecto['id'] ?? 'N/A' }}</h2>
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

    @if($errors->any())
        <div class="alert alert-error">
            <strong>Error en la validación:</strong>
            <ul style="margin: 0.5rem 0 0 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($proyecto))
    <!-- Información actual del proyecto -->
    <div style="background: #e3f2fd; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
        <h4 style="color: #1976d2; margin-bottom: 0.5rem;">📋 Información Actual</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
            <div><strong>Estado:</strong> 
                @switch($proyecto['estado'])
                    @case('pendiente') 🟡 Pendiente @break
                    @case('en_progreso') 🔵 En Progreso @break
                    @case('completado') 🟢 Completado @break
                    @default {{ $proyecto['estado'] }}
                @endswitch
            </div>
            <div><strong>Responsable:</strong> {{ $proyecto['responsable'] ?? 'Sin asignar' }}</div>
            <div><strong>Monto:</strong> ${{ number_format($proyecto['monto'] ?? 0, 2, ',', '.') }}</div>
            <div><strong>Última actualización:</strong> {{ \Carbon\Carbon::parse($proyecto['updated_at'])->diffForHumans() }}</div>
        </div>
    </div>

    <form action="{{ route('proyectos.update', $proyecto['id']) }}" method="POST" id="editProjectForm">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nombre">Nombre del Proyecto *</label>
            <input type="text" 
                   class="form-control" 
                   id="nombre" 
                   name="nombre" 
                   value="{{ old('nombre', $proyecto['nombre']) }}" 
                   required 
                   maxlength="255"
                   placeholder="Ingrese el nombre del proyecto">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción *</label>
            <textarea class="form-control" 
                      id="descripcion" 
                      name="descripcion" 
                      rows="4" 
                      required
                      placeholder="Describa el proyecto detalladamente">{{ old('descripcion', $proyecto['descripcion']) }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio *</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_inicio" 
                       name="fecha_inicio" 
                       value="{{ old('fecha_inicio', \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('Y-m-d')) }}" 
                       required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha de Finalización</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_fin" 
                       name="fecha_fin" 
                       value="{{ old('fecha_fin', isset($proyecto['fecha_fin']) ? \Carbon\Carbon::parse($proyecto['fecha_fin'])->format('Y-m-d') : '') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="responsable">Responsable del Proyecto *</label>
                <input type="text" 
                       class="form-control" 
                       id="responsable" 
                       name="responsable" 
                       value="{{ old('responsable', $proyecto['responsable']) }}" 
                       required 
                       maxlength="255"
                       placeholder="Nombre completo del responsable">
            </div>

            <div class="form-group">
                <label for="monto">Monto del Proyecto *</label>
                <input type="number" 
                       class="form-control" 
                       id="monto" 
                       name="monto" 
                       value="{{ old('monto', $proyecto['monto']) }}" 
                       required 
                       min="0" 
                       step="0.01"
                       placeholder="0.00">
            </div>
        </div>

        <div class="form-group">
            <label for="estado">Estado del Proyecto *</label>
            <select class="form-control" id="estado" name="estado" required>
                <option value="">Seleccione un estado</option>
                <option value="pendiente" 
                        {{ old('estado', $proyecto['estado']) == 'pendiente' ? 'selected' : '' }}>
                    🟡 Pendiente
                </option>
                <option value="en_progreso" 
                        {{ old('estado', $proyecto['estado']) == 'en_progreso' ? 'selected' : '' }}>
                    🔵 En Progreso
                </option>
                <option value="completado" 
                        {{ old('estado', $proyecto['estado']) == 'completado' ? 'selected' : '' }}>
                    🟢 Completado
                </option>
            </select>
        </div>

        <!-- Cambios detectados -->
        <div id="cambiosDetectados" style="background: #fff3e0; padding: 1rem; border-radius: 5px; margin: 1rem 0; display: none;">
            <h4 style="color: #f57c00; margin-bottom: 0.5rem;">⚠️ Cambios Detectados</h4>
            <ul id="listaCambios" style="margin: 0; padding-left: 1.5rem;"></ul>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-success">
                💾 Actualizar Proyecto
            </button>
            <button type="button" class="btn btn-warning" onclick="resetearFormulario()">
                🔄 Resetear Cambios
            </button>
            <a href="{{ route('proyectos.show', $proyecto['id']) }}" class="btn btn-info">
                👁️ Ver sin Guardar
            </a>
        </div>
    </form>

    @else
    <div class="alert alert-error">
        <strong>Error:</strong> No se pudo cargar la información del proyecto para editar.
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');
        const monto = document.getElementById('monto');

        // Valores originales para detectar cambios
        const valoresOriginales = {
            nombre: '{{ $proyecto['nombre'] ?? '' }}',
            descripcion: `{{ $proyecto['descripcion'] ?? '' }}`,
            fecha_inicio: '{{ isset($proyecto['fecha_inicio']) ? \Carbon\Carbon::parse($proyecto['fecha_inicio'])->format('Y-m-d') : '' }}',
            fecha_fin: '{{ isset($proyecto['fecha_fin']) ? \Carbon\Carbon::parse($proyecto['fecha_fin'])->format('Y-m-d') : '' }}',
            responsable: '{{ $proyecto['responsable'] ?? '' }}',
            monto: '{{ $proyecto['monto'] ?? 0 }}',
            estado: '{{ $proyecto['estado'] ?? '' }}'
        };

        // Validar que fecha de fin sea posterior a fecha de inicio
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value <= this.value) {
                fechaFin.value = '';
            }
            fechaFin.min = this.value;
            detectarCambios();
        });

        fechaFin.addEventListener('change', detectarCambios);

        // Formatear monto mientras se escribe
        monto.addEventListener('input', function() {
            let value = this.value.replace(/[^\d.]/g, '');
            this.value = value;
            detectarCambios();
        });

        // Detectar cambios en todos los campos
        document.querySelectorAll('#editProjectForm input, #editProjectForm textarea, #editProjectForm select').forEach(function(campo) {
            campo.addEventListener('input', detectarCambios);
            campo.addEventListener('change', detectarCambios);
        });

        function detectarCambios() {
            const cambios = [];
            const form = document.getElementById('editProjectForm');
            const formData = new FormData(form);

            for (let [campo, valorOriginal] of Object.entries(valoresOriginales)) {
                const valorActual = formData.get(campo) || '';
                if (valorActual !== valorOriginal) {
                    cambios.push({
                        campo: campo,
                        original: valorOriginal,
                        nuevo: valorActual
                    });
                }
            }

            const contenedorCambios = document.getElementById('cambiosDetectados');
            const listaCambios = document.getElementById('listaCambios');

            if (cambios.length > 0) {
                contenedorCambios.style.display = 'block';
                listaCambios.innerHTML = '';
                
                cambios.forEach(cambio => {
                    const li = document.createElement('li');
                    li.innerHTML = `<strong>${formatearNombreCampo(cambio.campo)}:</strong> "${cambio.original}" → "${cambio.nuevo}"`;
                    listaCambios.appendChild(li);
                });
            } else {
                contenedorCambios.style.display = 'none';
            }
        }

        function formatearNombreCampo(campo) {
            const nombres = {
                'nombre': 'Nombre',
                'descripcion': 'Descripción',
                'fecha_inicio': 'Fecha de Inicio',
                'fecha_fin': 'Fecha de Fin',
                'responsable': 'Responsable',
                'monto': 'Monto',
                'estado': 'Estado'
            };
            return nombres[campo] || campo;
        }

        // Función global para resetear formulario
        window.resetearFormulario = function() {
            if (confirm('¿Está seguro que desea descartar todos los cambios?')) {
                for (let [campo, valor] of Object.entries(valoresOriginales)) {
                    const elemento = document.querySelector(`[name="${campo}"]`);
                    if (elemento) {
                        elemento.value = valor;
                    }
                }
                detectarCambios();
            }
        };

        // Validación del formulario
        document.getElementById('editProjectForm').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const responsable = document.getElementById('responsable').value.trim();
            const montoValue = parseFloat(document.getElementById('monto').value);

            if (!nombre || !descripcion || !responsable) {
                e.preventDefault();
                alert('Por favor, complete todos los campos obligatorios.');
                return;
            }

            if (isNaN(montoValue) || montoValue < 0) {
                e.preventDefault();
                alert('Por favor, ingrese un monto válido mayor o igual a 0.');
                return;
            }

            if (fechaFin.value && fechaFin.value <= fechaInicio.value) {
                e.preventDefault();
                alert('La fecha de finalización debe ser posterior a la fecha de inicio.');
                return;
            }

            // Confirmar si hay cambios importantes
            const cambiosImportantes = ['estado', 'monto', 'responsable'];
            const formData = new FormData(this);
            let hayCambiosImportantes = false;

            for (let campo of cambiosImportantes) {
                if (formData.get(campo) !== valoresOriginales[campo]) {
                    hayCambiosImportantes = true;
                    break;
                }
            }

            if (hayCambiosImportantes) {
                if (!confirm('Se detectaron cambios importantes (estado, monto o responsable). ¿Está seguro que desea continuar?')) {
                    e.preventDefault();
                    return;
                }
            }
        });
    });
</script>
@endsection
