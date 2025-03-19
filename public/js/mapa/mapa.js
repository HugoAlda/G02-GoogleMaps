document.addEventListener('DOMContentLoaded', function() {
    let map, currentLocationMarker;
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

    // Función para obtener la ubicación del usuario
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userCoords = [position.coords.latitude, position.coords.longitude];
                    
                    if (!map) {
                        // Primera vez - inicializar el mapa
                        map = L.map('map').setView(userCoords, 17);
                        baseLayers[currentLayer].addTo(map);
                    } else {
                        // Actualizar vista
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

    // Evento para el botón de centrar en la ubicación actual
    document.getElementById('centerUser').addEventListener('click', getLocation);

    // Evento para cambiar el tipo de mapa
    document.getElementById('toggleSatellite').addEventListener('click', () => {
        if (!map) return;
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === 'normal' ? 'satellite' : 'normal';
        baseLayers[currentLayer].addTo(map);
    });

    // Iniciar con la ubicación del usuario
    getLocation();

    // Actualizar la ubicación cada 2 segundos
    setInterval(getLocation, 2000);
});