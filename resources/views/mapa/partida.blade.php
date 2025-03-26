@extends('layout.lobby')

@section('title', 'Lobby Inicial')

@section('content')
    <main>
        <div class="container">
            <div class="crear_partida">
                <h4>Empieza una partida</h4>
                <form id="form_crear_partida"> {{-- Quitamos action y method --}}
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

                    <button type="button" class="btn btn-primary" id="crear_partida">Crear Partida</button> 
                    {{-- Cambiado a type="button" para que no haga submit tradicional --}}
                </form>
            </div>
            <div class="buscar_partida">
                <div class="filtros">
                    <h4>Filtros</h4>
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
                        <div class="col-md-4 d-flex align-items-end">
                            <button id="limpiarFiltros" class="btn btn-secondary">Limpiar Filtros</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="partidas_creadas">
                <h4>Partidas Creadas</h4>
                <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Fecha de Inicio</th>
                        <th scope="col">Jugadores de la Partida</th>
                        <th scope="col">Acciones</th>
                      </tr>
                    </thead>
                    <tbody id="table_partidasCreadas"></tbody>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/lobby/lobby.js') }}"></script>
@endsection
