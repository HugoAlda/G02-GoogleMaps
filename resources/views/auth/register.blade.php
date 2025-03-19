@extends('layout.auth')

@section('title', 'Registro')

@section('content')
<main>
    {{-- Contenedor principal con el formulario de registro --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{-- Tarjeta personalizada para el formulario --}}
                <div class="card shadow custom-card">
                    {{-- Encabezado de la tarjeta --}}
                    <div class="card-header text-center">
                        <h4>Registro de Usuario</h4>
                    </div>
                    {{-- Cuerpo de la tarjeta con el formulario --}}
                    <div class="card-body">
                        <form method="POST" action="{{ route('register.post') }}">
                            @csrf
                            @method('POST')
                            
                            {{-- Primera fila: Nombre, Apellido, Email --}}
                            <div class="row mb-3">
                                {{-- Campo de nombre --}}
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="input" class="form__field @error('name') is-invalid @enderror" placeholder="Nombre" name="name" id='name' value="{{ old('name') }}" />
                                            <label for="name" class="form__label @error('name') text-danger @enderror">Nombre</label>
                                        </div>
                                        @error('name')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Campo de apellido --}}
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="input" class="form__field @error('surname') is-invalid @enderror" placeholder="Apellido" name="surname" id='surname' value="{{ old('surname') }}" />
                                            <label for="surname" class="form__label @error('surname') text-danger @enderror">Apellido</label>
                                        </div>
                                        @error('surname')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Campo de correo electrónico --}}
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="email" class="form__field @error('email') is-invalid @enderror" placeholder="Correo Electrónico" name="email" id='email' value="{{ old('email') }}" />
                                            <label for="email" class="form__label @error('email') text-danger @enderror">Correo Electrónico</label>
                                        </div>
                                        @error('email')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Segunda fila: Contraseña y Confirmar Contraseña --}}
                            <div class="row mb-3">
                                {{-- Campo de contraseña --}}
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="password" class="form__field @error('password') is-invalid @enderror" placeholder="Contraseña" name="password" id='password' />
                                            <label for="password" class="form__label @error('password') text-danger @enderror">Contraseña</label>
                                        </div>
                                        @error('password')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                {{-- Campo de confirmar contraseña --}}
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <div class="form__group field">
                                            <input type="password" class="form__field @error('password_confirmation') is-invalid @enderror" placeholder="Confirmar Contraseña" name="password_confirmation" id='password_confirmation' />
                                            <label for="password_confirmation" class="form__label @error('password_confirmation') text-danger @enderror">Confirmar Contraseña</label>
                                        </div>
                                        @error('password_confirmation')
                                            <span class="text-danger mt-1 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Mensaje de error general --}}
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

                            {{-- Botones de acción --}}
                            <div class="d-grid">
                                <button type="submit" class="btn-custom-white mt-3">
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
    <script src="{{ asset('js/auth/register.js') }}"></script>
@endpush