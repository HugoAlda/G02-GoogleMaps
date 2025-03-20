<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Etiqueta;
use App\Models\Usuario;

class EtiquetaSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario admin si no existe
        $admin = Usuario::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nombre' => 'Admin',
                'password' => bcrypt('admin'),
                'rol_id' => 1 // Asumiendo que 1 es el ID del rol admin
            ]
        );

        // Crear etiquetas
        $etiquetas = [
            [
                'nombre' => 'Monumentos',
                'icono' => 'monument',
                'es_privado' => false,
            ],
            [
                'nombre' => 'Hoteles',
                'icono' => 'hotel',
                'es_privado' => false,
            ],
            [
                'nombre' => 'Puntos de interés',
                'icono' => 'info',
                'es_privado' => false,
            ]
        ];

        foreach ($etiquetas as $etiqueta) {
            Etiqueta::firstOrCreate(
                ['nombre' => $etiqueta['nombre']],
                [
                    'icono' => $etiqueta['icono'],
                    'es_privado' => $etiqueta['es_privado'],
                    'usuario_id' => $admin->id
                ]
            );
        }
    }
}
