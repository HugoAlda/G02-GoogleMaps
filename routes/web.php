<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;

// Ruta principal redirige al login
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');

// Rutas de autenticación
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login')->name('login.post');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/register', 'showRegisterForm')->name('register');
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
    Route::get('/partida', 'partida')->name('mapa.partida');
});