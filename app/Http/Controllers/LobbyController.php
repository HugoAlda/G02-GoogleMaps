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
    
    public function getPartidas(Request $request)
    {
        try {
            $query = Partida::with(['juego', 'grupos.jugadores.usuario'])
                ->where('estado', 'pendiente');

            // Filtro por fecha
            if ($request->has('fecha')) {
                $fecha = $request->input('fecha');
                $query->whereDate('fecha_inicio', $fecha);
            }

            // Filtro por tipo de juego
            if ($request->has('tipo_juego') && $request->input('tipo_juego') !== '') {
                $query->where('juego_id', $request->input('tipo_juego'));
            }

            $partidas = $query->get()
                ->map(function ($partida) {
                    $grupo = $partida->grupos->first();
                    $propietario = $grupo ? $grupo->jugadores->where('is_owner', true)->first() : null;
                    $nombreCreador = $propietario && $propietario->usuario ? $propietario->usuario->name : 'Desconocido';
                    $emailCreador = $propietario && $propietario->usuario ? $propietario->usuario->email : 'N/A';

                    return [
                        'id' => $partida->id,
                        'juego' => $partida->juego ? $partida->juego->nombre : 'Juego no disponible',
                        'fecha_inicio' => $partida->fecha_inicio,
                        'creador' => [
                            'nombre' => $nombreCreador,
                            'email' => $emailCreador
                        ],
                        'estado' => $partida->estado
                    ];
                });

            return response()->json(['partidas' => $partidas]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las partidas: ' . $e->getMessage()], 500);
        }
    }

    public function checkUserInGame()
    {
        try {
            $user = Auth::user();
            $jugador = Jugador::where('usuario_id', $user->id)->first();

            if (!$jugador) {
                return response()->json(['inGame' => false]);
            }

            $enPartida = Partida::whereHas('grupos.jugadores', function ($query) use ($jugador) {
                $query->where('jugador_id', $jugador->id);
            })->where('estado', 'pendiente')->exists();

            return response()->json(['inGame' => $enPartida]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al verificar el estado del jugador: ' . $e->getMessage()], 500);
        }
    }
}
