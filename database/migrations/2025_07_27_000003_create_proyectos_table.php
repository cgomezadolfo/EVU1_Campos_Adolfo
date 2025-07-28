<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para crear la tabla proyectos.
     */
    public function up(): void
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['pendiente', 'en_progreso', 'completado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Revierte la migración eliminando la tabla proyectos.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyectos');
    }
};
