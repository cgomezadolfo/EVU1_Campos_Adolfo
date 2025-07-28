<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar los campos responsable y monto
     */
    public function up(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->string('responsable', 255)->after('estado')->default('Sin asignar');
            $table->decimal('monto', 12, 2)->after('responsable')->default(0.00);
        });
    }

    /**
     * Revierte la migración eliminando los campos responsable y monto
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            $table->dropColumn(['responsable', 'monto']);
        });
    }
};
