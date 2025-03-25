document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("btn-responder").addEventListener("click", enviarRespuesta);

    mostrarMapa();
    cargarPunto(window.juegoId, window.indicePunto);
});

function cargarPunto(juegoId, indice = 0) {
    fetch(`/api/punto-control/${juegoId}/${indice}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert("No hay más puntos. ¡Has terminado la gimcana!");
                document.getElementById("popup-pista").style.display = 'none';
                return;
            }

            document.getElementById("titulo-pista").textContent = data.nombre;
            document.getElementById("acertijo-pista").textContent = data.acertijo;
            window.respuestaCorrecta = data.respuesta.toLowerCase().trim();
        })
        .catch(error => console.error('Error al cargar el punto:', error));
}

// Función cuando el usuario envía su respuesta
function enviarRespuesta() {
    const respuesta = document.getElementById('respuesta').value.trim().toLowerCase();

    if (respuesta === window.respuestaCorrecta) {
        Swal.fire({
            icon: 'success',
            title: '¡Correcto!',
            text: 'Avanzas al siguiente punto',
            timer: 1500,
            showConfirmButton: false
        });
        window.indicePunto++;
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

// Función para mostrar el mapa de Leaflet con la ubicación del usuario y actualizar cada 5 segundos
function mostrarMapa(){
    let map, currentLocationMarker, pistaCircle;
    let currentLayer = 'normal';

    // Capas base disponibles
    let baseLayers = {
        "normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        "satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        })
    };

    // Inicializar el mapa solo si no existe
    if (!map) {
        map = L.map('map', { zoomControl: false }).setView([0, 0], 17); // Vista inicial (hasta obtener la ubicación)
        baseLayers[currentLayer].addTo(map);
    }

    // Función para obtener y actualizar la ubicación del usuario cada 5 segundos
    function trackLocation() {
        if (navigator.geolocation) {
            setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userCoords = [position.coords.latitude, position.coords.longitude];
                        
                        map.setView(userCoords, 17); // Centrar el mapa en la ubicación del usuario

                        if (currentLocationMarker) {
                            currentLocationMarker.setLatLng(userCoords);
                        } else {
                            currentLocationMarker = L.marker(userCoords).addTo(map)
                                .bindPopup("¡Estás aquí!").openPopup();
                        }
                        
                        // Actualizar el círculo con un radio de 800m
                        if (pistaCircle) {
                            pistaCircle.setLatLng(userCoords);
                        } else {
                            pistaCircle = L.circle(userCoords, {
                                color: 'blue',
                                fillColor: 'blue',
                                fillOpacity: 0.2,
                                radius: 800 // Radio de 800m
                            }).addTo(map);
                        }
                    },
                    (error) => {
                        console.error('Error:', error);
                        alert('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.');
                    },
                    {
                        enableHighAccuracy: true, // Mejor precisión
                        maximumAge: 0 // No usar ubicaciones almacenadas
                    }
                );
            }, 5000); // Actualiza cada 5 segundos
        } else {
            alert('Tu navegador no soporta geolocalización en tiempo real');
        }
    }

    // Iniciar el seguimiento de ubicación
    trackLocation();
} 