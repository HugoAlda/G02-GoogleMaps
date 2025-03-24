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
                'icono' => 'monument',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Hoteles',
                'icono' => 'hotel',
                'es_privado' => false,
                'usuario_id' => $admin->id
            ],
            [
                'nombre' => 'Puntos de interés',
                'icono' => 'info',
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
