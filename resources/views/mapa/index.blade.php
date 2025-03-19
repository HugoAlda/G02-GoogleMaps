<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Map</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .navbar {
            background-color: #2c3e50;
        }
        .navbar-brand {
            color: #ecf0f1;
            font-weight: bold;
        }
        .nav-link {
            color: #bdc3c7;
        }
        .nav-link:hover {
            color: #ecf0f1;
        }
        #map {
            height: calc(100vh - 56px);
            width: 100%;
        }
        .controls-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Leaflet Maps App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Cerrar sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Panel de controles -->
    <div class="controls-panel">
        <div class="mb-3">
            <button class="btn btn-primary" id="centerMadrid">Centrar en Madrid</button>
        </div>
        <div class="mb-3">
            <select class="form-select" id="mapType">
                <option value="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png">Normal</option>
                <option value="https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png">Topográfico</option>
                <option value="https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png">Humanitario</option>
            </select>
        </div>
    </div>

    <!-- Contenedor del mapa -->
    <div id="map"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        let map = L.map('map').setView([40.416775, -3.703790], 13); // bcn
        
        // Capas base disponibles
        let baseLayers = {
            "Normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }),
            "Topográfico": L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenTopoMap'
            }),
            "Humanitario": L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap HOT'
            })
        };

        // Agregar la capa normal por defecto
        baseLayers["Normal"].addTo(map);

        // Evento para el botón de centrar en Madrid
        document.getElementById('centerMadrid').addEventListener('click', () => {
            map.setView([40.416775, -3.703790], 12);
        });

        // Evento para cambiar el tipo de mapa
        document.getElementById('mapType').addEventListener('change', (e) => {
            let selectedLayer = e.target.value;
            Object.values(baseLayers).forEach(layer => map.removeLayer(layer)); // Quitar todas las capas
            L.tileLayer(selectedLayer, { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        });
    </script>
</body>
</html>
