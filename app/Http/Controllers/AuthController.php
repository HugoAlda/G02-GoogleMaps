<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    // Método para renderizar la vista del login
    public function showLoginForm() {
        return view('auth.login');
    }
    
    public function login(Request $request) {
        try {
            // Validar los datos del formulario
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
                $request->session()->regenerate();
    
                // if ($request->expectsJson()) {
                //     return response()->json(['message' => 'Inicio de sesión exitoso'], 200);
                // }

                $user = Auth::user(); // Obtiene el usuario autenticado

                // Verificar el rol del usuario y redirigir según corresponda
                if ($user->rol_id === 2) {
                    return redirect()->route('mapa.index');
                }         
                
                // Si no es cliente, redirigir a una ruta por defecto
                return redirect()->route('login')->withErrors(['invalid' => 'No tienes permiso para acceder'])->withInput();
    
                return redirect()->intended('home');
            }
    
            $errorResponse = ['invalid' => 'Credenciales incorrectas, '];
    
            // Si la solicitud es una solicitud JSON, devolver un error 401
            if ($request->expectsJson()) {
                return response()->json(['errors' => $errorResponse], 401);
            }
    
            // En caso de que no sea una solicitud JSON, redirigir al usuario a la página de login con los errores y los datos del formulario
            return redirect()->route('login')->withErrors($errorResponse)->withInput();
    
        } catch (ValidationException $e) {
            // Manejo de errores de validación
            if ($request->expectsJson()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
    
            return redirect()->route('login')->withErrors($e->errors())->withInput();
    
        } catch (Exception $e) {
            // Manejo de errores inesperados del servidor
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Ocurrió un error en el servidor, intenta nuevamente: ' . $e->getMessage()], 500);
            }
    
            return redirect()->route('login')->withErrors(['server' => 'Ocurrió un error en el servidor, intenta nuevamente.'])->withInput();
        }
    }    

    // Método para cerrar sesión
    public function logout() {
        // Si el usuario está autenticado, cerrar la sesión
        if (Auth::check()) {
            Auth::logout();
            return redirect()->route('login')->with('success', 'Sesión cerrada exitosamente');
        }

        // Si el usuario no está autenticado, redirigir al login
        return redirect()->route('login');
    }

    // Método para renderizar la vista del registro
    public function showRegisterForm() {
        return view('auth.register');
    }
}
