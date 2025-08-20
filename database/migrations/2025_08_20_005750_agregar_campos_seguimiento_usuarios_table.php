<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar las migraciones.
     * Agregar campos de seguimiento y seguridad a la tabla usuarios
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Campos para seguimiento de acceso
            $table->timestamp('last_login_at')->nullable()->comment('Fecha y hora del último acceso');
            $table->ipAddress('last_login_ip')->nullable()->comment('IP del último acceso');
            
            // Índices para optimizar consultas de seguridad
            $table->index(['email', 'last_login_at'], 'idx_users_email_login');
            $table->index('last_login_at', 'idx_users_last_login');
        });
    }

    /**
     * Reversar las migraciones.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar índices
            $table->dropIndex('idx_users_email_login');
            $table->dropIndex('idx_users_last_login');
            
            // Eliminar campos
            $table->dropColumn(['last_login_at', 'last_login_ip']);
        });
    }
};
