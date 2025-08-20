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

### Sistema de Autenticación y Autorización ✅ ACTUALIZADO
- ✅ **Registro de Usuario** - `POST /api/auth/registro`
- ✅ **Inicio de Sesión con JWT** - `POST /api/auth/login`
- ✅ **Cerrar Sesión** - `POST /api/auth/logout`
- ✅ **Usuario Autenticado** - `GET /api/auth/usuario`
- ✅ **Refrescar Token JWT** - `POST /api/auth/refresh`

### Modelos y Seeders ✅ IMPLEMENTADO
- ✅ **Modelo Usuario**: ID, Nombre, Correo (único), Clave cifrada
- ✅ **Modelo Proyecto (Actualizado)**: ID, Nombre, Fecha de Inicio, Estado, Responsable, Monto, created_by
- ✅ **UserSeeder**: 8 usuarios de prueba con credenciales corporativas
- ✅ **ProyectoSeeder**: 8 proyectos con relaciones a usuarios creadores

## Tecnologías Utilizadas

- **Laravel Framework** - Framework PHP moderno
- **JWT Auth** - Sistema de autenticación con JSON Web Tokens
- **Laravel Sanctum** - Sistema de autenticación API con tokens
- **MySQL** - Base de datos relacional robusta
- **Eloquent ORM** - Para manejo de modelos y relaciones
- **Cifrado Hash** - Bcrypt para protección de contraseñas

## Instalación y Configuración

### Configuración de Base de Datos MySQL
1. **Crear la base de datos manualmente** en MySQL:
   ```sql
   CREATE DATABASE desarrollo_software_1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Configurar variables de entorno** en el archivo `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=desarrollo_software_1
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Comando personalizado para crear la base de datos** (opcional):
   ```bash
   php artisan db:crear
   ```

### Instalación del Proyecto
1. Navega al directorio del proyecto: `cd c:\laragon\www\ev-1-adolfoCampos-yerkoGuerra`
2. Instala las dependencias: `composer install`
3. Configura el archivo `.env` con la configuración de MySQL (ver arriba)
4. Ejecuta las migraciones: `php artisan migrate` o usa `php artisan db:crear`
5. (Opcional) Carga datos de ejemplo: `php artisan db:seed --class=ProyectoSeeder`
6. Inicia el servidor: `php artisan serve`

El servidor estará disponible en: `http://127.0.0.1:8000`

## Rutas Web Disponibles

### Rutas Públicas
- `GET /login` - Vista de inicio de sesión
- `GET /registro` - Vista de registro de usuario
- `GET /` - Redirección automática al dashboard o login

### Rutas Protegidas (requieren JWT)
- `GET /dashboard` - Panel principal del sistema

### Rutas API Públicas
- `POST /api/auth/registro` - Registro de usuario
- `POST /api/auth/login` - Inicio de sesión con JWT
- `POST /api/auth/refresh` - Refrescar token JWT

### Rutas API Protegidas (requieren JWT)
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/usuario` - Obtener usuario autenticado
- `GET /api/proyectos` - Listar proyectos
- `POST /api/proyectos` - Crear nuevo proyecto
- `GET /api/proyectos/{id}` - Obtener proyecto por ID
- `PUT /api/proyectos/{id}` - Actualizar proyecto
- `DELETE /api/proyectos/{id}` - Eliminar proyecto

## Comandos Artisan Personalizados

- `php artisan db:crear` - Crear la base de datos MySQL automáticamente
- `php artisan usuario:crear` - Crear usuarios de prueba interactivamente
- `php artisan db:seed` - Poblar la base de datos con usuarios y proyectos de prueba

## Estructura de la Base de Datos

### Tabla: users (Usuarios del Sistema)
- `id` - Clave primaria
- `name` - Nombre completo del usuario
- `email` - Correo electrónico único
- `password` - Contraseña cifrada con Hash
- `email_verified_at` - Fecha de verificación de email
- `last_login_at` - Fecha y hora del último acceso
- `last_login_ip` - Dirección IP del último acceso

### Tabla: proyectos (Proyectos del Sistema) ✅ ACTUALIZADA
- `id` - Clave primaria
- `nombre` - Nombre del proyecto
- `descripcion` - Descripción detallada
- `fecha_inicio` - Fecha de inicio del proyecto
- `fecha_fin` - Fecha de finalización (puede ser nula)
- `estado` - Estado del proyecto (pendiente, en_progreso, completado)
- `responsable` - Nombre del responsable del proyecto
- `monto` - Monto asignado al proyecto (decimal)
- `created_by` - ID del usuario que creó el proyecto (Foreign Key)

### Seeders de Datos de Prueba
Los seeders proporcionan datos realistas para desarrollo y pruebas:

**UserSeeder**: Crea 8 usuarios corporativos:
- ana.garcia@techsolutions.com
- carlos.rodriguez@techsolutions.com  
- maria.fernandez@techsolutions.com
- jose.torres@techsolutions.com
- laura.jimenez@techsolutions.com
- roberto.vasquez@techsolutions.com
- diana.morales@techsolutions.com
- fernando.castillo@techsolutions.com

**ProyectoSeeder**: Crea 8 proyectos empresariales:
- Sistema de Gestión Corporativa ($125,000)
- Aplicación Móvil de Ventas ($85,000)
- Portal Web de Clientes ($95,000)
- Sistema de Inventario ($110,000)
- Plataforma de E-learning ($140,000)
- Sistema de Facturación Electrónica ($75,000)
- Dashboard de Analíticas ($90,000)
- Sistema de Recursos Humanos ($160,000)

*Todos los usuarios tienen la contraseña: `MiClave123!`*
- `last_login_ip` - IP del último acceso
- `created_at` - Fecha de registro
- `updated_at` - Fecha de actualización

### Tabla: personal_access_tokens (Tokens de Autenticación)
- `id` - Clave primaria
- `tokenable_id` - ID del usuario propietario
- `name` - Nombre del token
- `token` - Token cifrado
- `abilities` - Permisos del token
- `expires_at` - Fecha de expiración

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

### Endpoints de Autenticación

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|---------------|
| POST | `/api/auth/registro` | Registrar nuevo usuario | No |
| POST | `/api/auth/login` | Iniciar sesión | No |
| POST | `/api/auth/logout` | Cerrar sesión | Sí |
| GET | `/api/auth/perfil` | Obtener perfil del usuario | Sí |

### Endpoints de Gestión de Proyectos

| Método | Endpoint | Descripción | Autenticación |
|--------|----------|-------------|---------------|
| GET | `/api/proyectos` | Obtener todos los proyectos | Opcional |
| POST | `/api/proyectos` | Crear un nuevo proyecto | Opcional |
| GET | `/api/proyectos/{id}` | Obtener un proyecto específico | Opcional |
| PUT | `/api/proyectos/{id}` | Actualizar un proyecto | Opcional |
| DELETE | `/api/proyectos/{id}` | Eliminar un proyecto | Opcional |

### Ejemplos de Uso - Autenticación

#### 1. Registrar Usuario
```json
POST /api/auth/registro
Content-Type: application/json

