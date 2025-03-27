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
                'rol_id' => 1
            ]
        );

        // Crear etiquetas
        $etiquetas = [
            [
                'nombre' => 'Monumentos',
                'icono' => 'fa-solid fa-monument',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Hoteles',
                'icono' => 'fa-solid fa-hotel',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Puntos de interés',
                'icono' => 'fa-solid fa-circle-info',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Estadios',
                'icono' => 'fa-solid fa-futbol',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Vacacion 2024',
                'icono' => 'fa-solid fa-umbrella-beach',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Parques',
                'icono' => 'fa-solid fa-tree',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ]
        ];

        foreach ($etiquetas as $etiqueta) {
            Etiqueta::firstOrCreate(
                ['nombre' => $etiqueta['nombre']],
                $etiqueta
            );
        }
    }
}
