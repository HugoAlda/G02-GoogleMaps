document.addEventListener('DOMContentLoaded', function () {
    let filtroFecha = '';
    let filtroTipoJuego = '';

    // Función para cargar las partidas
    function cargarPartidas() {
        const params = new URLSearchParams();
        if (filtroFecha) params.append('fecha', filtroFecha);
        if (filtroTipoJuego) params.append('tipo_juego', filtroTipoJuego);

        fetch(`/mapa/partidas?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            const tbody = document.getElementById('table_partidasCreadas');
            if (!tbody) return;

            tbody.innerHTML = '';
            
            if (data.partidas.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay partidas disponibles</td></tr>';
                return;
            }

            data.partidas.forEach(partida => {
                const fecha = new Date(partida.fecha_inicio).toLocaleString();
                const fila = `
                    <tr>
                        <td>${partida.id}</td>
                        <td>${fecha}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="mb-2">
                                    <span class="badge bg-primary">Creador</span>
                                    <strong>${partida.creador.nombre}</strong>
                                    <small class="text-muted d-block">${partida.creador.email}</small>
                                </div>
                                <div>
                                    <span class="badge bg-info">Juego</span>
                                    <span>${partida.juego}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-primary btn-sm btn-unirse" data-partida-id="${partida.id}">
                                Unirse
                            </button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });

            // Agregar event listeners a los botones de unirse
            document.querySelectorAll('.btn-unirse').forEach(button => {
                button.addEventListener('click', function() {
                    const partidaId = this.getAttribute('data-partida-id');
                    checkAndJoinGame(partidaId);
                });
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Error al cargar las partidas', 'error');
        });
    }

    // Función para verificar si el usuario puede unirse a una partida
    function checkAndJoinGame(partidaId) {
        fetch('/mapa/check-in-game', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            if (data.inGame) {
                Swal.fire('Atención', 'Ya estás en una partida. No puedes unirte a otra hasta que finalice la actual.', 'warning');
            } else {
                // Aquí iría el código para unirse a la partida
                Swal.fire('Éxito', 'Te has unido a la partida correctamente.', 'success');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Error al verificar el estado del jugador', 'error');
        });
    }

    // Manejar los filtros
    document.getElementById('filtroFecha').addEventListener('change', function(e) {
        filtroFecha = e.target.value;
        cargarPartidas();
    });

    document.getElementById('filtroTipoJuego').addEventListener('change', function(e) {
        filtroTipoJuego = e.target.value;
        cargarPartidas();
    });

    // Botón para limpiar filtros
    document.getElementById('limpiarFiltros').addEventListener('click', function() {
        document.getElementById('filtroFecha').value = '';
        document.getElementById('filtroTipoJuego').value = '';
        filtroFecha = '';
        filtroTipoJuego = '';
        cargarPartidas();
    });

    // Cargar partidas al iniciar y cada 30 segundos
    cargarPartidas();
    setInterval(cargarPartidas, 30000);

    // Manejar el envío del formulario para crear una partida
    document.getElementById('crear_partida').addEventListener('click', function (e) {
        // prevenir el envio del boton
        e.preventDefault();
        // Obtener el valor del input "juego_id" y el token CSRF-TOKEN  para enviar la petición al controlador
        let juego_id = document.getElementById('juego_id').value;
        let token = document.querySelector('input[name="_token"]').value;

        // Validar que el juego_id no esté vacío y mostrar un mensaje de error si es así.
        if (!juego_id) {
            Swal.fire('Atención', 'Selecciona un juego antes de continuar.', 'warning');
            return;
        }

        // Enviar la petición POST al controlador para crear la partida con el juego_id 
        // y el token CSRF-TOKEN  para la validación de seguridad.
        fetch('/mapa/partida', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ juego_id })
        })
        .then(response => {
            if (!response.ok) {
                // Si el usuario ya ha creado una partida da error, ya que la respuesta va segun las partidas creadas
                throw new Error('Ya existe una partida');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            Swal.fire('Éxito', data.message, 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Error al crear la partida', 'error');
        });
    });
});
