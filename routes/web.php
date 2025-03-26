<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LobbyController;

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
    Route::get('/juego', 'juego')->name('mapa.juego');
    Route::post('/puntos', 'guardarPunto')->name('puntos.store'); // Guardar un nuevo punto

    // Rutas para la administración de partidas (crear/buscar/partidas creadas)
    Route::controller(LobbyController::class)->group(function () {  
        Route::get('/partida', 'index')->name('mapa.lobby');  // Cambiado el nombre para evitar conflictos
        Route::post('/partida', 'creaPartida')->name('mapa.creaPartida'); 
    });
});
