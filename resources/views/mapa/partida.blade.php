
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
                    <!-- filtros -->
                </div>
            </div>
            <div class="partidas_creadas">
                <!-- mis partidas -->
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script src="{{ asset('js/lobby/lobby.js') }}"></script>
@endsection



