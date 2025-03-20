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
        $etiquetas = Etiqueta::where('es_privado', false)->get();
        $marcadores = Marcador::with('etiquetas')->get();
        $admin = Usuario::where('email', 'admin@example.com')->first();

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
