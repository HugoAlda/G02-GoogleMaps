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
            ["nombre" => "Parroquia de Nuestra Señora de Bellvitge"],
            [
                "descripcion" => "Parroquia románica del siglo XII, uno de los monumentos más antiguos de Hospitalet.",
                "latitud" => 41.3492614,
                "longitud" => 2.1085391,
                "direccion" => "Carrer de l'Ermita, 65 - 67, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Ermita Sta Mª de Bellvitge"],
            [
                "descripcion" => "Ermita medieval dedicada a la Mare de Déu de Bellvitge.",
                "latitud" => 41.3490071,
                "longitud" => 2.1088651,
                "direccion" => "Carrer de l'Ermita de Bellvitge, 6, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        // Hotel
        Marcador::firstOrCreate(
            ["nombre" => "Hyatt Regency Barcelona Tower"],
            [
                "descripcion" => "Es el hotel por excelencia de Bellvitge.",
                "latitud" => 41.346340,
                "longitud" => 2.108386,
                "direccion" => "Avinguda de la Granvia de l’Hospitalet, 144, 08907 Barcelona"
            ]
        );

        // Puntos de interés
        Marcador::firstOrCreate(
            ["nombre" => "Hospital Universitario de Bellvitge"],
            [
                "descripcion" => "Uno de los hospitales más importantes de Cataluña.",
                "latitud" => 41.3447701,
                "longitud" => 2.101657,
                "direccion" => "Carrer de la Feixa Llarga, s/n, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Parque de Bellvitge"],
            [
                "descripcion" => "Parque urbano con áreas verdes y zonas de recreo.",
                "latitud" => 41.3484031,
                "longitud" => 2.1083935,
                "direccion" => "Carrer de l'Ermita de Bellvitge, 38, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Estación de Metro Bellvitge"],
            [
                "descripcion" => "Estación de la línea L1 del metro de Barcelona.",
                "latitud" => 41.3509701,
                "longitud" => 2.1109047,
                "direccion" => "08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Polideportivo Municipal Bellvitge Sergio Manzano"],
            [
                "descripcion" => "Polideportivo con pistas de fútbol, baloncesto, etc.",
                "latitud" => 41.3475679,
                "longitud" => 2.1051944,
                "direccion" => "Av. Mare de Déu de Bellvitge, 7, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "U.D. Unificación Bellvitge"],
            [
                "descripcion" => "Camp Municipal de la Feixa Llarga.",
                "latitud" => 41.3484678,
                "longitud" => 2.1042537,
                "direccion" => "Complex Esportiu Feixa Llarga Campo Municipal de Fútbol Feixa Llarga, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Estadio Municipal de Fútbol de L'Hospitalet"],
            [
                "descripcion" => "Estadio de futbol donde juega el Hospitalet.",
                "latitud" => 41.3477672,
                "longitud" => 2.1033509,
                "direccion" => "Carrer de la Residencia, 30, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Hospital Odontológico UB"],
            [
                "descripcion" => "Hospital de Odontologia de Hospitalet.",
                "latitud" => 41.3461218,
                "longitud" => 2.1053587,
                "direccion" => "Campus Bellvitge, Carrer de la Feixa Llarga, s/n, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "Gimnasio Metropolitan"],
            [
                "descripcion" => "Gimnasio que se encuentra al lado del Hyatt Regency Barcelona Tower.",
                "latitud" => 41.3469942,
                "longitud" => 2.1097974,
                "direccion" => "Avinguda de la Granvia de l’Hospitalet, 142, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );

        Marcador::firstOrCreate(
            ["nombre" => "La Flama"],
            [
                "descripcion" => "Restaurante ubicado en el centro de Bellvitge",
                "latitud" => 41.3509423,
                "longitud" => 2.1116497,
                "direccion" => "Avinguda de la Granvia de l’Hospitalet, 142, 08907 L'Hospitalet de Llobregat, Barcelona"
            ]
        );
    }
}