@extends('layout.auth')

@section('title', 'Registro')

@section('content')
    <main>
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow custom-card">
                        <div class="card-header text-center">
                            <h4>Registro de Usuario</h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                                @csrf

                                <!-- Fila 1: Nombre, Apellido, Correo -->
                                <div class="row mb-3">
                                    <div class="col-md-4 col-12">
                                        <div class="form__group field">
                                            <input type="text" class="form__field @error('nombre') is-invalid @enderror"
                                                placeholder="Nombre" name="nombre" id="nombre"
                                                value="{{ old('nombre') }}" />
                                            <label for="nombre"
                                                class="form__label @error('nombre') text-danger @enderror">Nombre</label>
                                        </div>
                                        <span class="text-danger mt-2 text-sm d-none" id="nombreError"></span>
                                        @error('nombre')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form__group field">
                                            <input type="text" class="form__field @error('apellidos') is-invalid @enderror"
                                                placeholder="Apellido" name="apellidos" id="apellidos"
                                                value="{{ old('apellidos') }}" />
                                            <label for="apellidos"
                                                class="form__label @error('apellidos') text-danger @enderror">Apellido</label>
                                        </div>
                                        <span class="text-danger mt-2 text-sm d-none" id="apellidosError"></span>
                                        @error('apellidos')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 col-12">
                                        <div class="form__group field">
                                            <input type="email" class="form__field @error('email') is-invalid @enderror"
                                                placeholder="Correo Electrónico" name="email" id="email"
                                                value="{{ old('email') }}" />
                                            <label for="email"
                                                class="form__label @error('email') text-danger @enderror">Correo
                                                Electrónico</label>
                                        </div>
                                        <span class="text-danger mt-2 text-sm d-none" id="emailError"></span>
                                        @error('email')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Fila 2: Nombre de Usuario, Contraseña, Confirmar Contraseña -->
                                <div class="row mb-3">
                                    <div class="col-md-4 col-12">
                                        <div class="form__group field">
                                            <input type="text" class="form__field @error('username') is-invalid @enderror"
                                                placeholder="Usuario" name="username" id="username"
                                                value="{{ old('username') }}" />
                                            <label for="username"
                                                class="form__label @error('username') text-danger @enderror">
                                                Usuario</label>
                                        </div>
                                        <span class="text-danger mt-2 text-sm d-none" id="usernameError"></span>
                                        @error('username')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 col-12 position-relative">
                                        <div class="form__group field">
                                            <input type="password"
                                                class="form__field @error('password') is-invalid @enderror"
                                                placeholder="Contraseña" name="password" id="password" />
                                            <label for="password"
                                                class="form__label @error('password') text-danger @enderror">Contraseña</label>
                                        </div>
                                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                                        <span class="text-danger mt-2 text-sm d-none" id="passwordError"></span>
                                        @error('password')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 col-12 position-relative">
                                        <div class="form__group field">
                                            <input type="password"
                                                class="form__field @error('password_confirmation') is-invalid @enderror"
                                                placeholder="Confirmar Contraseña" name="password_confirmation"
                                                id="password_confirmation" />
                                            <label for="password_confirmation"
                                                class="form__label @error('password_confirmation') text-danger @enderror">Confirmar
                                                Contraseña</label>
                                        </div>
                                        <i class="bi bi-eye-slash toggle-password" id="togglePasswordConfirm"></i>
                                        <span class="text-danger mt-2 text-sm d-none" id="passwordConfirmationError"></span>
                                        @error('password_confirmation')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mensaje de error general -->
                                <div id="errorMessage" class="alert custom-error-alert d-none">
                                    <i class="bi bi-exclamation-triangle"></i> <span id="errorMessageText"></span>
                                </div>

                                <!-- Mensaje de éxito -->
                                @if (session('success'))
                                    <div id="successMessage" class="alert custom-success-alert d-none">
                                        <i class="bi bi-check-circle"></i> <span>{{ session('success') }}</span>
                                    </div>
                                @endif

                                <!-- Botones de acción -->
                                <div class="d-grid">
                                    <button type="submit" class="btn-custom-white" id="registerButton">
                                        <i class="bi bi-person-plus"></i> Registrarme
                                    </button>
                                    <a href="{{ route('login') }}" class="btn-custom-blue mt-3">
                                        <i class="bi bi-box-arrow-in-right"></i> Ya tengo cuenta
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/auth/utils.js') }}"></script>
    <script src="{{ asset('js/auth/register.js') }}"></script>
@endpush