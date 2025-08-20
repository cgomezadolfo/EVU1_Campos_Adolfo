<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'responsable',
        'monto',
        'created_by'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto' => 'decimal:2',
    ];

    // Validaciones de estado
    public static function getEstadosValidos()
    {
        return ['pendiente', 'en_progreso', 'completado'];
    }

    // Scope para filtrar por estado
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    // Scope para filtrar por responsable
    public function scopePorResponsable($query, $responsable)
    {
        return $query->where('responsable', 'like', '%' . $responsable . '%');
    }

    // Scope para filtrar por rango de monto
    public function scopePorMontoEntre($query, $montoMin, $montoMax)
    {
        return $query->whereBetween('monto', [$montoMin, $montoMax]);
    }

    // Relación con el usuario que creó el proyecto
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessor para formatear el monto
    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 2, ',', '.');
    }

    // Accessor para obtener el estado en formato legible
    public function getEstadoLegibleAttribute()
    {
        $estados = [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En Progreso',
            'completado' => 'Completado'
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    // Método para obtener datos estáticos de ejemplo
    public static function getDatosEstaticos()
    {
        return [
            [
                'id' => 1,
                'nombre' => 'Sistema de Gestión Corporativa',
                'fecha_inicio' => '2025-01-15',
                'estado' => 'en_progreso',
                'responsable' => 'Ana García Martínez',
                'monto' => 125000.00
            ],
            [
                'id' => 2,
                'nombre' => 'Aplicación Móvil de Ventas',
                'fecha_inicio' => '2025-02-01',
                'estado' => 'pendiente',
                'responsable' => 'Carlos Rodríguez López',
                'monto' => 85000.00
            ],
            [
                'id' => 3,
                'nombre' => 'Portal Web de Clientes',
                'fecha_inicio' => '2024-10-01',
                'estado' => 'completado',
                'responsable' => 'María Fernández Silva',
                'monto' => 95000.00
            ],
            [
                'id' => 4,
                'nombre' => 'Sistema de Inventario',
                'fecha_inicio' => '2025-03-01',
                'estado' => 'pendiente',
                'responsable' => 'José Torres Mendoza',
                'monto' => 110000.00
            ],
            [
                'id' => 5,
                'nombre' => 'Plataforma de E-learning',
                'fecha_inicio' => '2024-11-15',
                'estado' => 'en_progreso',
                'responsable' => 'Laura Jiménez Castro',
                'monto' => 140000.00
            ],
            [
                'id' => 6,
                'nombre' => 'Sistema de Facturación Electrónica',
                'fecha_inicio' => '2025-04-15',
                'estado' => 'pendiente',
                'responsable' => 'Roberto Vásquez Herrera',
                'monto' => 75000.00
            ],
            [
                'id' => 7,
                'nombre' => 'Dashboard de Analíticas',
                'fecha_inicio' => '2025-05-01',
                'estado' => 'pendiente',
                'responsable' => 'Diana Morales Ruiz',
                'monto' => 90000.00
            ],
            [
                'id' => 8,
                'nombre' => 'Sistema de Recursos Humanos',
                'fecha_inicio' => '2025-06-01',
                'estado' => 'pendiente',
                'responsable' => 'Fernando Castillo Pérez',
                'monto' => 160000.00
            ]
        ];
    }
}
