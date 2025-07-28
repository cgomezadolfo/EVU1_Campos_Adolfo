<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importar todos los controladores específicos
use App\Http\Controllers\CrearProyectoController;
use App\Http\Controllers\ObtenerProyectosController;
use App\Http\Controllers\ActualizarProyectoController;
use App\Http\Controllers\EliminarProyectoController;
use App\Http\Controllers\ObtenerProyectoPorIdController;
use App\Http\Controllers\UFController;

// Ruta para obtener información del usuario autenticado (ruta por defecto de Laravel)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas específicas para la API de gestión de proyectos
Route::prefix('proyectos')->group(function () {
    
    // 1. Crear un nuevo proyecto
    Route::post('/', [CrearProyectoController::class, 'crear'])
        ->name('proyectos.crear');
    
    // 2. Obtener todos los proyectos (con filtros opcionales)
    Route::get('/', [ObtenerProyectosController::class, 'obtenerTodos'])
        ->name('proyectos.obtener-todos');
    
    // 3. Obtener un proyecto específico por ID
    Route::get('/{id}', [ObtenerProyectoPorIdController::class, 'obtenerPorId'])
        ->name('proyectos.obtener-por-id')
        ->where('id', '[0-9]+');
    
    // 4. Actualizar un proyecto por ID
    Route::put('/{id}', [ActualizarProyectoController::class, 'actualizar'])
        ->name('proyectos.actualizar')
        ->where('id', '[0-9]+');
    
    // 5. Eliminar un proyecto por ID
    Route::delete('/{id}', [EliminarProyectoController::class, 'eliminar'])
        ->name('proyectos.eliminar')
        ->where('id', '[0-9]+');

    // Rutas adicionales para funcionalidades extendidas
    
    // Confirmar eliminación (soft check)
    Route::get('/{id}/confirmar-eliminacion', [EliminarProyectoController::class, 'confirmarEliminacion'])
        ->name('proyectos.confirmar-eliminacion')
        ->where('id', '[0-9]+');
    
    // Verificar existencia de proyecto (usando GET en lugar de HEAD)
    Route::get('/{id}/verificar', [ObtenerProyectoPorIdController::class, 'verificarExistencia'])
        ->name('proyectos.verificar-existencia')
        ->where('id', '[0-9]+');
});

// Rutas para el servicio de Unidad de Fomento (UF)
Route::prefix('uf')->group(function () {
    
    // Obtener valor actual de la UF
    Route::get('/actual', [UFController::class, 'obtenerUFActual'])
        ->name('uf.actual');
    
    // Convertir CLP a UF
    Route::post('/convertir/clp-uf', [UFController::class, 'convertirCLPaUF'])
        ->name('uf.convertir-clp-uf');
    
    // Convertir UF a CLP
    Route::post('/convertir/uf-clp', [UFController::class, 'convertirUFaCLP'])
        ->name('uf.convertir-uf-clp');
    
    // Obtener historial de UF
    Route::get('/historial', [UFController::class, 'obtenerHistorial'])
        ->name('uf.historial');
    
    // Obtener estadísticas del servicio
    Route::get('/estadisticas', [UFController::class, 'obtenerEstadisticas'])
        ->name('uf.estadisticas');
    
    // Limpiar cache de UF
    Route::delete('/cache', [UFController::class, 'limpiarCache'])
        ->name('uf.limpiar-cache');
});
