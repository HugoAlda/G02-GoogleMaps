<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el AuthController
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');     // Vista de login
    Route::post('/login', 'login')->name('login.post'); // Procesar el login
    Route::get('/logout', 'logout')->name('logout'); // Cerrar sesión
});