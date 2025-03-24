document.addEventListener('DOMContentLoaded', function () {
    let map, currentLocationMarker;
    let currentLayer = 'normal';
    let allMarkers = [];

    // Icono personalizado para la ubicación del usuario con FontAwesome
    const userLocationIcon = L.divIcon({
        className: 'custom-user-icon', // Clase CSS para personalizar el marcador
        html: '<i class="fa-solid fa-map-pin"></i>', // Icono de FontAwesome
        iconSize: [30, 30], // Tamaño del icono
        iconAnchor: [15, 30], // Punto de anclaje del icono
        popupAnchor: [0, -30] // Punto donde aparecerá el popup
    });

    // Capas base disponibles
    let baseLayers = {
        "normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        "satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri'
        })
    };

    // Función para obtener la ubicación del usuario
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
                    currentLocationMarker = L.marker(userCoords, { icon: userLocationIcon }).addTo(map);

                    return true;
                },
                (error) => {
                    console.error('Error:', error);
                    alert('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.');
                    return false;
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización');
        }
    }

    // Función para centrar el mapa en la ubicación del usuario
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
                    currentLocationMarker = L.marker(userCoords, { icon: userLocationIcon }).addTo(map);
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

    // Cargar los marcadores en el mapa
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

    // Filtrar los marcadores según la etiqueta seleccionada
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

    // Función para actualizar la clase "active" de las etiquetas
    function updateActiveTag(selectedTag) {
        // Eliminar la clase 'active' de todos los botones
        document.querySelectorAll('.btn-tag').forEach(button => {
            button.classList.remove('active');
        });

        // Añadir la clase 'active' solo al botón seleccionado
        const selectedButton = document.querySelector(`.btn-tag[data-tag="${selectedTag}"]`);
        if (selectedButton) {
            selectedButton.classList.add('active');
        }
    }

    // Añadir eventos a los botones para filtrar y actualizar la clase "active"
    document.querySelectorAll('.filter-tag').forEach(button => {
        button.addEventListener('click', function () {
            const selectedTag = this.dataset.tag;
            filterMarkers(selectedTag);
            updateActiveTag(selectedTag);
        });
    });

    // Función para manejar el botón "Todos" y mostrar todos los marcadores
    document.querySelector('.btn-tag[data-tag="all"]').addEventListener('click', function () {
        filterMarkers('all');
        updateActiveTag('all');
    });

    // Funcionalidades del panel de controles
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

    // Iniciar con la ubicación del usuario
    if (getLocation()) {
        // Actualizar la ubicación cada 2 segundos
        setInterval(getLocation, 2000);
    }
});