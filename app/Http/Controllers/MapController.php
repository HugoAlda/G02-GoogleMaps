<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\Usuario;

class MapController extends Controller
{
    public function index()
    {
        // Obtener las etiquetas públicas
        $etiquetas = Etiqueta::where('es_privado', false)->get();
        
        // Obtener los marcadores con sus etiquetas
        $marcadores = Marcador::with('etiquetas')->get()->map(function ($marcador) {
            return [
                'id' => $marcador->id,
                'nombre' => $marcador->nombre,
                'descripcion' => $marcador->descripcion,
                'latitud' => $marcador->latitud,
                'longitud' => $marcador->longitud,
                'etiqueta' => $marcador->etiquetas->first()->nombre ?? 'sin-etiqueta' // Si no tiene etiquetas, usa 'sin-etiqueta'
            ];
        });
    
        // Obtener el usuario administrador (opcional)
        $admin = Usuario::where('email', 'admin@example.com')->first();
    
        // Retornar la vista con las etiquetas y marcadores
        return view('mapa.index', compact('etiquetas', 'marcadores', 'admin'));
    }

    public function juego()
    {
        return view('mapa.juego');
    }

    public function partida()
    {
        return view('mapa.partida');
    }
}