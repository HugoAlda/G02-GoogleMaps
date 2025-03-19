<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{

    // Método para renderizar la vista del login
    public function showLoginForm() {
        return view('auth.login');
    }
}
