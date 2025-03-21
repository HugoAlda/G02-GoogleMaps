document.addEventListener('DOMContentLoaded', function () {
    let map, currentLocationMarker;
    let currentLayer = 'normal';

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
                        map = L.map('map', {zoomControl: false}).setView(userCoords, 17);
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
                        map = L.map('map', {zoomControl: false}).setView(userCoords, 17);
                        baseLayers[currentLayer].addTo(map);
                        loadMarkers(); // Cargar los marcadores en el mapa
                    } else {
                        map.setView(userCoords, 17);
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
            window.marcadores.forEach(marcador => {
                L.marker([marcador.latitud, marcador.longitud])
                    .addTo(map)
                    .bindPopup(`<strong>${marcador.nombre}</strong><br>${marcador.descripcion}`);
            });
        } else {
            console.error("No se encontraron marcadores");
        }
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

    getLocation();
    setInterval(getLocation, 2000);
});
