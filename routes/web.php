<?php

use Illuminate\Support\Facades\Route;

// Ruta raíz - redirigir al dashboard si está autenticado, sino al login
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Rutas de autenticación (públicas)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('auth.login');
    
    Route::get('/registro', function () {
        return view('auth.registro');
    })->name('auth.registro');
});

// Rutas protegidas por JWT
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Ruta para cerrar sesión
Route::post('/logout', function () {
    return redirect()->route('auth.login');
})->name('logout');
