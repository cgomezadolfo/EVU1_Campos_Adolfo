<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PruebaUsuario extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuario:crear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear usuario de prueba para verificar la conexión MySQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $nombre = $this->ask('Nombre del usuario', 'Usuario Prueba');
        $email = $this->ask('Email del usuario', 'test' . rand(1000, 9999) . '@test.com');
        $password = $this->secret('Contraseña (por defecto: MiClave123!)', 'MiClave123!');
        
        $this->info("Creando usuario en MySQL...");
        $this->info("Nombre: $nombre");
        $this->info("Email: $email");
        
        try {
            $usuario = User::create([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]);
            
            $this->info("✅ Usuario creado exitosamente:");
            $this->info("ID: {$usuario->id}");
            $this->info("Nombre: {$usuario->name}");
            $this->info("Email: {$usuario->email}");
            $this->info("Creado en: {$usuario->created_at}");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Error al crear usuario: " . $e->getMessage());
            return 1;
        }
    }
}
