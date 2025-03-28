<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminController;

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
    // Métodos GET
    Route::get('/', 'index')->name('mapa.index'); // Mostrar el mapa
    Route::get('/juego', 'juego')->name('mapa.juego'); // Mostrar el juego
    Route::get('/partida', 'partida')->name('mapa.partida'); // Iniciar una nueva partida

    // Métodos POST
    Route::post('/puntos', 'guardarPunto')->name('puntos.store'); // Guardar un nuevo punto
});