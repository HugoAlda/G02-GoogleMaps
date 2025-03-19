<!DOCTYPE html>
<html lang="en">
{{-- Sección del head con metadatos y enlaces a estilos --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    {{-- Estilos boostrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Iconos boostrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{-- Estilos propios --}}
    <link rel="stylesheet" href="{{ asset('css/auth/style.css') }}">
    <style>
        
    </style>
</head>
<body>
    {{-- Contenido principal --}}
    <main>
        {{-- Contenedor principal con el formulario de login --}}
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    {{-- Tarjeta personalizada para el formulario --}}
                    <div class="card shadow custom-card">
                        {{-- Encabezado de la tarjeta --}}
                        <div class="card-header text-center">
                            <h4>Iniciar Sesión</h4>
                        </div>
                        {{-- Cuerpo de la tarjeta con el formulario --}}
                        <div class="card-body">
                            <form method="POST" action="{{ route('login.post') }}">
                                @csrf
                                @method('POST')
                                {{-- Campo de correo electrónico --}}
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="input" class="form__field @error('email') is-invalid @enderror" placeholder="Correo Electrónico" name="email" id='email' value="{{ old('email') }}" />
                                            <label for="email" class="form__label @error('email') text-danger @enderror">Correo Electrónico</label>
                                        </div>
                                        @error('email')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Campo de contraseña --}}
                                <div class="mb-3">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="password" class="form__field @error('password') is-invalid @enderror" placeholder="Contraseña" name="password" id='password' value="{{ old('password') }}" />
                                            <label for="password" class="form__label @error('password') text-danger @enderror">Contraseña</label>
                                            {{-- Botón para mostrar/ocultar contraseña --}}
                                            <span class="input-group-text" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer;">
                                                <i class="bi bi-eye-slash fs-4" id="togglePassword"></i>
                                            </span>
                                        </div>
                                        @error('password')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Mensaje de error para credenciales incorrectas --}}
                                @error('invalid')
                                    <div class="alert custom-error-alert">
                                        <i class="bi bi-exclamation-triangle"></i> {{ $message }}
                                    </div>
                                @enderror

                                {{-- Mensaje de éxito --}}
                                @if (session('success'))
                                    <div class="alert custom-success-alert">
                                        <i class="bi bi-check-circle"></i> {{ session('success') }}
                                    </div>
                                @endif
                                
                                {{-- Enlace para recuperar contraseña --}}
                                <div class="d-flex justify-content-end mb-3">
                                    <a href="#" class="text-decoration-none forgot-password">¿Olvidaste tu contraseña?</a>
                                </div>
                                {{-- Botones de acción --}}
                                <div class="d-grid">
                                    <button type="submit" class="btn-custom-white">
                                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                                    </button>
                                    <a href="{{ route('register') }}" class="btn-custom-blue mt-3">
                                        <i class="bi bi-person-plus"></i> Registrarme
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Scripts de Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts personalizados --}}
    <script src="{{ asset('js/auth/auth.js') }}"></script>
</body>
</html>