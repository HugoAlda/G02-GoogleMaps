@extends('layout.lobby')

@section('title', 'Lobby Inicial')

@section('content')
    <!-- Añadir meta tag con el email del usuario -->
    <meta name="user-email" content="{{ Auth::user()->email }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <main>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Lobby de Partidas</h2>
                <a href="{{ route('mapa.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div class="crear_partida">
                <h4>Empieza una partida</h4>
                <form id="form_crear_partida" onsubmit="return false;"> {{-- Prevent default form submission --}}
                    @csrf
                    <div class="form-group">
                        <label for="juego_id">Selecciona un juego:</label>
                        <select name="juego_id" id="juego_id" class="form-control" required>
                            <option value="">-- Selecciona un juego --</option> 
                            @foreach($juegos as $juego)
                                <option value="{{ $juego->id }}">{{ $juego->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary" id="crear_partida" onclick="crearPartida(); return false;">Crear Partida</button>
                </form>
            </div>
            <div class="partidas_creadas">
                <h4>Partidas Creadas</h4>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroFecha">Fecha:</label>
                            <input type="date" id="filtroFecha" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="filtroTipoJuego">Tipo de Juego:</label>
                            <select id="filtroTipoJuego" class="form-control">
                                <option value="">Todos</option>
                                @foreach($juegos as $juego)
                                    <option value="{{ $juego->id }}">{{ $juego->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button id="limpiarFiltros" class="btn btn-secondary form-control">Limpiar Filtros</button>
                        </div>
                    </div>
                </div>
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col" class="column-id">ID</th>
                        <th scope="col">Fecha de Inicio</th>
                        <th scope="col">Juego</th>
                        <th scope="col">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_partidasCreadas"></tbody>
                </table>
            </div>
        </div>

        <!-- Modal para unirse a un grupo -->
        <div class="modal fade" id="modalUnirseGrupo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Unirse a un grupo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label for="select-grupo">Selecciona un grupo:</label>
                            <select id="select-grupo" class="form-select">
                                <option value="">Selecciona un grupo...</option>
                            </select>
                        </div>
                        <div id="usuarios-grupo" class="usuarios-container">
                            <!-- Aquí se mostrarán los usuarios del grupo seleccionado -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn-unirse-grupo">Unirse al grupo</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/lobby/lobby.js') }}"></script>
@endsection
