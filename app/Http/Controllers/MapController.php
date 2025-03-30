<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
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

    public function juego()
    {
        return view('mapa.juego');
    }

    public function partida()
    {
        return redirect()->route('mapa.lobby');
    }

    public function store(Request $request)
    {
        return view('mapa.store');
    }

    public function guardarPunto(Request $request)
    {
        try {
            $request->validate([
                'nombre'      => 'required|string|max:255',
                'latitud'     => 'required|numeric',
                'longitud'    => 'required|numeric',
                'direccion'   => 'nullable|string|max:255',
                'descripcion' => 'nullable|string|max:255',
                'etiqueta_id' => 'nullable|integer',
                'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ], [
                'nombre.required'      => 'El nombre es obligatorio.',
                'latitud.required'     => 'La latitud es obligatoria.',
                'longitud.required'    => 'La longitud es obligatoria.',
                'descripcion.max'      => 'La descripción debe tener menos de 255 caracteres.',
                'imagen.image'         => 'El archivo debe ser una imagen.',
                'imagen.mimes'         => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg o webp.',
                'imagen.max'           => 'La imagen debe pesar menos de 2MB.',
            ]);
  
            DB::beginTransaction();
  
            $marcador = new Marcador();
            $marcador->nombre      = $request->nombre;
            $marcador->latitud     = $request->latitud;
            $marcador->longitud    = $request->longitud;
            $marcador->direccion   = $request->direccion;
            $marcador->descripcion = $request->descripcion;
            $marcador->imagen      = $request->imagen;
            $marcador->save();
  
            if ($request->etiqueta_id) {
                $etiqueta = Etiqueta::find($request->etiqueta_id);
                if ($etiqueta) {
                    $marcador->etiquetas()->attach($etiqueta);
                }
            } else {
                $etiqueta = new Etiqueta();
                $etiqueta->nombre = $request->nombre;
                $etiqueta->es_privado = false;
                $etiqueta->usuario_id = Auth::user()->id;
                $etiqueta->save();
                $marcador->etiquetas()->attach($etiqueta);
            }
  
            DB::commit();
  
            return response()->json(['message' => 'Punto creado correctamente'], 200);
  
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
  
    // Método para añadir un marcador a la etiqueta "Favoritos"
    public function addToFavorites(Request $request)
    {
        $request->validate([
            'marker_id' => 'required|integer|exists:marcadores,id',
        ]);
  
        $markerId = $request->marker_id;
        // Cargar el marcador con sus etiquetas para poder comprobar la existencia de "Favoritos"
        $marcador = Marcador::with('etiquetas')->find($markerId);
  
        if (!$marcador) {
            return response()->json(['error' => 'Marcador no encontrado'], 404);
        }
  
        // Verificar si ya tiene la etiqueta "Favoritos" (id 6)
        if ($marcador->etiquetas->contains('id', 6)) {
            return response()->json(['message' => 'El marcador ya está en favoritos'], 200);
        }
  
        // Asociar la etiqueta "Favoritos" (id 6) al marcador
        // $marcador = new MarcadoresEtiquetas();
        //     $marcador->marcador_id = $request->marker_id;
        //     $marcador->etiqueta_id = 6;
        //     $marcador->save();
        $marcador->etiquetas()->attach(6);
  
        return response()->json(['message' => 'Marcador añadido a favoritos'], 200);
    }
}