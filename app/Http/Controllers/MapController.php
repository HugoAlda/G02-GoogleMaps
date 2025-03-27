<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Juego;

class MapController extends Controller
{
    public function index()
    {
        // Obtener las etiquetas públicas (limitamos a 2 para mostrar inicialmente)
        $etiquetas = Etiqueta::where('es_privado', false)
            ->orderBy('nombre')
            ->get();
        
        // Obtener todos los marcadores con sus etiquetas
        $marcadores = Marcador::with('etiquetas')->get()->map(function ($marcador) {
            return [
                'id' => $marcador->id,
                'nombre' => $marcador->nombre,
                'descripcion' => $marcador->descripcion,
                'latitud' => $marcador->latitud,
                'longitud' => $marcador->longitud,
                'etiqueta' => $marcador->etiquetas->first()->nombre ?? 'sin-etiqueta',
                'etiqueta_id' => $marcador->etiquetas->first()->id ?? null
            ];
        });
    
        return view('mapa.index', [
            'etiquetas' => $etiquetas,
            'marcadores' => $marcadores,
            'etiquetas_visibles' => $etiquetas->take(2), // Solo las 2 primeras para mostrar
            'etiquetas_paginadas' => $etiquetas->slice(2) // El resto para paginación
        ]);
    }
    
    public function juego($id)
    {
        $juego = Juego::findOrFail($id); // Lanza 404 si no existe
        // $juego = $partida->juego;
        return view('mapa.juego', compact('juego'));
    }

    public function partida()
    {
        return redirect()->route('mapa.lobby');
    }

    public function store(Request $request)
    {
        return view('mapa.store');
    }

    // Método para guardar un nuevo punto (etiqueta y marcador)
    public function guardarPunto(Request $request)
    {
        try {
            // Validar los datos
            $request->validate([
                'nombre' => 'required|string|max:255', // Nombre del marcador
                'latitud' => 'required|numeric', // Latitud del marcador
                'longitud' => 'required|numeric', // Longitud del marcador
                'direccion' => 'nullable|string|max:255', // Dirección del marcador
                'descripcion' => 'nullable|string|max:255', // Descripción del marcador
                'etiqueta_id' => 'required|nullable|integer', // Etiqueta del marcador
                'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048', // Imagen con formatos permitidos
                // 'icono' => 'nullable|string|max:255', // Icono del marcador
            ], [
                'nombre.required' => 'El nombre es obligatorio.',
                'latitud.required' => 'La latitud es obligatoria.',
                'longitud.required' => 'La longitud es obligatoria.',
                'descripcion.max' => 'La descripción debe tener menos de 255 caracteres.',
                'imagen.image' => 'El archivo debe ser una imagen.',
                'imagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg o webp.',
                'imagen.max' => 'La imagen debe pesar menos de 2MB.',
                'icono.max' => 'El icono no puede superar los 255 caracteres.',
            ]);

            // Diccionario de iconos
            // TODO: Añadir más iconos y cambiar el icono por defecto
            $iconos = [
                'monumentos' => '<i class="fa-solid fa-monument"></i>',
            ];

            // Empezar una transacción
            DB::beginTransaction();

            // Crear un nuevo marcador
            $marcador = new Marcador();
            $marcador->nombre = $request->nombre;
            $marcador->latitud = $request->latitud;
            $marcador->longitud = $request->longitud;
            $marcador->direccion = $request->direccion;
            $marcador->descripcion = $request->descripcion;
            $marcador->imagen = $request->imagen;
            // $marcador->icono = '<i class="fa-solid fa-monument"></i>';

            // Guardar el marcador
            $marcador->save();

            // Verificar si existe una etiqueta
            if ($request->etiqueta_id) {
                $etiqueta = Etiqueta::find($request->etiqueta_id);

                // Si existe, se añade al marcador
                if ($etiqueta) {
                    $marcador->etiquetas()->attach($etiqueta);
                }
            } else {
                // Si no existe, se crea una nueva etiqueta
                $etiqueta = new Etiqueta();
                $etiqueta->nombre = $request->nombre;

                //TODO: Añadir icono a la etiqueta
                // $etiqueta->icono = $iconos[$request->icono];
                $etiqueta->es_privado = false;
                $etiqueta->usuario_id = Auth::user()->id;
                $etiqueta->save();

                // Se añade la etiqueta al marcador
                $marcador->etiquetas()->attach($etiqueta);
            }

            // Finalizar la transacción
            DB::commit();

            // Retornar una respuesta de éxito
            return response()->json(['message' => 'Punto creado correctamente'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        
        
    }
}
