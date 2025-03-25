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
                'icono' => '<i class="fa-solid fa-monument"></i>',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Hoteles',
                'icono' => '<i class="fa-solid fa-hotel"></i>',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Puntos de interés',
                'icono' => '<i class="fa-solid fa-circle-info"></i>',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Estadios',
                'icono' => '<i class="fa-solid fa-futbol"></i>',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Vacacion 2024',
                'icono' => '<i class="fa-solid fa-umbrella-beach"></i>',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Parques',
                'icono' => '<i class="fa-solid fa-tree"></i>',
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
