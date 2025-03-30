<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoControl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public function abandonarPartida(Request $request)
    {
        $request->validate([
            'juego_id' => 'required|integer'
        ]);

        $userId = Auth::id();
        $jugador = \App\Models\Jugador::where('usuario_id', $userId)->first();

        if (!$jugador) {
            return response()->json(['error' => 'Jugador no encontrado'], 404);
        }

        // Obtener partida activa y grupo donde está el jugador
        $grupo = DB::table('grupos')
            ->join('grupos_partidas', 'grupos.id', '=', 'grupos_partidas.grupo_id')
            ->join('partidas', 'grupos_partidas.partida_id', '=', 'partidas.id')
            ->join('jugadores_grupos', 'grupos.id', '=', 'jugadores_grupos.grupo_id')
            ->where('jugadores_grupos.jugador_id', $jugador->id)
            ->where('partidas.juego_id', $request->juego_id)
            ->where('partidas.estado', 'en_curso')
            ->select('grupos.id AS grupo_id')
            ->first();

        if (!$grupo) {
            return response()->json(['error' => 'No estás en una partida activa de este juego'], 400);
        }

        // Eliminar jugador del grupo
        DB::table('jugadores_grupos')
            ->where('jugador_id', $jugador->id)
            ->where('grupo_id', $grupo->grupo_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Has abandonado la partida correctamente']);
    }
}