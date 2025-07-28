@extends('layout.app')

@section('title', 'Crear Proyecto')

@section('content')
<!-- Widget UF para referencia en creación de proyectos -->
@include('components.uf-widget')

<div class="card">
    <h2>➕ Crear Nuevo Proyecto</h2>
    
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

    <form action="{{ route('proyectos.store') }}" method="POST" id="createProjectForm">
        @csrf
        
        <div class="form-group">
            <label for="nombre">Nombre del Proyecto *</label>
            <input type="text" 
                   class="form-control" 
                   id="nombre" 
                   name="nombre" 
                   value="{{ old('nombre') }}" 
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
                      placeholder="Describa el proyecto detalladamente">{{ old('descripcion') }}</textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="fecha_inicio">Fecha de Inicio *</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_inicio" 
                       name="fecha_inicio" 
                       value="{{ old('fecha_inicio') }}" 
                       required>
            </div>

            <div class="form-group">
                <label for="fecha_fin">Fecha de Finalización</label>
                <input type="date" 
                       class="form-control" 
                       id="fecha_fin" 
                       name="fecha_fin" 
                       value="{{ old('fecha_fin') }}">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label for="responsable">Responsable del Proyecto *</label>
                <input type="text" 
                       class="form-control" 
                       id="responsable" 
                       name="responsable" 
                       value="{{ old('responsable') }}" 
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
                       value="{{ old('monto') }}" 
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
                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>
                    🟡 Pendiente
                </option>
                <option value="en_progreso" {{ old('estado') == 'en_progreso' ? 'selected' : '' }}>
                    🔵 En Progreso
                </option>
                <option value="completado" {{ old('estado') == 'completado' ? 'selected' : '' }}>
                    🟢 Completado
                </option>
            </select>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-success">
                💾 Crear Proyecto
            </button>
            <a href="{{ route('proyectos.index') }}" class="btn btn-primary">
                🔙 Volver a la Lista
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fechaInicio = document.getElementById('fecha_inicio');
        const fechaFin = document.getElementById('fecha_fin');
        const monto = document.getElementById('monto');

        // Validar que fecha de fin sea posterior a fecha de inicio
        fechaInicio.addEventListener('change', function() {
            if (fechaFin.value && fechaFin.value <= this.value) {
                fechaFin.value = '';
            }
            fechaFin.min = this.value;
        });

        // Formatear monto mientras se escribe
        monto.addEventListener('input', function() {
            let value = this.value.replace(/[^\d.]/g, '');
            this.value = value;
        });

        // Validación del formulario
        document.getElementById('createProjectForm').addEventListener('submit', function(e) {
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
        });
    });
</script>
@endsection
