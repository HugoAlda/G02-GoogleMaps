<?php

namespace Database\Seeders;

use App\Models\Marcador;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class MarcadorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::whereHas('rol', function($q) {
            $q->where('nombre', 'admin');
        })->first();

        $marcadores = [
            [
                'nombre' => 'Plaza Mayor',
                'latitud' => 40.4168,
                'longitud' => -3.7038,
                'direccion' => 'Plaza Mayor, Madrid',
                'descripcion' => 'Plaza histórica en el centro de Madrid',
                'color' => '#FF0000',
                'icono' => 'monument'
            ],
            [
                'nombre' => 'Parque del Retiro',
                'latitud' => 40.4152,
                'longitud' => -3.6844,
                'direccion' => 'Parque del Retiro, Madrid',
                'descripcion' => 'Parque histórico de Madrid',
                'color' => '#00FF00',
                'icono' => 'park'
            ],
            [
                'nombre' => 'Museo del Prado',
                'latitud' => 40.4138,
                'longitud' => -3.6921,
                'direccion' => 'Paseo del Prado, Madrid',
                'descripcion' => 'Museo nacional de arte',
                'color' => '#0000FF',
                'icono' => 'museum'
            ],
            [
                'nombre' => 'Puerta del Sol',
                'latitud' => 40.4169,
                'longitud' => -3.7035,
                'direccion' => 'Puerta del Sol, Madrid',
                'descripcion' => 'Plaza emblemática de Madrid',
                'color' => '#FFFF00',
                'icono' => 'landmark'
            ],
            [
                'nombre' => 'Palacio Real',
                'latitud' => 40.4180,
                'longitud' => -3.7144,
                'direccion' => 'Calle Bailén, Madrid',
                'descripcion' => 'Residencia oficial de la Familia Real Española',
                'color' => '#800080',
                'icono' => 'castle'
            ]
        ];

        foreach ($marcadores as $marcador) {
            $marcador['usuario_id'] = $admin->id;
            Marcador::create($marcador);
        }
    }
}
