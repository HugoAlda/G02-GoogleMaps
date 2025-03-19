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
    Route::get('/mapa', [MapController::class, 'index'])->name('mapa.index');
    Route::get('/mapa/juego', [MapController::class, 'juego'])->name('mapa.juego');
    Route::get('/mapa/partida', [MapController::class, 'partida'])->name('mapa.partida');
});