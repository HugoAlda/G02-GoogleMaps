<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\MarcadoresEtiquetas;
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

    public function addToFavorites(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Validar que se recibió el ID del marcador
        $request->validate([
            'marker_id' => 'required|exists:marcadores,id',
        ]);

        $marcador = Marcador::find($request->marker_id);

        // Insert
        $etiquetaFavoritos = MarcadoresEtiquetas::firstOrCreate([
            'marcador_id' => $request->marker_id, // Opcional si gestionas etiquetas privadas
            'etiqueta_id' => 6,
        ]);

        return response()->json(['message' => 'Marcador añadido a Favoritos con éxito']);
    }

    public function removeFavorites(Request $request): JsonResponse
    {
        // Validar que se recibió el ID del marcador
        $request->validate([
            'marker_id' => 'required|exists:marcadores,id',
        ]);
    
        $marcadorId = $request->marker_id;
        $etiquetaId = 6; // ID de la etiqueta "Favoritos"
    
        // Eliminar la relación si existe
        DB::table('marcadores_etiquetas')
            ->where('marcador_id', $marcadorId)
            ->where('etiqueta_id', $etiquetaId)
            ->delete();
    
        return response()->json(['message' => 'Marcador eliminado de Favoritos con éxito']);
    }

    public function partida()
    {
        return view('mapa.partida');
    }
}