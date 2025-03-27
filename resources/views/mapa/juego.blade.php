<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Juego - Google Maps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/juegos/juegos.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    <!-- Mapa -->
    <div id="map"></div>

    <!-- Popup de pista dinámico -->
    <div id="popup-pista" class="popup-pista">
        <h5 id="titulo-pista"></h5>
        <p id="acertijo-pista"></p>
        <input type="text" id="respuesta" class="form-control mb-2" placeholder="Escribe tu respuesta">
        <button class="btn btn-primary w-100" id="btn-responder">Responder</button>
    </div>
    

    <!-- Variable desde el backend -->
    <script>
        window.juegoId = {{ $juego->id }};
        window.indicePunto = 0;
    </script>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/juegos/juegos.js') }}"></script>
</body>
</html>