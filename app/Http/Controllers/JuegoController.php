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

        return response()->json([
            'nombre' => $puntos[$indice]->nombre,
            'acertijo' => $puntos[$indice]->acertijo,
            'respuesta' => $puntos[$indice]->respuesta,
        ]);
    }
}