{
    "nombre": "Juan Pérez",
    "email": "juan@empresa.com",
    "password": "MiClave123!",
    "confirmar_password": "MiClave123!"
}
```

#### 2. Iniciar Sesión (Devuelve JWT si credenciales son correctas)
```json
POST /api/auth/login
Content-Type: application/json

{
    "email": "juan@empresa.com",
    "password": "MiClave123!"
}

// Respuesta exitosa con JWT:
{
    "exito": true,
    "mensaje": "Inicio de sesión exitoso",
    "datos": {
        "usuario": {...},
        "jwt_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "tipo_token": "Bearer",
        "expira_en": "60 minutos"
    }
}
```

#### 3. Usar JWT Token en Peticiones Protegidas
```json
GET /api/auth/usuario
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
```

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

#### Gestión de Proyectos:
- ✅ GET `/api/proyectos` - Lista proyectos con estadísticas completas (monto total, promedio, etc.)
- ✅ GET `/api/proyectos/{id}?detalle=true` - Obtiene proyecto con todos los campos nuevos
- ✅ POST `/api/proyectos` - Crea proyectos con responsable y monto
- ✅ Migración exitosa de campos `responsable` y `monto`
- ✅ Seeder actualizado con datos completos funcionando
- ✅ Todos los controladores específicos actualizados y funcionando
- ✅ Servidor funcionando en `http://127.0.0.1:8000`

#### Sistema de Autenticación: ✅ ACTUALIZADO CON JWT
- ✅ POST `/api/auth/registro` - Registro con cifrado de clave (StatusCode: 201) ✅ JWT devuelto
- ✅ POST `/api/auth/login` - Inicio de sesión devuelve JWT si credenciales son correctas (StatusCode: 200)
- ✅ POST `/api/auth/logout` - Cierre de sesión invalidando JWT
- ✅ GET `/api/auth/usuario` - Usuario autenticado con JWT
- ✅ POST `/api/auth/refresh` - Refrescar token JWT
- ✅ **Controlador de Autenticación** implementado con conexión a modelos
- ✅ **Función de Registro** con cifrado de clave implementada
- ✅ **Función de Inicio de Sesión** que devuelve JWT si credenciales son correctas
- ✅ Validaciones de seguridad implementadas
- ✅ Rate limiting configurado (5 registros/min, 10 logins/min)
- ✅ Logging de eventos de seguridad
- ✅ Migración de campos de seguimiento ejecutada
- ✅ POST `/api/proyectos` - Crea proyectos con responsable y monto
- ✅ Migración exitosa de campos `responsable` y `monto`
- ✅ Seeder actualizado con datos completos funcionando
- ✅ Todos los controladores específicos actualizados y funcionando
- ✅ Servidor funcionando en `http://127.0.0.1:8000`

**El sistema ahora cuenta con un Controlador de Autenticación completo que conecta las rutas con los modelos definidos, implementando:**
- ✅ **Función de Registro de Usuario** con cifrado de clave usando Hash::make()
- ✅ **Función de Inicio de Sesión** que devuelve JWT si las credenciales son correctas
- ✅ **Conexión directa con modelo User** para validaciones y operaciones
- ✅ **Sistema JWT** configurado y funcional con tymon/jwt-auth

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
