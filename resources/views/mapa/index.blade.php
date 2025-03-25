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
                    {!! $etiqueta->icono !!} {{ ucfirst($etiqueta->nombre) }}
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
                <button class="btn btn-danger" title="Crear nuevo punto" id="button-add-point-form" data-bs-toggle="modal" data-bs-target="#modal-add-point">
                    <i class="fa-solid fa-plus fa-xs me-1"></i>
                    <i class="fa-solid fa-location-dot"></i>
                </button>
            @endif
        </div>
    </div>

    {{-- Modal para el admin: Referencia a button-add-point --}}
    <div class="modal fade" id="modal-add-point" tabindex="-1" aria-labelledby="modal-add-point-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content custom-modal">

                {{-- Header del modal --}}
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-add-point-label"><i class="fas fa-map-marker-alt me-2"></i> Crear nuevo punto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Formulario para crear un nuevo punto --}}
                <form action="" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        {{-- Fila 1: Etiqueta y Nombre --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="etiqueta-select" class="form-label fw-bold">Etiqueta</label>
                                <div class="d-flex">
                                    <select class="form-control custom-select me-2" id="etiqueta-select" name="etiqueta_id">
                                        <option value="" disabled selected>Selecciona una etiqueta</option>
                                        @foreach($etiquetas as $etiqueta)
                                            <option value="{{ $etiqueta->id }}">{{ $etiqueta->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="btn-create-etiqueta" title="Crear nueva etiqueta">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label fw-bold">Nombre del punto</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej: Mirador de la ciudad">
                            </div>
                        </div>

                        {{-- Fila 2: Dirección --}}
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="direccion" class="form-label fw-bold">Dirección</label>
                                <div class="input-group">
                                    <input type="text" class="form-control w-50" id="direccion" name="direccion" placeholder="Buscar dirección...">
                                    <button class="btn btn-outline-secondary" id="button-add-point" type="button" title="Añadir marcador">
                                        <i class="fas fa-location-dot"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Fila 3: Icono e Imagen --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="icono" class="form-label fw-bold">Icono del marcador</label>
                                <select class="form-control custom-select" id="icono" name="icono">
                                    <option value="" disabled selected>Selecciona un icono</option>
                                    <option value="map-marker">📍 Ubicación</option>
                                    <option value="map-pin">📌 Marcador</option>
                                    <option value="flag">🚩 Bandera</option>
                                    <option value="thumbtack">📍 Chincheta</option>
                                    <option value="star">⭐ Estrella</option>
                                    <option value="heart">❤️ Corazón</option>
                                    <option value="home">🏠 Casa</option>
                                    <option value="tree">🌳 Árbol</option>
                                    <option value="mountain">⛰️ Montaña</option>
                                    <option value="bicycle">🚲 Bicicleta</option>
                                    <option value="bus">🚌 Autobús</option>
                                    <option value="train">🚆 Tren</option>
                                    <option value="car">🚗 Coche</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="imagen" class="form-label fw-bold">Imagen del punto</label>
                                <input type="file" class="form-control" id="imagen" name="imagen" accept="image/png, image/jpeg, image/jpg, image/webp">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Formatos: PNG, JPEG, JPG, WEBP</small>
                            </div>
                        </div>

                        {{-- Fila 4: Descripción --}}
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Añade una descripción detallada del punto..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Footer del modal --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Cancelar</button>
                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-save me-2"></i>Guardar punto</button>
                    </div>
                </form>
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

    {{-- Script para el admin --}}
    @if (Auth::check() && Auth::user()->rol->nombre == 'Administrador')
        <script src="{{ asset('js/admin/admin.js') }}"></script>
    @endif
</body>
</html>