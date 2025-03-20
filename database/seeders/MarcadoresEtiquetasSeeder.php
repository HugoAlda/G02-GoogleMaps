<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Marcador;
use App\Models\Etiqueta;

class MarcadoresEtiquetasSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener las etiquetas
        $etiquetaMonumentos = Etiqueta::where('nombre', 'Monumentos')->first();
        $etiquetaHoteles = Etiqueta::where('nombre', 'Hoteles')->first();
        $etiquetaInteres = Etiqueta::where('nombre', 'Puntos de interés')->first();

        if (!$etiquetaMonumentos || !$etiquetaHoteles || !$etiquetaInteres) {
            throw new \Exception('Las etiquetas necesarias no existen. Por favor, ejecuta primero el EtiquetaSeeder.');
        }

        // Relacionar monumentos
        $iglesiaBellvitge = Marcador::where('nombre', 'Iglesia de Bellvitge')->first();
        $ermitaBellvitge = Marcador::where('nombre', 'Ermita de Bellvitge')->first();
        
        if ($iglesiaBellvitge) {
            $iglesiaBellvitge->etiquetas()->syncWithoutDetaching([$etiquetaMonumentos->id]);
        }
        if ($ermitaBellvitge) {
            $ermitaBellvitge->etiquetas()->syncWithoutDetaching([$etiquetaMonumentos->id]);
        }

        // Relacionar hoteles
        $hotelPlaza = Marcador::where('nombre', 'Hotel SB Plaza Europa')->first();
        $hotelTravelodge = Marcador::where('nombre', 'Hotel Travelodge L\'Hospitalet')->first();
        
        if ($hotelPlaza) {
            $hotelPlaza->etiquetas()->syncWithoutDetaching([$etiquetaHoteles->id]);
        }
        if ($hotelTravelodge) {
            $hotelTravelodge->etiquetas()->syncWithoutDetaching([$etiquetaHoteles->id]);
        }

        // Relacionar puntos de interés
        $hospital = Marcador::where('nombre', 'Hospital Universitario de Bellvitge')->first();
        $centroComercial = Marcador::where('nombre', 'Centro Comercial Gran Via 2')->first();
        $parque = Marcador::where('nombre', 'Parc de Bellvitge')->first();
        $metro = Marcador::where('nombre', 'Estación de Metro Bellvitge')->first();
        
        if ($hospital) {
            $hospital->etiquetas()->syncWithoutDetaching([$etiquetaInteres->id]);
        }
        if ($centroComercial) {
            $centroComercial->etiquetas()->syncWithoutDetaching([$etiquetaInteres->id]);
        }
        if ($parque) {
            $parque->etiquetas()->syncWithoutDetaching([$etiquetaInteres->id]);
        }
        if ($metro) {
            $metro->etiquetas()->syncWithoutDetaching([$etiquetaInteres->id]);
        }
    }
}
