// Variables globales
let filtroFecha = '';
let filtroTipoJuego = '';
let modalUnirseGrupo = null;

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
                    <td class="column-id">${partida.id}</td>
                    <td>${fecha}</td>
                    <td>
                        <div class="d-flex flex-column">
                            <div>
                                <span class="badge bg-info">Juego</span>
                                <span>${partida.juego}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button class="btn btn-primary btn-sm btn-unirse" onclick="mostrarGruposPartida(${partida.id})">
                            Unirse
                        </button>
                        ${partida.creador && partida.creador.email === document.querySelector('meta[name="user-email"]').content && 
                          partida.grupos_completos >= 2 ? 
                            `<button class="btn btn-success btn-sm btn-empezar" onclick="empezarPartida(${partida.id})">
                                Empezar
                            </button>` : ''}
                        ${partida.creador && partida.creador.email === document.querySelector('meta[name="user-email"]').content && 
                          partida.grupos_completos < 2 ? 
                            `<span class="badge bg-warning ms-2" title="Se necesitan al menos 2 grupos con 4 jugadores">
                                Esperando jugadores (${partida.grupos_completos}/2 grupos completos)
                            </span>` : ''}
                    </td>
                </tr>
            `;
            tbody.innerHTML += fila;
        });
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al cargar las partidas', 'error');
    });
}

// Función para mostrar los grupos de una partida
function mostrarGruposPartida(partidaId) {
    // Mostrar el modal primero
    if (modalUnirseGrupo) {
        modalUnirseGrupo.show();
    }

    // Obtener los grupos de la partida
    fetch(`/mapa/grupos/${partidaId}`, {
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

        const modalBody = document.querySelector('#modalUnirseGrupo .modal-body');
        if (!modalBody) return;

        // Limpiar el contenido anterior
        modalBody.innerHTML = '';

        // Mostrar los grupos
        data.grupos.forEach(grupo => {
            const grupoElement = document.createElement('div');
            grupoElement.className = 'grupo-container mb-3 p-3 border rounded';
            
            // Crear el contenido del grupo
            const usuariosHtml = grupo.usuarios.map(usuario => `
                <div class="usuario-item">
                    <span>${usuario.nombre}</span>
                    ${usuario.is_owner ? '<span class="badge bg-primary ms-2">Líder</span>' : ''}
                </div>
            `).join('');

            grupoElement.innerHTML = `
                <h5>Grupo ${grupo.id}</h5>
                <div class="usuarios-list mb-2">
                    ${usuariosHtml}
                </div>
                <button class="btn btn-primary btn-sm" onclick="unirseAGrupo(${grupo.id}, ${partidaId})">
                    Unirse a este grupo
                </button>
            `;

            modalBody.appendChild(grupoElement);
        });

        const modalFooter = document.querySelector('#modalUnirseGrupo .modal-footer');
        if (modalFooter) {
            modalFooter.innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-empezar-partida" disabled>Empezar partida</button>
            `;

            // Verificar si hay 4 usuarios en ambos grupos
            const btnEmpezarPartida = document.getElementById('btn-empezar-partida');
            const gruposCompletos = data.grupos.every(grupo => grupo.usuarios.length >= 4);

            if (gruposCompletos) {
                btnEmpezarPartida.disabled = false;
                btnEmpezarPartida.title = 'Ambos grupos están completos, puedes empezar la partida';
            } else {
                btnEmpezarPartida.disabled = true;
                // Contar cuántos jugadores faltan en cada grupo
                const faltanJugadores = data.grupos.map(grupo => {
                    const jugadoresFaltantes = 4 - grupo.usuarios.length;
                    return jugadoresFaltantes > 0 ? `${jugadoresFaltantes} en ${grupo.nombre}` : null;
                }).filter(x => x).join(' y ');
                btnEmpezarPartida.title = `Faltan ${faltanJugadores} jugadores`;
            }

            // Capturar el evento onclick del botón empezar partida
            btnEmpezarPartida.onclick = function() {
                if (gruposCompletos) {
                    // Aquí puedes agregar la lógica para empezar la partida
                    Swal.fire({
                        title: '¡Grupos completos!',
                        text: 'La partida puede comenzar',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    // Cerrar el modal
                    modalUnirseGrupo.hide();
                } else {
                    Swal.fire({
                        title: 'No se puede empezar',
                        text: 'Se necesitan 4 usuarios en cada grupo para empezar la partida',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
            };
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al cargar los grupos', 'error');
    });
}

// Función para unirse a un grupo
function unirseAGrupo(grupoId, partidaId) {
    // Deshabilitar el botón para evitar múltiples clics
    const btnUnirseGrupo = event.target;
    if (btnUnirseGrupo) {
        btnUnirseGrupo.disabled = true;
    }

    fetch('/mapa/unirse-grupo', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            grupo_id: grupoId,
            partida_id: partidaId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        Swal.fire('¡Éxito!', 'Te has unido al grupo correctamente', 'success');
        if (modalUnirseGrupo) {
            modalUnirseGrupo.hide();
        }
        cargarPartidas();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al unirse al grupo', 'error');
        // Reactivar el botón en caso de error
        if (btnUnirseGrupo) {
            btnUnirseGrupo.disabled = false;
        }
    });
}

// Función para empezar la partida
function empezarPartida(partidaId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Quieres empezar la partida ahora?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, empezar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/mapa/empezar-partida/${partidaId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
                
                Swal.fire('¡Éxito!', 'La partida ha comenzado', 'success');
                window.location.href = `/mapa/juego?partida_id=${partidaId}`;
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', error.message || 'Error al empezar la partida', 'error');
            });
        }
    });
}

