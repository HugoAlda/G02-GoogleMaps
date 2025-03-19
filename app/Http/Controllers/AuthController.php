<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    // Método para renderizar la vista del login
    public function showLoginForm() {
        return view('auth.login');
    }

    // Método para procesar el login
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico debe ser válido',
            'password.required' => 'La contraseña es obligatoria'
        ]);
    
        // Obtener las credenciales del formulario
        $credentials = $request->only(['email', 'password']);
    
        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Generar una sesión de usuario
            $request->session()->regenerate();

            // Redirigir al usuario a la página de inicio
            // Usa intended para redirigir a la última página protegida visitada
            return redirect()->intended('home'); // TODO: Cambiar a la página de inicio
        }
    
        // Si las credenciales son incorrectas, redirigir al usuario a la página de login con un mensaje de error y con su old input
        return redirect()->route('login')->withErrors(['invalid' => 'Credenciales incorrectas'])->withInput();
    }

    // Método para cerrar sesión
    public function logout() {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente');
    }
}
