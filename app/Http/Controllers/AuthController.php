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

            $user = Auth::user(); // Obtiene el usuario autenticado

            // Verificar el rol del usuario y redirigir según corresponda
            if ($user->rol_id === 2) {
                return redirect()->route('mapa.index');
            }         
            
            // Si no es cliente, redirigir a una ruta por defecto
            return redirect()->route('login')->withErrors(['invalid' => 'No tienes permiso para acceder'])->withInput();
        }
    
        // Si las credenciales son incorrectas, redirigir al usuario a la página de login con un mensaje de error y con su old input
        return redirect()->route('login')->withErrors(['invalid' => 'Credenciales incorrectas'])->withInput();
    }

    // Método para cerrar sesión
    public function logout() {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente');
    }

    // Método para renderizar la vista del registro
    public function showRegisterForm() {
        return view('auth.register');
    }
}