// Función para crear partida
function crearPartida() {
    const juegoId = document.getElementById('juego_id').value;
        
    if (!juegoId) {
        Swal.fire('Error', 'Por favor, selecciona un juego', 'error');
        return;
    }

    fetch('/mapa/partida', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            juego_id: juegoId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        Swal.fire('¡Éxito!', 'Partida creada correctamente', 'success');
        document.getElementById('juego_id').value = '';
        cargarPartidas();
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', error.message || 'Error al crear la partida', 'error');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el modal cuando el documento esté listo
    const modalElement = document.getElementById('modalUnirseGrupo');
    if (modalElement) {
        modalUnirseGrupo = new bootstrap.Modal(modalElement);
    }

    // Manejar los filtros
    const filtroFechaElement = document.getElementById('filtroFecha');
    if (filtroFechaElement) {
        filtroFechaElement.addEventListener('change', function(e) {
            filtroFecha = e.target.value;
            cargarPartidas();
        });
    }

    const filtroTipoJuegoElement = document.getElementById('filtroTipoJuego');
    if (filtroTipoJuegoElement) {
        filtroTipoJuegoElement.addEventListener('change', function(e) {
            filtroTipoJuego = e.target.value;
            cargarPartidas();
        });
    }

    // Manejar el botón de limpiar filtros
    const limpiarFiltrosBtn = document.getElementById('limpiarFiltros');
    if (limpiarFiltrosBtn) {
        limpiarFiltrosBtn.addEventListener('click', function() {
            filtroFecha = '';
            filtroTipoJuego = '';
            
            if (filtroFechaElement) {
                filtroFechaElement.value = '';
            }
            if (filtroTipoJuegoElement) {
                filtroTipoJuegoElement.value = '';
            }
            
            // Recargar las partidas
            cargarPartidas();
        });
    }

    const crearPartidaBtn = document.getElementById('crear_partida');
    if (crearPartidaBtn) {
        crearPartidaBtn.addEventListener('click', function(e) {
            e.preventDefault();
            crearPartida();
        });
    }

    // Cargar partidas al inicio
    cargarPartidas();
});
