<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LobbyController;
use App\Http\Controllers\JuegoController;

// Ruta principal redirige al login
Route::get('/', [AuthController::class, 'showLoginView'])->name('login');

// Rutas de autenticación
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login.post');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/register', 'showRegisterView')->name('register');
    Route::post('/register', 'register')->name('register.post');
});

/**
 * Rutas del mapa protegidas por autenticación
 * 
 * @middleware auth - Verifica que el usuario esté autenticado
 * @prefix mapa - Agrega '/mapa' como prefijo a todas las rutas del grupo
 * @controller MapController - Asigna el controlador para manejar las rutas
 */

// Rutas del mapa protegidas por autenticación

Route::middleware('auth')->prefix('mapa')->controller(MapController::class)->group(function () {
    Route::get('/', 'index')->name('mapa.index');
    //Route::get('/juego', 'juego')->name('mapa.juego');
    // Route::get('/juego/partida/{id}', 'juegoDesdePartida')->name('mapa.juego.desdePartida');
    Route::get('/juego/{id}', 'juego')->name('mapa.juego');
    Route::get('/partida', 'partida')->name('mapa.partida');
    Route::get('/markers', [MapController::class, 'apiMarkers']);
    Route::post('/api/favorites', [MapController::class, 'addToFavorites'])->name('favorites.add');

        // Métodos POST
        Route::post('/puntos', 'guardarPunto')->name('puntos.store'); // Guardar un nuevo punto

    // Rutas para la administración de partidas (crear/buscar/partidas creadas)
    Route::controller(LobbyController::class)->group(function () {  
        Route::get('/partida', 'index')->name('mapa.lobby');  // Cambiado el nombre para evitar conflictos
        Route::post('/partida', 'creaPartida')->name('mapa.creaPartida'); 
        Route::get('/partidas', 'getPartidas')->name('mapa.getPartidas');
        Route::get('/check-in-game', 'checkUserInGame')->name('mapa.checkInGame');
        
        // Nuevas rutas para grupos
        Route::get('/grupos/{partidaId}', 'getGruposPartida')->name('mapa.getGruposPartida');
        Route::post('/unirse-grupo', 'unirseGrupo')->name('mapa.unirseGrupo');
        
        // Nueva ruta para empezar partida
        Route::post('/empezar-partida/{partidaId}', 'empezarPartida')->name('mapa.empezarPartida');
    });
});

// Rutas para el lobby y partidas
Route::middleware(['auth'])->group(function () {
    Route::get('/mapa/partidas', [LobbyController::class, 'getPartidas']);
    Route::post('/mapa/partida', [LobbyController::class, 'creaPartida']);
    Route::post('/mapa/unirse-grupo', [LobbyController::class, 'unirseGrupo']);
    Route::get('/mapa/grupos/{partida}', [LobbyController::class, 'getGruposPartida']);
    Route::post('/mapa/empezar-partida/{partida}', [LobbyController::class, 'empezarPartida']);
});

// Ruta API protegida para obtener un punto de control de un juego según el índice
Route::middleware('auth')->get('/api/punto-control/{juegoId}/{indice}', [JuegoController::class, 'obtenerPuntoControl']);
Route::middleware('auth')->post('/api/comprobar-respuesta', [JuegoController::class, 'comprobarRespuesta']);
Route::middleware('auth')->get('/api/marcadores-juego/{juegoId}', [JuegoController::class, 'marcadoresJuego']);
Route::middleware('auth')->get('/api/todos-puntos/{juegoId}', [JuegoController::class, 'obtenerTodosPuntos']);
Route::middleware('auth')->post('/api/abandonar-partida', [JuegoController::class, 'abandonarPartida']);
Route::middleware('auth')->get('/mapa/juego/{partidaId}', [MapController::class, 'juego'])->name('mapa.juego');
