<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if (Auth::check() && Auth::user()->rol->nombre == 'Administrador')
        <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    @endif
    <link rel="stylesheet" href="{{ asset('css/mapa/mapa.css') }}">
    <title>Mapa Interactivo</title>
</head>
<body>
    <div class="container">
        <!-- Barra de navegación -->
        <nav class="navbar">
            <div class="container-fluid">
                <div class="search-container">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar en el mapa...">
                        <button class="btn-search">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Barra de etiquetas -->
        <div class="tags-bar">
            <button class="btn-tag active" data-tag="all">
            <i class="fas fa-globe"></i> Todos
            </button>
            @foreach($etiquetas as $etiqueta)
                <button class="btn-tag filter-tag" data-tag="{{ $etiqueta->nombre }}">
                    <i class="fas fa-{{ $etiqueta->icono }}"></i> {{ ucfirst($etiqueta->nombre) }}
                </button>
            @endforeach
        </div>

        <div id="map"></div>

        <div class="controls-panel">
            <a href="{{ route('logout') }}" class="btn btn-danger" title="Cerrar sesión">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
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
            <a href="{{ route('mapa.lobby') }}" class="btn btn-primary" title="Iniciar partida">
                <i class="fas fa-play"></i>
            </a>
            <a href="{{ route('mapa.juego') }}" class="btn btn-primary" title="Iniciar juego">
                <i class="fas fa-gamepad"></i>
            </a>
            <!-- Botones de ADMIN -->
            @if (Auth::check() && Auth::user()->rol->nombre == 'Administrador')
                <button class="btn btn-danger" title="Crear nuevo punto" id="button-create-point" data-bs-toggle="modal" data-bs-target="#modal-create-point">
                    <i class="fa-solid fa-plus fa-xs me-1"></i>
                    <i class="fa-solid fa-location-dot"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- Modal para el admin referencia a button-create-point --}}
    <div class="modal fade" id="modal-create-point" tabindex="-1" aria-labelledby="modal-create-point-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-create-point-label">Crear nuevo punto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h1>Etiquetas</h1>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-outline-secondary">Crear</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.marcadores = @json($marcadores);
        window.etiquetas = @json($etiquetas);
    </script>
    <script src="{{ asset('js/mapa/mapa.js') }}"></script>

    @if (Auth::check() && Auth::user()->rol->nombre == 'Administrador')
        <script src="{{ asset('js/admin/admin.js') }}"></script>
    @endif
</body>
</html>