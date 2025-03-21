document.addEventListener('DOMContentLoaded', function () {
    let map, currentLocationMarker;
    let currentLayer = 'normal';
    let allMarkers = [];

    // Capas base disponibles
    let baseLayers = {
        "normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        "satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri'
        })
    };

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userCoords = [position.coords.latitude, position.coords.longitude];

                    if (!map) {
                        map = L.map('map', {zoomControl: false}).setView(userCoords, 16);
                        baseLayers[currentLayer].addTo(map);
                        loadMarkers(); // Cargar los marcadores en el mapa
                    }

                    if (currentLocationMarker) {
                        map.removeLayer(currentLocationMarker);
                    }
                    currentLocationMarker = L.marker(userCoords).addTo(map);
                },
                (error) => {
                    console.error('Error:', error);
                    alert('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.');
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización');
        }
    }

    function centerLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userCoords = [position.coords.latitude, position.coords.longitude];

                    if (!map) {
                        map = L.map('map', {zoomControl: false}).setView(userCoords, 16);
                        baseLayers[currentLayer].addTo(map);
                        loadMarkers(); // Cargar los marcadores en el mapa
                    } else {
                        map.setView(userCoords, 16);
                    }

                    if (currentLocationMarker) {
                        map.removeLayer(currentLocationMarker);
                    }
                    currentLocationMarker = L.marker(userCoords).addTo(map);
                },
                (error) => {
                    console.error('Error:', error);
                    alert('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.');
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización');
        }
    }

    function loadMarkers() {
        if (window.marcadores) {
            allMarkers = window.marcadores.map(marcador => {
                return L.marker([marcador.latitud, marcador.longitud])
                    .addTo(map)
                    .bindPopup(`<strong>${marcador.nombre}</strong><br>${marcador.descripcion}`)
                    .setOpacity(1); // Mostrar todos los marcadores al inicio
            });
        } else {
            console.error("No se encontraron marcadores");
        }
    }

    function filterMarkers(tag) {
        allMarkers.forEach((marker, index) => {
            const marcador = window.marcadores[index];
    
            if (marcador.etiqueta === tag || tag === "all") {
                marker.addTo(map); // Mostrar marcador
            } else {
                map.removeLayer(marker); // Ocultar marcador
            }
        });
    }

    document.getElementById('centerUser').addEventListener('click', centerLocation);

    document.getElementById('zoomIn').addEventListener('click', () => {
        if (map) map.setZoom(map.getZoom() + 1);
    });

    document.getElementById('zoomOut').addEventListener('click', () => {
        if (map) map.setZoom(map.getZoom() - 1);
    });

    document.getElementById('toggleSatellite').addEventListener('click', () => {
        if (!map) return;
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === 'normal' ? 'satellite' : 'normal';
        baseLayers[currentLayer].addTo(map);
    });

    // Agregar evento a las etiquetas para filtrar
    document.querySelectorAll('.filter-tag').forEach(button => {
        button.addEventListener('click', function () {
            const selectedTag = this.dataset.tag;
            filterMarkers(selectedTag);
        });
    });

    getLocation();
    setInterval(getLocation, 2000);
});