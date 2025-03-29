<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\MarcadoresEtiquetas;

class MarcadoresEtiquetasSeeder extends Seeder
{
    public function run(): void
    {
        $relaciones = [
            [
                'marcador_id' => 1, // Parroquia de Bellvitge
                'etiqueta_id' => 1, // Monumentos
            ],
            [
                'marcador_id' => 2, // Ermita de Bellvige
                'etiqueta_id' => 1, // Monumentos
            ],
            [
                'marcador_id' => 3, // Hotel Esperia
                'etiqueta_id' => 2, // Hoteles
            ],
            [
                'marcador_id' => 4, // Hospital de Bellvitge
                'etiqueta_id' => 3, // Punto de interes
            ],
            [
                'marcador_id' => 4, // Hospital de Bellvitge
                'etiqueta_id' => 6, // Favoritos
            ],
            [
                'marcador_id' => 5, // Parque de Bellvitge
                'etiqueta_id' => 5, // Parques
            ],
            [
                'marcador_id' => 6, // Metro de Bellvitge
                'etiqueta_id' => 3, // Punto de interes
            ],
            [
                'marcador_id' => 7, // Sergio Manzano
                'etiqueta_id' => 3, // Punto de interes
            ],
            [
                'marcador_id' => 8, // Unificación Bellvitge
                'etiqueta_id' => 4, // Estadios
            ],
            [
                'marcador_id' => 9, // Campo de futbol del Hospitalet
                'etiqueta_id' => 4, // Estadios
            ],
            [
                'marcador_id' => 10, // Hospital odontologico
                'etiqueta_id' => 3, // Punto de interes
            ],
            [
                'marcador_id' => 11, // Metropolitan
                'etiqueta_id' => 3, // Punto de interes
            ],
            [
                'marcador_id' => 12, // La Flama
                'etiqueta_id' => 3, // Punto de interes
            ]
        ];

        foreach ($relaciones as $relacion){
            $relacion = MarcadoresEtiquetas::create($relacion);
        }
    }
}