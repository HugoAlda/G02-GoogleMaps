<!DOCTYPE html>
<html lang="en">
{{-- Sección del head con metadatos y enlaces a estilos --}}
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Estilos boostrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Iconos boostrap --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    {{-- Estilos propios --}}
    <link rel="stylesheet" href="{{ asset('css/auth/style.css') }}">
</head>
<body>
    {{-- Contenido de la página --}}
    @yield('content')

    {{-- Scripts de Bootstrap --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Scripts personalizados --}}
    <script src="{{ asset('js/auth/utils.js') }}"></script>
    @stack('scripts')
</body>
</html>