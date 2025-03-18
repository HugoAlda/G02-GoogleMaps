<?php

namespace Database\Seeders;

use App\Models\PuntoControl;
use App\Models\Juego;
use App\Models\Grupo;
use Illuminate\Database\Seeder;

class PuntoControlSeeder extends Seeder
{
    public function run(): void
    {
        $juego = Juego::where('nombre', 'Gymkana')->first();
        $grupos = Grupo::all();

        $puntosControl = [
            [
                'nombre' => 'Punto Control 1',
                'latitud' => 40.4168,
                'longitud' => -3.7038,
                'direccion' => 'Plaza Mayor, Madrid',
                'acertijo' => '¿Qué famoso edificio del siglo XVII se encuentra en el centro de esta plaza?',
                'respuesta' => 'Casa de la Panadería',
                'color' => '#FF0000',
                'icono' => 'flag',
                'grupo_id' => null
            ],
            [
                'nombre' => 'Punto Control 2',
                'latitud' => 40.4152,
                'longitud' => -3.6844,
                'direccion' => 'Parque del Retiro, Madrid',
                'acertijo' => '¿Qué rey mandó construir este parque en el siglo XVII?',
                'respuesta' => 'Felipe IV',
                'color' => '#00FF00',
                'icono' => 'flag',
                'grupo_id' => null
            ],
            [
                'nombre' => 'Punto Control 3',
                'latitud' => 40.4138,
                'longitud' => -3.6921,
                'direccion' => 'Museo del Prado, Madrid',
                'acertijo' => '¿Qué famoso pintor español tiene más obras expuestas en este museo?',
                'respuesta' => 'Francisco de Goya',
                'color' => '#0000FF',
                'icono' => 'flag',
                'grupo_id' => null
            ],
            [
                'nombre' => 'Punto Control 4',
                'latitud' => 40.4180,
                'longitud' => -3.7144,
                'direccion' => 'Palacio Real, Madrid',
                'acertijo' => '¿Cuántas habitaciones tiene el Palacio Real?',
                'respuesta' => '3418',
                'color' => '#800080',
                'icono' => 'flag',
                'grupo_id' => null
            ]
        ];

        foreach ($puntosControl as $punto) {
            $punto['juego_id'] = $juego->id;
            PuntoControl::create($punto);
        }
    }
}
