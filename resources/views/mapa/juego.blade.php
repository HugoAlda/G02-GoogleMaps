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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
</head>
<body>

    <!-- Popup de pista dinámico -->
    <div id="popup-pista" class="popup-pista">
        <h5 id="titulo-pista"></h5>
        <p id="acertijo-pista"></p>
        <input type="text" id="respuesta" class="form-control mb-2" placeholder="Escribe tu respuesta">
        <button class="btn btn-primary w-100" id="btn-responder">Responder</button>
    </div>

    <!-- Mapa -->
    <div id="map"></div>

    <div class="controls-panel">
        <button id="btn-abandonar" class="btn btn-primary iconos abandonar" title="Abandonar partida">
            <i class="fa-solid fa-flag"></i>
        </button>
        <button id="zoomOut" class="btn btn-primary iconos" title="Alejar">
            <i class="fas fa-minus"></i>
        </button>
        <button id="zoomIn" class="btn btn-primary iconos" title="Acercar">
            <i class="fas fa-plus"></i>
        </button>
        <button id="centerUser" class="btn btn-primary iconos" title="Centrar en mi ubicación">
            <i class="fas fa-location-crosshairs"></i>
        </button>
        <button id="toggleRoutePanel" class="btn btn-primary iconos" title="Mostrar indicaciones">
            <i class="fas fa-route"></i>
        </button>        
    </div>
    

    <!-- Variable desde el backend -->
    <script>
        window.juegoId = {{ $juego->id }};
        window.partidaId = {{ $partida->id }};
        window.indicePunto = 0;
    </script>    

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Leaflet debe ir antes que Routing Machine -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    
    <!-- Tu archivo JS final -->
    <script src="{{ asset('js/juegos/juegos.js') }}"></script>
</body>
</html>