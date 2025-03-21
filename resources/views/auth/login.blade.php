@extends('layout.auth')

@section('title', 'Login')

@section('content')
<main>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow custom-card">
                    <div class="card-header text-center">
                        <h4>Iniciar Sesión</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
                            @csrf
                            {{-- Campo de correo electrónico --}}
                            <div class="mb-3">
                                <div class="position-relative">
                                    <div class="form__group field">
                                        <input type="email" class="form__field @error('email') is-invalid @enderror"
                                            placeholder="Correo Electrónico" name="email" id='email'
                                            value="{{ old('email') }}" />
                                        <label for="email"
                                            class="form__label @error('email') text-danger @enderror">Correo
                                            Electrónico</label>
                                    </div>
                                    {{-- Mensaje de error desde frontend --}}
                                    <span class="text-danger mt-2 text-sm d-none" id="emailError"></span>
                                    {{-- Mensaje de error desde servidor --}}
                                    @error('email')
                                        <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- Campo de contraseña --}}
                            <div class="mb-3">
                                <div class="position-relative">
                                    {{-- Contenedor del input y label --}}
                                    <div class="form__group field w-100">
                                        <input type="password" class="form__field @error('password') is-invalid @enderror"
                                            placeholder="Contraseña" name="password" id="password" />
                                        <label for="password" class="form__label @error('password') text-danger @enderror">Contraseña</label>
                                    </div>
                            
                                    {{-- Icono del ojo (posicionado dentro del input) --}}
                                    <i class="bi bi-eye-slash toggle-password position-absolute" id="togglePassword"></i>
                                </div>
                            
                                {{-- Mensaje de error desde frontend --}}
                                <span class="text-danger mt-2 text-sm d-none" id="passwordError"></span>
                            
                                {{-- Mensaje de error desde servidor --}}
                                @error('password')
                                    <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Mensaje de errores generales desde servidor --}}
                            <div id="errorMessage" class="alert custom-error-alert d-none">
                                <i class="bi bi-exclamation-triangle"> </i><span id="errorMessageText"></span>
                            </div>
                            
                            {{-- Mensaje de éxito --}}
                            @if (session('success'))
                                <div id="successMessage" class="alert custom-success-alert d-none">
                                    <i class="bi bi-check-circle"></i> <span>{{ session('success') }}</span>
                                </div>
                            @endif

                            {{-- Enlace para recuperar contraseña --}}
                            <div class="d-flex justify-content-end mb-3">
                                <a href="#" class="text-decoration-none forgot-password">¿Olvidaste tu contraseña?</a>
                            </div>

                            {{-- Botones de acción --}}
                            <div class="d-grid">
                                <button type="submit" class="btn-custom-white" id="loginButton">
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
@endsection

@push('scripts')
    <script src="{{ asset('js/auth/login.js') }}"></script>
@endpush