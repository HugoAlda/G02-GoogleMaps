<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Marcador;
use App\Models\Etiqueta;
use App\Models\Usuario;
use App\Http\Controllers\MarcadorController;
use App\Http\Controllers\EtiquetaController;

class MapController extends Controller
{
    public function index()
    {
        // Obtener las etiquetas públicas
        $etiquetas = Etiqueta::where('es_privado', false)->get();

        // Obteneres 
        
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
            'descripcion' => 'nullable|string|max:255', // Descripción del marcador
            'etiqueta' => 'required|integer', // Etiqueta del marcador
            'imagen' => 'nullable|image|max:2048', // Imagen del marcador
            'icono' => 'nullable|string|max:255', // Icono del marcador
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'], // Color del marcador
        ], [
            'etiqueta.required' => 'La etiqueta es obligatoria',
            'nombre.required' => 'El nombre es obligatorio',
            'latitud.required' => 'La latitud es obligatoria',
            'longitud.required' => 'La longitud es obligatoria',
            'descripcion.max' => 'La descripción debe tener menos de 255 caracteres',
            'imagen.max' => 'La imagen debe tener menos de 2048KB',
            'color.regex' => 'El color debe ser un valor hexadecimal válido (por ejemplo, #FFF o #FFFFFF)',
        ]);

        


        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }
}
