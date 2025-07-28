# Sistema de Gestión de Proyectos - Tech Solutions

## Descripción del Proyecto

Este es un sistema moderno de gestión de proyectos desarrollado con Laravel para la empresa Tech Solutions. El sistema permite gestionar proyectos a través de una API REST completa.

## Características Implementadas

### API de Gestión de Proyectos
- ✅ **Listar todos los proyectos** - `GET /api/proyectos`
- ✅ **Agregar nuevo proyecto** - `POST /api/proyectos`
- ✅ **Obtener proyecto por ID** - `GET /api/proyectos/{id}`
- ✅ **Actualizar proyecto por ID** - `PUT /api/proyectos/{id}`
- ✅ **Eliminar proyecto por ID** - `DELETE /api/proyectos/{id}`

## Tecnologías Utilizadas

- **Laravel Framework** - Framework PHP moderno
- **SQLite** - Base de datos ligera para desarrollo
- **Eloquent ORM** - Para manejo de modelos y relaciones

## Instalación y Configuración

1. Navega al directorio del proyecto: `cd c:\laragon\www\ev-1-adolfoCampos-yerkoGuerra`
2. Instala las dependencias: `composer install`
3. Configura el archivo `.env` con la configuración de base de datos
4. Ejecuta las migraciones: `php artisan migrate`
5. (Opcional) Carga datos de ejemplo: `php artisan db:seed --class=ProyectoSeeder`
6. Inicia el servidor: `php artisan serve`

El servidor estará disponible en: `http://127.0.0.1:8000`

## Estructura de la Base de Datos

### Tabla: proyectos
- `id` - Clave primaria
- `nombre` - Nombre del proyecto
- `descripcion` - Descripción del proyecto
- `fecha_inicio` - Fecha de inicio
- `fecha_fin` - Fecha de finalización
- `estado` - Estado del proyecto (pendiente, en_progreso, completado)
- `responsable` - Responsable del proyecto
- `monto` - Monto del proyecto (decimal)
- `created_at` - Fecha de creación
- `updated_at` - Fecha de actualización

## Uso de la API

### Endpoints Disponibles

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/proyectos` | Obtener todos los proyectos |
| POST | `/api/proyectos` | Crear un nuevo proyecto |
| GET | `/api/proyectos/{id}` | Obtener un proyecto específico |
| PUT | `/api/proyectos/{id}` | Actualizar un proyecto |
| DELETE | `/api/proyectos/{id}` | Eliminar un proyecto |

### Ejemplo de Estructura JSON para Proyecto

```json
{
    "nombre": "Proyecto Web Corporativo",
    "descripcion": "Desarrollo de sitio web para empresa",
    "fecha_inicio": "2025-01-15",
    "fecha_fin": "2025-03-15",
    "estado": "en_progreso",
    "responsable": "María García López",
    "monto": 125000.00
}
```

## Desarrollo

Este proyecto está en desarrollo activo. Se irán agregando nuevas funcionalidades según los requerimientos.

## Estado del Proyecto

✅ **COMPLETADO** - API de Gestión de Proyectos implementada con éxito

### Funcionalidades Implementadas:
- [x] **Modelo `Proyecto` actualizado** con nuevos campos:
  - [x] `responsable` - Campo para el responsable del proyecto
  - [x] `monto` - Campo decimal para el monto del proyecto
  - [x] Métodos estáticos con datos de ejemplo
  - [x] Scopes adicionales para filtrado
  - [x] Accessors para formateo de datos
- [x] **Migración de base de datos** con tabla `proyectos` actualizada
- [x] **Controladores específicos actualizados:**
  - [x] `CrearProyectoController` - Validaciones para nuevos campos
  - [x] `ObtenerProyectosController` - Filtros por responsable y monto
  - [x] `ObtenerProyectoPorIdController` - Información completa de proyectos
  - [x] `ActualizarProyectoController` - Actualización de todos los campos
  - [x] `EliminarProyectoController` - Mantenido sin cambios
- [x] **Seeder actualizado** con datos completos incluyendo responsables y montos
- [x] Validaciones de entrada actualizadas para todos los campos
- [x] Respuestas JSON estructuradas con estadísticas mejoradas

### Controladores Específicos Creados:

#### 1. CrearProyectoController
- **Endpoint**: `POST /api/proyectos`
- **Función**: Crear nuevos proyectos con validaciones completas
- **Características**: Validación de estados, fechas y campos obligatorios

#### 2. ObtenerProyectosController  
- **Endpoint**: `GET /api/proyectos`
- **Función**: Listar proyectos con filtros opcionales
- **Características**: Filtros por estado, fechas, ordenamiento y estadísticas

#### 3. ObtenerProyectoPorIdController
- **Endpoint**: `GET /api/proyectos/{id}`
- **Función**: Obtener proyecto específico por ID
- **Características**: Información adicional opcional (duración, progreso temporal)

#### 4. ActualizarProyectoController
- **Endpoint**: `PUT /api/proyectos/{id}`
- **Función**: Actualizar proyectos existentes
- **Características**: Actualización parcial, historial de cambios

#### 5. EliminarProyectoController
- **Endpoint**: `DELETE /api/proyectos/{id}`
- **Función**: Eliminar proyectos con validaciones
- **Características**: Protección contra eliminación de proyectos en progreso

### Endpoints Adicionales:
- `GET /api/proyectos/{id}/confirmar-eliminacion` - Verificar antes de eliminar
- `GET /api/proyectos/{id}/verificar` - Verificar existencia de proyecto

### Pruebas Realizadas:
- ✅ GET `/api/proyectos` - Lista proyectos con estadísticas completas (monto total, promedio, etc.)
- ✅ GET `/api/proyectos/{id}?detalle=true` - Obtiene proyecto con todos los campos nuevos
- ✅ POST `/api/proyectos` - Crea proyectos con responsable y monto
- ✅ Migración exitosa de campos `responsable` y `monto`
- ✅ Seeder actualizado con datos completos funcionando
- ✅ Todos los controladores específicos actualizados y funcionando
- ✅ Servidor funcionando en `http://127.0.0.1:8000`

