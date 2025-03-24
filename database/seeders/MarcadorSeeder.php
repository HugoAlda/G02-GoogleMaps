<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marcador;
use App\Models\Usuario;

class MarcadorSeeder extends Seeder
{
    public function run(): void
    {

        // Monumentos
        Marcador::firstOrCreate(
            ['nombre' => 'Iglesia de Bellvitge'],
            [
                'descripcion' => 'Iglesia románica del siglo XII, uno de los monumentos más antiguos de L\'Hospitalet',
                'latitud' => 41.3526,
                'longitud' => 2.1083,
                'direccion' => 'Ermita Mare de Déu de Bellvitge, L\'Hospitalet de Llobregat'
            ]
        );

        Marcador::firstOrCreate(
            ['nombre' => 'Ermita de Bellvitge'],
            [
                'descripcion' => 'Ermita medieval dedicada a la Mare de Déu de Bellvitge',
                'latitud' => 41.3519,
                'longitud' => 2.1067,
                'direccion' => 'Av. Mare de Déu de Bellvitge, L\'Hospitalet de Llobregat'
            ]
        );

        // Hoteles
        Marcador::firstOrCreate(
            ['nombre' => 'Hotel SB Plaza Europa'],
            [
                'descripcion' => 'Hotel moderno de 4 estrellas cerca de la Fira de Barcelona',
                'latitud' => 41.3589,
                'longitud' => 2.1289,
                'direccion' => 'Carrer de les Ciències, 11-13, L\'Hospitalet de Llobregat'
            ]
        );

        Marcador::firstOrCreate(
            ['nombre' => 'Hotel Travelodge L\'Hospitalet'],
            [
                'descripcion' => 'Hotel económico bien comunicado con el centro de Barcelona',
                'latitud' => 41.3561,
                'longitud' => 2.1198,
                'direccion' => 'Carrer Botànica, 25, L\'Hospitalet de Llobregat'
            ]
        );

        // Puntos de interés
        Marcador::firstOrCreate(
            ['nombre' => 'Hospital Universitario de Bellvitge'],
            [
                'descripcion' => 'Uno de los hospitales más importantes de Cataluña',
                'latitud' => 41.3442,
                'longitud' => 2.1019,
                'direccion' => 'Carrer de la Feixa Llarga, s/n, L\'Hospitalet de Llobregat'
            ]
        );

        Marcador::firstOrCreate(
            ['nombre' => 'Centro Comercial Gran Via 2'],
            [
                'descripcion' => 'Centro comercial con tiendas, restaurantes y cines',
                'latitud' => 41.3587,
                'longitud' => 2.1297,
                'direccion' => 'Av. de la Granvia, 75, L\'Hospitalet de Llobregat'
            ]
        );

        Marcador::firstOrCreate(
            ['nombre' => 'Parc de Bellvitge'],
            [
                'descripcion' => 'Parque urbano con áreas verdes y zonas de recreo',
                'latitud' => 41.3534,
                'longitud' => 2.1089,
                'direccion' => 'Avinguda Mare de Déu de Bellvitge, L\'Hospitalet de Llobregat'
            ]
        );

        Marcador::firstOrCreate(
            ['nombre' => 'Estación de Metro Bellvitge'],
            [
                'descripcion' => 'Estación de la línea L1 del metro de Barcelona',
                'latitud' => 41.3611,
                'longitud' => 2.1222,
                'direccion' => 'Rambla Marina, L\'Hospitalet de Llobregat'
            ]
        );
    }
}
