<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Grupo, Partida, Jugador, Juego};
use Illuminate\Support\Facades\{DB, Auth};
use Log;

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

            // Primero, obtener todas las partidas pendientes del jugador para depuración
            $partidasPendientes = DB::table('partidas AS p')
                ->select('p.id AS partida_id', 'g.id AS grupo_id', 'jg.is_owner')
                ->join('grupos_partidas AS gp', 'p.id', '=', 'gp.partida_id')
                ->join('grupos AS g', 'gp.grupo_id', '=', 'g.id')
                ->join('jugadores_grupos AS jg', 'g.id', '=', 'jg.grupo_id')
                ->where('jg.jugador_id', $jugador->id)
                ->where('p.estado', 'pendiente')
                ->get();

            // Si hay partidas pendientes donde el jugador es propietario, las eliminamos
            if ($partidasPendientes->contains('is_owner', true)) {
                foreach ($partidasPendientes as $partida) {
                    if ($partida->is_owner) {
                        // Eliminar las relaciones y la partida
                        DB::table('jugadores_grupos')->where('grupo_id', $partida->grupo_id)->delete();
                        DB::table('grupos_partidas')->where('partida_id', $partida->partida_id)->delete();
                        DB::table('grupos')->where('id', $partida->grupo_id)->delete();
                        DB::table('partidas')->where('id', $partida->partida_id)->delete();
                    }
                }
            }
    
            // Verificar si el juego existe
            $juego = Juego::find($request->input('juego_id'));
            if (!$juego) {
                return response()->json(['error' => 'El juego seleccionado no existe.'], 400);
            }
    
            // Crear dos grupos
            $grupo1 = Grupo::create([
                'nombre' => 'Grupo 1 de ' . $user->nombre,
                'estado' => 'abierto'
            ]);

            $grupo2 = Grupo::create([
                'nombre' => 'Grupo 2 de ' . $user->nombre,
                'estado' => 'abierto'
            ]);

            // Asociar el usuario como propietario del primer grupo
            $grupo1->jugadores()->attach($jugador->id, ['is_owner' => true]);
    
            // Crear la partida
            $partida = Partida::create([
                'juego_id' => $juego->id,
                'estado' => 'pendiente',
                'fecha_inicio' => now(),
                'fecha_fin' => null
            ]);
    
            // Asociar ambos grupos con la partida
            $grupo1->partidas()->attach($partida->id);
            $grupo2->partidas()->attach($partida->id);
    
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
                    $nombreCreador = $propietario && $propietario->usuario ? $propietario->usuario->nombre : 'Desconocido';
                    $emailCreador = $propietario && $propietario->usuario ? $propietario->usuario->email : 'N/A';

                    // Contar grupos con al menos 4 jugadores
                    $gruposCompletos = $partida->grupos->filter(function($grupo) {
                        return $grupo->jugadores->count() >= 4;
                    })->count();

                    return [
                        'id' => $partida->id,
                        'juego' => $partida->juego ? $partida->juego->nombre : 'Juego no disponible',
                        'fecha_inicio' => $partida->fecha_inicio,
                        'creador' => [
                            'nombre' => $nombreCreador,
                            'email' => $emailCreador
                        ],
                        'estado' => $partida->estado,
                        'grupos_completos' => $gruposCompletos,
                        'total_grupos' => $partida->grupos->count()
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

    public function getGruposPartida($partidaId)
    {
        try {
            // Cargar la partida con todas las relaciones necesarias
            $partida = Partida::with(['grupos.jugadores.usuario'])->findOrFail($partidaId);
            
            $grupos = $partida->grupos->map(function ($grupo) {
                return [
                    'id' => $grupo->id,
                    'nombre' => $grupo->nombre,
                    'estado' => $grupo->estado,
                    'usuarios' => $grupo->jugadores->map(function ($jugador) {
                        // Asegurarse de que tenemos acceso al usuario
                        $usuario = $jugador->usuario;
                        return [
                            'id' => $jugador->id,
                            'nombre' => $usuario ? $usuario->nombre : 'Usuario sin nombre',
                            'email' => $usuario ? $usuario->email : 'Sin email',
                            'is_owner' => $jugador->pivot->is_owner
                        ];
                    })->values()
                ];
            });

            return response()->json(['grupos' => $grupos]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los grupos: ' . $e->getMessage()], 500);
        }
    }

    public function unirseGrupo(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validar el request
            $request->validate([
                'grupo_id' => 'required|exists:grupos,id',
                'partida_id' => 'required|exists:partidas,id'
            ]);

            // Obtener el jugador del usuario actual
            $user = Auth::user();
            $jugador = Jugador::where('usuario_id', $user->id)->first();

            // Si el jugador no existe, lo creamos
            if (!$jugador) {
                $jugador = Jugador::create([
                    'usuario_id' => $user->id,
                    'puntos' => 0
                ]);
            }

            // Verificar si ya está en una partida activa
            $enPartida = Partida::whereHas('grupos.jugadores', function ($query) use ($jugador) {
                $query->where('jugador_id', $jugador->id);
            })->where('estado', 'pendiente')->exists();

            if ($enPartida) {
                return response()->json(['error' => 'Ya estás en una partida activa'], 400);
            }

            // Obtener el grupo y verificar que existe
            $grupo = Grupo::findOrFail($request->grupo_id);
            
            // Verificar que el grupo no esté lleno (máximo 4 jugadores)
            if ($grupo->jugadores()->count() >= 4) {
                return response()->json(['error' => 'El grupo ya está lleno (máximo 4 jugadores)'], 400);
            }

            // Verificar que el grupo pertenece a la partida
            $partidaCorrecta = $grupo->partidas()
                ->where('partidas.id', $request->partida_id)
                ->where('estado', 'pendiente')
                ->exists();

            if (!$partidaCorrecta) {
                return response()->json(['error' => 'El grupo no pertenece a la partida especificada o la partida no está pendiente'], 400);
            }

            // Verificar si el jugador ya está en el grupo
            if ($grupo->jugadores()->where('jugador_id', $jugador->id)->exists()) {
                return response()->json(['error' => 'Ya eres miembro de este grupo'], 400);
            }

            // Unir al jugador al grupo
            $grupo->jugadores()->attach($jugador->id, ['is_owner' => false]);

            DB::commit();
            return response()->json(['message' => 'Te has unido al grupo correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al unirse al grupo: ' . $e->getMessage()], 500);
        }
    }

    public function empezarPartida($partidaId)
    {
        try {
            DB::beginTransaction();

            // Obtener la partida con sus grupos y jugadores
            $partida = Partida::with(['grupos.jugadores'])->findOrFail($partidaId);

            // Verificar que la partida está en estado pendiente
            if ($partida->estado !== 'pendiente') {
                return response()->json(['error' => 'La partida ya ha comenzado o ha finalizado'], 400);
            }

            // Contar grupos con al menos 4 jugadores
            $gruposValidos = $partida->grupos->filter(function($grupo) {
                return $grupo->jugadores->count() >= 4;
            });

            // Verificar que hay al menos 2 grupos con 4 jugadores cada uno
            if ($gruposValidos->count() < 2) {
                $mensaje = 'No hay suficientes grupos completos. Se necesitan al menos 2 grupos con 4 jugadores cada uno.';
                $gruposFaltantes = [];
                
                foreach ($partida->grupos as $grupo) {
                    $jugadoresActuales = $grupo->jugadores->count();
                    if ($jugadoresActuales < 4) {
                        $gruposFaltantes[] = [
                            'grupo_id' => $grupo->id,
                            'jugadores_actuales' => $jugadoresActuales,
                            'jugadores_necesarios' => 4 - $jugadoresActuales
                        ];
                    }
                }

                return response()->json([
                    'error' => $mensaje,
                    'grupos_incompletos' => $gruposFaltantes
                ], 400);
            }

            // Si todo está correcto, actualizar el estado de la partida
            $partida->estado = 'en_curso';
            $partida->fecha_inicio = now();
            $partida->save();

            // También podemos actualizar el estado de los grupos
            foreach ($gruposValidos as $grupo) {
                $grupo->estado = 'en_juego';
                $grupo->save();
            }

            DB::commit();
            return response()->json([
                'message' => '¡La partida ha comenzado!',
                'partida' => [
                    'id' => $partida->id,
                    'estado' => $partida->estado,
                    'fecha_inicio' => $partida->fecha_inicio,
                    'grupos_activos' => $gruposValidos->count()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al iniciar la partida: ' . $e->getMessage()], 500);
        }
    }
}
