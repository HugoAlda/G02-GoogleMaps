<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\Juego;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function index()
    {
        // Obtener las etiquetas públicas (para filtros, paginación, etc.)
        $etiquetas = Etiqueta::where('es_privado', false)
            ->orderBy('nombre')
            ->get();
        
        // Obtener todos los marcadores con sus etiquetas
        // Se ajusta el mapeo: si un marcador tiene la etiqueta "Favoritos" se usará esa para mostrarlo
        $marcadores = Marcador::with('etiquetas')->get()->map(function ($marcador) {
            $etiquetaFavoritos = $marcador->etiquetas->firstWhere('nombre', 'Favoritos');
            $etiqueta = $etiquetaFavoritos ?: $marcador->etiquetas->first();
            return [
                'id'           => $marcador->id,
                'nombre'       => $marcador->nombre,
                'descripcion'  => $marcador->descripcion,
                'direccion'    => $marcador->direccion,
                'latitud'      => $marcador->latitud,
                'longitud'     => $marcador->longitud,
                'etiqueta'     => $etiqueta ? $etiqueta->nombre : 'sin-etiqueta',
                'etiqueta_id'  => $etiqueta ? $etiqueta->id : null,
            ];
        });
    
        return view('mapa.index', [
            'etiquetas'            => $etiquetas,
            'marcadores'           => $marcadores,
            'etiquetas_visibles'   => $etiquetas->take(2),
            'etiquetas_paginadas'  => $etiquetas->slice(2)
        ]);
    }

    public function juego(Juego $id)
    {

        try {
            // Obtener el juego por su ID
            $juego = Juego::with(['partidas.grupos.jugadores', 'puntosControl'])->findOrFail($id->id);

            return view('mapa.juego', compact('juego'));
        } catch (\Exception $e) {
            return redirect()->route('mapa.index')->withErrors(['error' => 'Juego no encontrado']);
        }
    }

    public function partida()
    {
        return view('mapa.partida');
    }
}