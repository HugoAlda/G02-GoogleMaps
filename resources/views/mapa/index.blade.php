<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    {{-- Añadir el CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <button class="btn-tag filter-tag active" data-tag="all">
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

    <!-- Modal para el admin: Agregar un nuevo punto -->
    <div class="modal fade" id="modal-add-point" tabindex="-1" aria-labelledby="modal-add-point-label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <!-- Encabezado del modal -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-point-label">
                        <i class="fas fa-map-marker-alt me-2"></i> Crear nuevo punto
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <!-- Formulario para agregar un punto -->
                <form action="{{ route('puntos.store') }}" method="POST" enctype="multipart/form-data" id="form-add-point" >
                    @csrf

                    <div class="modal-body">
                        <div class="row g-3">

                            {{-- Nombre --}}
                            <div class="col-md-6">
                                <label for="nombre" class="form-label fw-bold">Nombre</label>
                                <input type="text" class="form-control custom-input" id="nombre" name="nombre" placeholder="Ej: Mirador de la ciudad">
                                @error('nombre')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>


                            
                            <!-- Dirección y Icono -->
                            <div class="col-md-6">
                                <label for="direccion" class="form-label fw-bold">Dirección</label>
                                <div class="input-group">
                                    <input type="text" class="form-control custom-input w-75" id="direccion" name="direccion" placeholder="Ej: Mirador de la ciudad">
                                    <button class="btn btn-outline-secondary px-3" id="button-add-point" type="button" title="Añadir marcador">
                                        <i class="fas fa-location-dot"></i>
                                    </button>
                                </div>
                                @error('direccion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Etiqueta -->
                            <div class="col-md-6">
                                <label for="etiqueta-select" class="form-label fw-bold">Etiqueta</label>
                                <div class="input-group">
                                    <select class="form-select custom-select" id="etiqueta-select" name="etiqueta_id">
                                        <option value="" disabled selected>Selecciona una etiqueta</option>
                                        @foreach($etiquetas as $etiqueta)
                                            <option value="{{ $etiqueta->id }}">{{ $etiqueta->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-outline-secondary" id="btn-create-etiqueta" title="Crear nueva etiqueta">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                @error('etiqueta_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Icono -->
                            <div class="col-md-6">
                                <label for="icono" class="form-label fw-bold">Icono</label>
                                <select class="form-select custom-select" id="icono" name="icono">
                                    <option value="" disabled selected>Selecciona un icono</option>
                                    <option value="monumentos">&#xf19c; Monumentos</option>
                                    <option value="hoteles">&#xf594; Hoteles</option>
                                    <option value="puntos-interes">&#xf3c5; Puntos de interés</option>
                                    <option value="estadios">&#xf1e3; Estadios</option>
                                    <option value="vacaciones-2024">&#xf072; Vacaciones 2024</option>
                                    <option value="parques">&#xf1bb; Parques</option>
                                </select>
                                @error('icono')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Descripción (12 columnas) -->
                            <div class="col-12">
                                <label for="descripcion" class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control custom-textarea" id="descripcion" name="descripcion" rows="3" placeholder="Añade una descripción detallada del punto..."></textarea>
                                @error('descripcion')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <!-- Imagen (12 columnas) -->
                            <div class="col-12">
                                <label for="imagen" class="form-label fw-bold">Imagen</label>
                                <div class="image-upload-container">
                                    <!-- Input de archivo oculto -->
                                    <input type="file" class="form-control d-none" id="imagen" name="imagen" accept="image/png, image/jpeg, image/jpg, image/webp">

                                    <!-- Botón para subir imagen -->
                                    <label for="imagen" class="upload-box">
                                        <i class="fas fa-upload"></i>
                                        <span>Seleccionar imagen</span>
                                    </label>

                                    <!-- Contenedor de previsualización de imagen -->
                                    <div id="image-preview-container" class="d-none position-relative mt-2">
                                        <img id="image-preview" class="img-thumbnail" style="max-width: 200px;">
                                        <button id="remove-image" type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <!-- Información de formatos admitidos -->
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Formatos: PNG, JPEG, JPG, WEBP
                                    </small>
                                </div>
                                @error('imagen')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Pie del modal -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-save me-2"></i> Guardar punto
                        </button>
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

    {{-- Interfaz flotante para Confirmar/Cancelar --}}
    <div id="point-controls" style="display: none; position: absolute; top: 10px; left: 10px; z-index: 1000; background: white; padding: 10px; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0,0,0,0.2); text-align: center;">
        <p id="select-point-text" style="margin: 0; font-size: 14px; font-weight: bold; color: #333;">Selecciona un punto en el mapa</p>
        <div style="margin-top: 5px;">
            <button id="cancel-add-point" class="btn btn-outline-secondary btn-sm">Cancelar</button>
            <button id="confirm-add-point" class="btn btn-outline-primary btn-sm" disabled>Confirmar</button>
        </div>
    </div>
</body>
</html>