**El modelo Proyecto ha sido actualizado exitosamente con los nuevos campos solicitados: ID, Nombre, Fecha de Inicio, Estado, Responsable y Monto. Todos los controladores han sido actualizados para manejar estos campos correctamente.**

## Interfaz Web Implementada

### Vistas Blade Creadas:
- ✅ **Vista de listado** (`resources/views/proyectos/index.blade.php`) - Lista todos los proyectos con filtros
- ✅ **Vista de creación** (`resources/views/proyectos/create.blade.php`) - Formulario para crear proyectos
- ✅ **Vista de detalle** (`resources/views/proyectos/show.blade.php`) - Ver información completa del proyecto
- ✅ **Vista de edición** (`resources/views/proyectos/edit.blade.php`) - Formulario para editar proyectos
- ✅ **Vista de eliminación** (`resources/views/proyectos/delete.blade.php`) - Confirmación segura para eliminar
- ✅ **Layout principal** (`resources/views/layout/app.blade.php`) - Plantilla base con CSS moderno

### Características de las Vistas:
- 🎨 Diseño responsivo con CSS moderno
- 🛡️ Validaciones de seguridad (especialmente en eliminación)
- 📱 Interfaz intuitiva con iconos y colores
- ⚡ JavaScript interactivo para mejor experiencia
- 🔄 Integración completa con el sistema existente

### Próximos Pasos Recomendados:
1. **Crear rutas web** en `routes/web.php` para servir estas vistas
2. **Actualizar controladores** para retornar vistas en lugar de JSON para requests web  
3. **Probar la interfaz** completa con todas las funcionalidades
4. **Implementar middleware** de autenticación si es necesario
5. **Agregar componentes reutilizables** para funcionalidades específicas

## Componentes Adicionales

### Componente UF (Unidad de Fomento) ✅ IMPLEMENTADO
- 📊 **Servicio UF** - Componente reutilizable que consume API externa para obtener el valor de la UF del día
- 🔄 **Actualización automática** - Se actualiza diariamente con el valor oficial
- 💰 **Conversión de montos** - Permite convertir montos de proyectos a UF
- ⚡ **Cache inteligente** - Almacena valores para evitar consultas innecesarias

#### Funcionalidades del Servicio UF:
- ✅ **Obtención de UF actual** desde múltiples fuentes (MinHacienda, SBIF, Banco Central)
- ✅ **Conversión CLP ↔ UF** con cálculos automáticos
- ✅ **Historial de UF** para análisis de tendencias
- ✅ **Cache inteligente** con TTL de 24 horas
- ✅ **Fallback robusto** con múltiples APIs de respaldo
- ✅ **Widget visual** integrado en vistas de proyectos
- ✅ **Comando Artisan** para testing y administración

#### Endpoints UF Disponibles:
| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/api/uf/actual` | Obtener valor actual de UF |
| POST | `/api/uf/convertir/clp-uf` | Convertir CLP a UF |
| POST | `/api/uf/convertir/uf-clp` | Convertir UF a CLP |
| GET | `/api/uf/historial` | Obtener historial de UF |
| GET | `/api/uf/estadisticas` | Estadísticas del servicio |
| DELETE | `/api/uf/cache` | Limpiar cache de UF |

#### Comando Artisan UF:
```bash
# Obtener UF actual con estadísticas
php artisan uf:test --estadisticas

# Convertir monto de CLP a UF
php artisan uf:test --convertir=150000

# Ver historial de últimos 7 días
php artisan uf:test --historial

# Limpiar cache de UF
php artisan uf:test --limpiar-cache
```

#### Uso del Widget UF en Blade:
```blade
{{-- Widget básico --}}
@include('components.uf-widget')

{{-- Widget con conversión de monto --}}
@include('components.uf-widget', [
    'mostrarConversion' => true, 
    'montoProyecto' => 150000
])
```

#### APIs Externas Soportadas:
- 🟢 **MinHacienda** (https://mindicador.cl) - Principal, sin autenticación
- 🟡 **SBIF** - Requiere API key (configurable)
- 🟡 **Banco Central** - Requiere credenciales (configurable)
