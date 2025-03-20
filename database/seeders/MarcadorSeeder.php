<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marcador;
use App\Models\Usuario;
use App\Models\Etiqueta;

class MarcadorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Usuario::where('email', 'admin@example.com')->first();
        
        // Obtener las etiquetas
        $etiquetaMonumentos = Etiqueta::where('nombre', 'Monumentos')->first();
        $etiquetaHoteles = Etiqueta::where('nombre', 'Hoteles')->first();
        $etiquetaInteres = Etiqueta::where('nombre', 'Puntos de interés')->first();

        // Monumentos
        Marcador::create([
            'nombre' => 'Iglesia de Bellvitge',
            'descripcion' => 'Iglesia románica del siglo XII, uno de los monumentos más antiguos de L\'Hospitalet',
            'latitud' => 41.3526,
            'longitud' => 2.1083,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaMonumentos->id
        ]);

        Marcador::create([
            'nombre' => 'Ermita de Bellvitge',
            'descripcion' => 'Ermita medieval dedicada a la Mare de Déu de Bellvitge',
            'latitud' => 41.3519,
            'longitud' => 2.1067,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaMonumentos->id
        ]);

        // Hoteles
        Marcador::create([
            'nombre' => 'Hotel SB Plaza Europa',
            'descripcion' => 'Hotel moderno de 4 estrellas cerca de la Fira de Barcelona',
            'latitud' => 41.3589,
            'longitud' => 2.1289,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaHoteles->id
        ]);

        Marcador::create([
            'nombre' => 'Hotel Travelodge L\'Hospitalet',
            'descripcion' => 'Hotel económico bien comunicado con el centro de Barcelona',
            'latitud' => 41.3561,
            'longitud' => 2.1198,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaHoteles->id
        ]);

        // Puntos de interés
        Marcador::create([
            'nombre' => 'Hospital Universitario de Bellvitge',
            'descripcion' => 'Uno de los hospitales más importantes de Cataluña',
            'latitud' => 41.3442,
            'longitud' => 2.1019,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaInteres->id
        ]);

        Marcador::create([
            'nombre' => 'Centro Comercial Gran Via 2',
            'descripcion' => 'Centro comercial con tiendas, restaurantes y cines',
            'latitud' => 41.3587,
            'longitud' => 2.1297,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaInteres->id
        ]);

        Marcador::create([
            'nombre' => 'Parc de Bellvitge',
            'descripcion' => 'Parque urbano con áreas verdes y zonas de recreo',
            'latitud' => 41.3534,
            'longitud' => 2.1089,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaInteres->id
        ]);

        Marcador::create([
            'nombre' => 'Estación de Metro Bellvitge',
            'descripcion' => 'Estación de la línea L1 del metro de Barcelona',
            'latitud' => 41.3611,
            'longitud' => 2.1222,
            'usuario_id' => $admin->id,
            'etiqueta_id' => $etiquetaInteres->id
        ]);
    }
}
