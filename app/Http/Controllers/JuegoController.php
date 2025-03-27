<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoControl;

class JuegoController extends Controller
{
    public function obtenerPuntoControl($juegoId, $indice = 0)
    {
        $puntos = PuntoControl::where('juego_id', $juegoId)
            ->orderBy('id')
            ->get();

        if ($indice < 0 || $indice >= $puntos->count()) {
            return response()->json(['error' => 'Punto no encontrado'], 404);
        }

        // Incluir información del punto anterior si existe
        $puntoAnterior = null;
        if ($indice > 0) {
            $puntoAnterior = [
                'latitud' => $puntos[$indice - 1]->latitud,
                'longitud' => $puntos[$indice - 1]->longitud,
            ];
        }

        return response()->json([
            'nombre' => $puntos[$indice]->nombre,
            'acertijo' => $puntos[$indice]->acertijo,
            'respuesta' => $puntos[$indice]->respuesta,
            'latitud' => $puntos[$indice]->latitud,
            'longitud' => $puntos[$indice]->longitud,
            'direccion' => $puntos[$indice]->direccion,
            'puntoAnterior' => $puntoAnterior
        ]);        
    }

    public function obtenerTodosPuntos($juegoId)
    {
        return response()->json(
            PuntoControl::where('juego_id', $juegoId)
                ->select('latitud', 'longitud')
                ->get()
        );
    }

    public function comprobarRespuesta(Request $request)
    {
        $request->validate([
            'juego_id' => 'required|integer',
            'indice' => 'required|integer',
            'respuesta' => 'required|string',
        ]);

        $punto = PuntoControl::where('juego_id', $request->juego_id)
                    ->orderBy('id')
                    ->skip($request->indice)
                    ->first();

        if (!$punto) {
            return response()->json(['correcto' => false, 'error' => 'Punto no encontrado'], 404);
        }

        $respuestaCorrecta = strtolower(trim($punto->respuesta));
        $respuestaUsuario = strtolower(trim($request->respuesta));

        return response()->json([
            'correcto' => $respuestaCorrecta === $respuestaUsuario
        ]);
    }
}