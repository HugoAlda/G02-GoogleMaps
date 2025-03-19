<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modo Juego - Google Maps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <!-- Contenedor del mapa -->
    <div id="map"></div>

    <!-- Panel de controles -->
    <div class="controls-panel">
        <button id="zoomOut" class="btn btn-primary" title="Alejar">
            <i class="fas fa-minus"></i>
        </button>
        <button id="zoomIn" class="btn btn-primary" title="Acercar">
            <i class="fas fa-plus"></i>
        </button>
        <button id="centerUser" class="btn btn-primary" title="Centrar en mi ubicación">
            <i class="fas fa-location-crosshairs"></i>
        </button>
        <button id="toggleSatellite" class="btn btn-primary" title="Cambiar vista">
            <i class="fas fa-map"></i>
        </button>
        <a href="" class="btn btn-primary" title="Volver al mapa">
            <i class="fas fa-map-marked-alt"></i>
        </a>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
