<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PuntoControl;
use App\Models\Juego;

class PuntoControlSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener el juego "Gymkana" (ajusta el nombre si es necesario)
        $juego = Juego::where('nombre', 'Gymkana')->first();

        // Definir los 5 puntos de control en Hospitalet de Llobregat
        $puntosControl = [
            [
                'nombre' => 'El Pont de la Llibertat',
                'latitud' => 41.3620,
                'longitud' => 2.1150,
                'direccion' => "El pont de la llibertat, Av. del Carrilet, 313, 08907 L'Hospitalet de Llobregat, Barcelona",
                'acertijo' => 'En este puente, ¿qué símbolo representa la libertad y la unión de la comunidad?',
                'respuesta' => 'Libertad',
                'color' => '#FF4500'
            ],
            [
                'nombre' => 'Plaça de Lluís Companys i Jover',
                'latitud' => 41.3645,
                'longitud' => 2.1170,
                'direccion' => "Plaça de Lluís Companys i Jover, 08901 L'Hospitalet de Llobregat, Barcelona",
                'acertijo' => '¿Que nuemero fue Lluís Companys i Jover como presidente de la Generalitat de Catalunya?',
                'respuesta' => '123',
                'color' => '#FFD700'
            ],
            [
                'nombre' => "Santa Eulàlia de l'Hospitalet",
                'latitud' => 41.3675,
                'longitud' => 2.1180,
                'direccion' => "Santa Eulàlia de l'Hospitalet, Carrer de Barcelona, 104, 08901 L'Hospitalet de Llobregat, Barcelona",
                'acertijo' => '¿Qué edificio sagrado ha sido centro de fe y tradición en la ciudad durante siglos?',
                'respuesta' => 'Iglesia',
                'color' => '#1E90FF'
            ],
            [
                'nombre' => 'Ayuntamiento de Hospitalet de Llobregat',
                'latitud' => 41.3685,
                'longitud' => 2.1155,
                'direccion' => "Ajuntament de l'Hospitalet de Llobregat, Plaça de l'Ajuntament, 11, 08901 L'Hospitalet de Llobregat, Barcelona",
                'acertijo' => '¿Qué edificio simboliza la administración y el pulso de la ciudad?',
                'respuesta' => 'Ayuntamiento',
                'color' => '#800080'
            ]
        ];
  
        foreach ($puntosControl as $punto) {
            $punto['juego_id'] = $juego->id;
            PuntoControl::create($punto);
        }
    }
}