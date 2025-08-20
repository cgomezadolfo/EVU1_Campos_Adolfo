<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar tabla de usuarios antes de poblar (cuidado en producción)
        \DB::table('users')->where('id', '>', 1)->delete();
        
        $usuarios = [
            [
                'name' => 'Ana García Martínez',
                'email' => 'ana.garcia@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Carlos Rodríguez López',
                'email' => 'carlos.rodriguez@techsolutions.com', 
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'María Fernández Silva',
                'email' => 'maria.fernandez@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'José Torres Mendoza',
                'email' => 'jose.torres@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Laura Jiménez Castro',
                'email' => 'laura.jimenez@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Roberto Vásquez Herrera',
                'email' => 'roberto.vasquez@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Diana Morales Ruiz',
                'email' => 'diana.morales@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ],
            [
                'name' => 'Fernando Castillo Pérez',
                'email' => 'fernando.castillo@techsolutions.com',
                'password' => Hash::make('MiClave123!'),
                'email_verified_at' => Carbon::now(),
            ]
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                $usuario
            );
        }

        $this->command->info('✅ Usuarios creados correctamente.');
    }
}
