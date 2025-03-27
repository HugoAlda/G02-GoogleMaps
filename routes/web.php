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

    // Rutas para la administración de partidas (crear/buscar/partidas creadas)
    Route::controller(LobbyController::class)->group(function () {  
        Route::get('/partida', 'index')->name('mapa.lobby');  // Cambiado el nombre para evitar conflictos
        Route::post('/partida', 'creaPartida')->name('mapa.creaPartida'); 
    });
});

// Ruta API protegida para obtener un punto de control de un juego según el índice
Route::middleware('auth')->get('/api/punto-control/{juegoId}/{indice}', [JuegoController::class, 'obtenerPuntoControl']);
Route::middleware('auth')->post('/api/comprobar-respuesta', [JuegoController::class, 'comprobarRespuesta']);
Route::middleware('auth')->get('/api/marcadores-juego/{juegoId}', [JuegoController::class, 'marcadoresJuego']);
Route::middleware('auth')->get('/api/todos-puntos/{juegoId}', [JuegoController::class, 'obtenerTodosPuntos']);
