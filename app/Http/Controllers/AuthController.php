<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    // Método para renderizar la vista del login
    public function showLoginView() {
        return view('auth.login');
    }

    // Método para renderizar la vista del registro
    public function showRegisterView() {
        // Si el usuario está autenticado, redirigir al mapa
        if (Auth::check()) {
            return redirect()->route('mapa.index');
        }

        return view('auth.register');
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
    
                // Obtener el nombre del rol (el ? es para evitar errores si es null)
                $roleName = Auth::user()->rol?->nombre;
    
                // Mapear roles a rutas
                $routes = [
                    'Administrador' => 'mapa.index',
                    'Cliente' => 'mapa.index',
                ];

                
                if (isset($routes[$roleName])) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'message' => 'Inicio de sesión exitoso',
                            'routes' => $routes[$roleName]
                        ], 200);
                    }
    
                    return redirect()->intended(route($routes[$roleName]));
                }
    
                // Si el rol no es válido, cerrar sesión y mostrar error
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'invalid' => 'No tienes permiso para acceder'
                ])->withInput();
            }
    
            // Credenciales incorrectas
            $errorResponse = ['invalid' => 'Credenciales incorrectas, introduce tus datos nuevamente.'];
    
            if ($request->expectsJson()) {
                return response()->json(['errors' => $errorResponse], 401);
            }
    
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
                return response()->json([
                    'error' => 'Ocurrió un error en el servidor, intenta nuevamente: ' . $e->getMessage()
                ], 500);
            }
    
            return redirect()->route('login')->withErrors([
                'server' => 'Ocurrió un error en el servidor, intenta nuevamente.'
            ])->withInput();
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
}
