<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Grupo, Partida, Jugador, Juego};
use Illuminate\Support\Facades\{DB, Auth};

class LobbyController extends Controller
{
    public function index()
    {
        $juegos = Juego::all(); // Obtener todos los juegos disponibles
        return view('mapa.partida', compact('juegos'));
    }
    
    public function creaPartida(Request $request)
    {
        try {
            DB::beginTransaction();
    
            // Obtener usuario autenticado
            $user = Auth::user();
    
            // Buscar al jugador en base a usuario_id
            $jugador = Jugador::where('usuario_id', $user->id)->first();
    
            // Si el jugador no existe, lo creamos
            if (!$jugador) {
                $jugador = Jugador::create([
                    'usuario_id' => $user->id,
                    'puntos' => 0
                ]);
            }
    
            // Verificar si el usuario ya tiene una partida activa
            $partidaActiva = Partida::whereHas('grupos.jugadores', function ($query) use ($jugador) {
                $query->where('jugador_id', $jugador->id);
            })->where('estado', 'pendiente')->exists();
    
            if ($partidaActiva) {
                return response()->json(['error' => 'Ya tienes una partida en curso.'], 400);
            }
    
            // Verificar si el juego existe
            $juego = Juego::find($request->input('juego_id'));
            if (!$juego) {
                return response()->json(['error' => 'El juego seleccionado no existe.'], 400);
            }
    
            // Crear un nuevo grupo
            $grupo = Grupo::create([
                'nombre' => 'Grupo de ' . $user->name,
                'estado' => 'abierto'
            ]);
    
            // Asociar el usuario como propietario del grupo
            $grupo->jugadores()->attach($jugador->id, ['is_owner' => true]);
    
            // Crear la partida
            $partida = Partida::create([
                'juego_id' => $juego->id,
                'estado' => 'pendiente',
                'fecha_inicio' => now(),
                'fecha_fin' => null
            ]);
    
            // Asociar el grupo con la partida
            $grupo->partidas()->attach($partida->id);
    
            DB::commit();
    
            return response()->json(['message' => 'Partida creada exitosamente', 'partida' => $partida], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al crear la partida: ' . $e->getMessage()], 500);
        }
    }
    
}
