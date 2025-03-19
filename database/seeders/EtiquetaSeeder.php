<?php

namespace Database\Seeders;

use App\Models\Etiqueta;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class EtiquetaSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'admin');
        })->first();

        Etiqueta::create([
            'nombre' => 'admin',
            'icono' => 'star',
            'es_privado' => false,
            'usuario_id' => $admin->id
        ]);
    }
}
