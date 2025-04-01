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
Route::middleware('auth')->prefix('mapa')->group(function () {
    // Rutas del MapController
    Route::controller(MapController::class)->group(function () {
        Route::get('/', 'index')->name('mapa.index');
        Route::get('/juego/{partidaId}', 'juego')->name('mapa.juego');
        Route::get('/partida', 'partida')->name('mapa.partida');
        Route::get('/markers', 'apiMarkers');
        Route::post('/api/favorites', 'addToFavorites')->name('favorites.add');
        Route::post('/puntos', 'guardarPunto')->name('puntos.store');
    });

    // Rutas del LobbyController
    Route::controller(LobbyController::class)->group(function () {  
        Route::get('/partida', 'index')->name('mapa.lobby');
        Route::post('/partida', 'creaPartida')->name('mapa.creaPartida'); 
        Route::get('/partidas', 'getPartidas')->name('mapa.getPartidas');
        Route::get('/check-in-game', 'checkUserInGame')->name('mapa.checkInGame');
        Route::get('/grupos/{partidaId}', 'getGruposPartida')->name('mapa.getGruposPartida');
        Route::post('/unirse-grupo', 'unirseGrupo')->name('mapa.unirseGrupo');
        Route::post('/empezar-partida/{id}', 'empezarPartida')->name('mapa.empezarPartida');
        Route::get('/estado-partida/{id}', 'estadoPartida')->name('mapa.estadoPartida');
    });

    // Rutas del JuegoController
    Route::controller(JuegoController::class)->prefix('api')->group(function () {
        Route::get('/punto-control/{juegoId}/{indice}', 'obtenerPuntoControl');
        Route::post('/comprobar-respuesta', 'comprobarRespuesta');
        Route::get('/marcadores-juego/{juegoId}', 'marcadoresJuego');
        Route::get('/todos-puntos/{juegoId}', 'obtenerTodosPuntos');
        Route::post('/abandonar-partida', 'abandonarPartida');
    });
});
