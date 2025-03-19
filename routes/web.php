<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;

// Rutas para el AuthController
Route::controller(AuthController::class)->group(function () {
    Route::get('/', function () {
        return view('auth.login');
    });
    Route::post('/login', 'login')->name('login.post'); // Procesar el login
    Route::get('/logout', 'logout')->name('logout'); // Cerrar sesión
    Route::get('/register', 'showRegisterForm')->name('register'); // Vista de registro
    Route::post('/register', 'register')->name('register.post'); // Procesar el registro
    // Pagina de mapa
    Route::get('/mapa', [MapController::class, 'index'])->name('mapa.index');
});