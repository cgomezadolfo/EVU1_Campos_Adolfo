<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class CrearBaseDatos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:crear {--force : Forzar creación de la base de datos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear la base de datos especificada en la configuración';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nombreBaseDatos = Config::get('database.connections.mysql.database');
        $host = Config::get('database.connections.mysql.host');
        $puerto = Config::get('database.connections.mysql.port');
        $usuario = Config::get('database.connections.mysql.username');
        $clave = Config::get('database.connections.mysql.password');

        $this->info("Configuración de la base de datos:");
        $this->info("Host: {$host}:{$puerto}");
        $this->info("Base de datos: {$nombreBaseDatos}");
        $this->info("Usuario: {$usuario}");

        try {
            // Conectar sin especificar base de datos para crearla
            Config::set('database.connections.mysql.database', '');
            DB::reconnect('mysql');
            
            // Verificar si la base de datos ya existe
            $existeBaseDatos = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$nombreBaseDatos]);
            
            if (!empty($existeBaseDatos) && !$this->option('force')) {
                $this->warn("La base de datos '{$nombreBaseDatos}' ya existe.");
                
                if ($this->confirm('¿Deseas continuar de todas formas?')) {
                    $this->info("Continuando con la base de datos existente...");
                } else {
                    $this->info("Operación cancelada.");
                    return 0;
                }
            } else {
                // Crear la base de datos
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$nombreBaseDatos}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $this->info("✅ Base de datos '{$nombreBaseDatos}' creada exitosamente.");
            }
            
            // Restaurar configuración original
            Config::set('database.connections.mysql.database', $nombreBaseDatos);
            DB::reconnect('mysql');
            
            // Probar la conexión
            DB::connection('mysql')->getPdo();
            $this->info("✅ Conexión a la base de datos establecida correctamente.");
            
            // Verificar si necesita ejecutar migraciones
            if ($this->confirm('¿Deseas ejecutar las migraciones ahora?', true)) {
                $this->call('migrate');
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Error al crear la base de datos: " . $e->getMessage());
            $this->error("Verifica que el servidor MySQL esté ejecutándose y que las credenciales sean correctas.");
            return 1;
        }
    }
}
