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

    <!-- Panel de gestión de partidas -->
    <div class="container mt-3">
        <div class="card p-3 shadow">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <!-- Botón para crear nueva partida -->
                <button class="btn btn-success" id="crearPartida">
                    <i class="fas fa-plus-circle me-2"></i>Crear Partida
                </button>

                <!-- Input para buscar partidas por nombre -->
                <input type="text" class="form-control" id="buscarPartidaInput" placeholder="Buscar partida por nombre..." />

                <!-- Select para listar partidas existentes -->
                <select id="selectPartidas" class="form-select">
                    <option selected disabled>Selecciona una partida</option>
                    <!-- Aquí se insertarán dinámicamente las partidas existentes -->
                    <!-- <option value="1">Partida #1</option> -->
                </select>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</body>
</html>
