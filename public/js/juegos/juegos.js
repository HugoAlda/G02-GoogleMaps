function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371e3;
    const rad = Math.PI / 180;
    const dLat = (lat2 - lat1) * rad;
    const dLon = (lon2 - lon1) * rad;

    const a = Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * rad) * Math.cos(lat2 * rad) *
        Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

document.addEventListener("DOMContentLoaded", function () {
    const guardado = localStorage.getItem('indicePunto');
    window.indicePunto = guardado ? parseInt(guardado) : 0;
    window.puntosJuego = [];

    document.getElementById("btn-responder").addEventListener("click", enviarRespuesta);

    fetch(`/api/todos-puntos/${window.juegoId}`)
        .then(res => res.json())
        .then(data => {
            window.puntosJuego = data;
            mostrarMapa();
        });

    cargarPunto(window.juegoId, window.indicePunto);
});

function cargarPunto(juegoId, indice = 0) {
    fetch(`/api/punto-control/${juegoId}/${indice}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                localStorage.removeItem('indicePunto');
                localStorage.removeItem('puntosSuperados');
                Swal.fire({
                    icon: 'success',
                    title: '¡Has completado el juego! 🎉',
                    text: '¡Felicidades por llegar al final!',
                    confirmButtonText: 'Salir',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then(() => {
                    window.location.href = "/mapa";
                });

                document.getElementById("popup-pista").style.display = 'none';
                return;
            }

            document.getElementById("titulo-pista").textContent = data.nombre;
            document.getElementById("acertijo-pista").textContent = data.acertijo;
            window.respuestaCorrecta = data.respuesta;
            window.ubicacionPunto = {
                lat: data.latitud,
                lng: data.longitud
            };

            if (window.ubicacionJugador) {
                actualizarRango(window.ubicacionJugador);
            }
        })
        .catch(error => console.error('Error al cargar el punto:', error));
}

function enviarRespuesta() {
    const respuestaUsuario = normalizarTexto(document.getElementById('respuesta').value);
    const respuestaCorrecta = normalizarTexto(window.respuestaCorrecta);

    if (respuestaUsuario === respuestaCorrecta) {
        Swal.fire({
            icon: 'success',
            title: '¡Correcto!',
            text: 'Avanzas al siguiente punto',
            timer: 1500,
            showConfirmButton: false
        });

        const puntoActual = {
            lat: window.ubicacionPunto.lat,
            lng: window.ubicacionPunto.lng
        };

        const superados = JSON.parse(localStorage.getItem('puntosSuperados')) || [];
        superados.push(puntoActual);
        localStorage.setItem('puntosSuperados', JSON.stringify(superados));

        L.marker([puntoActual.lat, puntoActual.lng])
            .addTo(map)
            .bindPopup("¡Punto superado!").openPopup();

        if (window.ubicacionJugador) {
            mostrarRuta(window.ubicacionJugador, [puntoActual.lat, puntoActual.lng]);
        }

        window.indicePunto++;
        localStorage.setItem('indicePunto', window.indicePunto);
        document.getElementById('respuesta').value = "";
        cargarPunto(window.juegoId, window.indicePunto);
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Incorrecto',
            text: 'Intenta de nuevo'
        });
    }
}

function normalizarTexto(texto) {
    return texto
        .toLowerCase()
        .trim()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");
}

let map, currentLocationMarker, pistaCircle, rutaPanel, rutaPanelVisible = false;

function mostrarMapa() {
    let currentLayer = 'normal';

    const baseLayers = {
        "normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        "satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri, USGS, etc.'
        })
    };

    map = L.map('map', { zoomControl: false }).setView([0, 0], 17);
    baseLayers[currentLayer].addTo(map);

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userCoords = [position.coords.latitude, position.coords.longitude];
                window.ubicacionJugador = userCoords;
                map.panTo(userCoords);

                if (currentLocationMarker) {
                    currentLocationMarker.setLatLng(userCoords);
                } else {
                    currentLocationMarker = L.marker(userCoords).addTo(map)
                        .bindPopup("¡Estás aquí!").openPopup();
                }

                actualizarRango(userCoords);
                dibujarPuntosSuperados();
            },
            (error) => {
                console.error('Error:', error);
                alert('No se pudo obtener tu ubicación.');
            },
            {
                enableHighAccuracy: true,
                maximumAge: 0
            }
        );
    } else {
        alert('Tu navegador no soporta geolocalización.');
    }

    configurarControlesPersonalizados();
}

function dibujarPuntosSuperados() {
    const superados = JSON.parse(localStorage.getItem('puntosSuperados')) || [];

    superados.forEach(p => {
        const coords = [p.lat, p.lng];
        L.marker(coords).addTo(map).bindPopup("¡Punto superado!");

        if (window.ubicacionJugador) {
            mostrarRuta(window.ubicacionJugador, coords);
        }
    });
}

function mostrarRuta(origen, destino) {
    if (window.rutaControl) {
        map.removeControl(window.rutaControl);
    }

    window.rutaControl = L.Routing.control({
        waypoints: [
            L.latLng(origen[0], origen[1]),
            L.latLng(destino[0], destino[1])
        ],
        routeWhileDragging: false,
        draggableWaypoints: false,
        addWaypoints: false,
        show: true,
        createMarker: () => null
    }).addTo(map);

    setTimeout(() => {
        rutaPanel = document.querySelector('.leaflet-routing-container');
        if (rutaPanel) {
            rutaPanel.style.display = "none";
        }
    }, 300);
}

function actualizarRango(ubicacionJugador) {
    if (!window.ubicacionPunto) return;

    const dist = calcularDistancia(
        ubicacionJugador[0],
        ubicacionJugador[1],
        window.ubicacionPunto.lat,
        window.ubicacionPunto.lng
    );

    if (pistaCircle) {
        pistaCircle.setLatLng(ubicacionJugador).setRadius(dist + 50);
    } else {
        pistaCircle = L.circle(ubicacionJugador, {
            color: 'blue',
            fillColor: 'blue',
            fillOpacity: 0.2,
            radius: dist + 50
        }).addTo(map);
    }
}

function configurarControlesPersonalizados() {
    const zoomInBtn = document.getElementById("zoomIn");
    const zoomOutBtn = document.getElementById("zoomOut");
    const centerUserBtn = document.getElementById("centerUser");
    const toggleRoutePanelBtn = document.getElementById("toggleRoutePanel");

    if (zoomInBtn) {
        zoomInBtn.addEventListener("click", () => {
            if (map) map.setZoom(map.getZoom() + 1);
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener("click", () => {
            if (map) map.setZoom(map.getZoom() - 1);
        });
    }

    if (centerUserBtn) {
        centerUserBtn.addEventListener("click", () => {
            if (window.ubicacionJugador && map) {
                map.setView(window.ubicacionJugador, 17);
            } else {
                alert("Ubicación del jugador no disponible todavía.");
            }
        });
    }

    if (toggleRoutePanelBtn) {
        toggleRoutePanelBtn.addEventListener("click", () => {
            if (!rutaPanel) {
                rutaPanel = document.querySelector('.leaflet-routing-container');
            }
    
            if (rutaPanel) {
                rutaPanelVisible = !rutaPanelVisible;
                rutaPanel.style.display = rutaPanelVisible ? "block" : "none";
    
                rutaPanel.style.zIndex = "4000"; // Mayor que 1000
            }
            if (window.innerWidth <= 768) {
                rutaPanel.style.width = "280px";
                rutaPanel.style.fontSize = "13px";
            }
        });
    }
}
