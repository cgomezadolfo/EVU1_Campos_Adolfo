<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Proyecto;

class ProyectoSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para crear proyectos de ejemplo
     */
    public function run(): void
    {
        // Limpiar tabla antes de llenarla con datos actualizados
        Proyecto::truncate();
        
        // Obtener los IDs de los usuarios creados
        $usuariosIds = \App\Models\User::pluck('id')->toArray();

        $proyectos = [
            [
                'nombre' => 'Sistema de Gestión Corporativa',
                'descripcion' => 'Desarrollo de un sistema integral para la gestión de procesos corporativos incluyendo recursos humanos, finanzas y operaciones.',
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-06-30',
                'estado' => 'en_progreso',
                'responsable' => 'Ana García Martínez',
                'monto' => 125000.00,
                'created_by' => $usuariosIds[0] ?? 1
            ],
            [
                'nombre' => 'Aplicación Móvil de Ventas',
                'descripcion' => 'Aplicación móvil para el equipo de ventas que permita gestionar clientes, productos y realizar seguimiento de oportunidades.',
                'fecha_inicio' => '2025-02-01',
                'fecha_fin' => '2025-04-15',
                'estado' => 'pendiente',
                'responsable' => 'Carlos Rodríguez López',
                'monto' => 85000.00,
                'created_by' => $usuariosIds[1] ?? 1
            ],
            [
                'nombre' => 'Portal Web de Clientes',
                'descripcion' => 'Portal web para que los clientes puedan acceder a sus servicios, facturas y realizar solicitudes de soporte.',
                'fecha_inicio' => '2024-10-01',
                'fecha_fin' => '2024-12-31',
                'estado' => 'completado',
                'responsable' => 'María Fernández Silva',
                'monto' => 95000.00,
                'created_by' => $usuariosIds[2] ?? 1
            ],
            [
                'nombre' => 'Sistema de Inventario',
                'descripcion' => 'Sistema para el control y gestión de inventario con alertas automáticas y reportes en tiempo real.',
                'fecha_inicio' => '2025-03-01',
                'fecha_fin' => '2025-05-30',
                'estado' => 'pendiente',
                'responsable' => 'José Torres Mendoza',
                'monto' => 110000.00,
                'created_by' => $usuariosIds[3] ?? 1
            ],
            [
                'nombre' => 'Plataforma de E-learning',
                'descripcion' => 'Plataforma de aprendizaje en línea para capacitación de empleados con seguimiento de progreso y certificaciones.',
                'fecha_inicio' => '2024-11-15',
                'fecha_fin' => '2025-02-28',
                'estado' => 'en_progreso',
                'responsable' => 'Laura Jiménez Castro',
                'monto' => 140000.00,
                'created_by' => $usuariosIds[4] ?? 1
            ],
            [
                'nombre' => 'Sistema de Facturación Electrónica',
                'descripcion' => 'Implementación de sistema de facturación electrónica conforme a normativas fiscales.',
                'fecha_inicio' => '2025-04-15',
                'fecha_fin' => '2025-07-15',
                'estado' => 'pendiente',
                'responsable' => 'Roberto Vásquez Herrera',
                'monto' => 75000.00,
                'created_by' => $usuariosIds[5] ?? 1
            ],
            [
                'nombre' => 'Dashboard de Analíticas',
                'descripcion' => 'Dashboard empresarial para visualización de métricas y KPIs en tiempo real.',
                'fecha_inicio' => '2025-05-01',
                'fecha_fin' => '2025-08-01',
                'estado' => 'pendiente',
                'responsable' => 'Diana Morales Ruiz',
                'monto' => 90000.00,
                'created_by' => $usuariosIds[6] ?? 1
            ],
            [
                'nombre' => 'Sistema de Recursos Humanos',
                'descripcion' => 'Sistema integral de gestión de recursos humanos con módulos de nómina, evaluaciones y gestión de talento.',
                'fecha_inicio' => '2025-06-01',
                'fecha_fin' => '2025-12-01',
                'estado' => 'pendiente',
                'responsable' => 'Fernando Castillo Pérez',
                'monto' => 160000.00,
                'created_by' => $usuariosIds[7] ?? 1
            ]
        ];

        foreach ($proyectos as $proyecto) {
            Proyecto::create($proyecto);
        }
        
        $this->command->info('✅ Proyectos creados correctamente.');
    }
}
