document.addEventListener('DOMContentLoaded', function () {
    let map, currentLocationMarker;
    let currentLayer = 'normal';

    // Definición de las capas base
    let baseLayers = {
        "normal": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }),
        "satellite": L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '&copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        })
    };

    // Inicialización del mapa
    map = L.map('map', {
        layers: [baseLayers[currentLayer]], // Capa inicial
        zoomControl: false // Desactivamos los controles de zoom predeterminados
    }).setView([41.3526, 2.1083], 14);

    // LayerGroup para los marcadores
    let marcadoresLayer = L.layerGroup().addTo(map);
    let marcadoresActivos = [];

    // Función para crear un marcador personalizado
    function crearMarcador(marcador) {
        let iconoClass = 'fa-info';

        switch (marcador.etiqueta.nombre) {
            case 'monumentos':
                iconoClass = 'fa-monument';
                break;
            case 'hoteles':
                iconoClass = 'fa-hotel';
                break;
            case 'interes':
                iconoClass = 'fa-info';
                break;
        }

        const icon = L.divIcon({
            html: `<i class="fas ${iconoClass}" style="color: #0066CC;"></i>`,
            className: 'custom-div-icon',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        const marker = L.marker([marcador.latitud, marcador.longitud], { icon });
        marker.bindPopup(`
            <div class="marker-popup">
                <h5>${marcador.nombre}</h5>
                <p>${marcador.descripcion}</p>
            </div>
        `);
        marker.etiqueta = marcador.etiqueta.nombre;
        return marker;
    }

    // Función para actualizar los marcadores según una etiqueta
    function actualizarMarcadores(etiquetaNombre = 'all') {
        marcadoresLayer.clearLayers();
        marcadoresActivos = [];

        window.marcadores.forEach(marcador => {
            if (etiquetaNombre === 'all' || marcador.etiqueta.nombre === etiquetaNombre) {
                const marker = crearMarcador(marcador);
                marcadoresLayer.addLayer(marker);
                marcadoresActivos.push(marker);
            }
        });

        if (marcadoresActivos.length > 0) {
            const grupo = L.featureGroup(marcadoresActivos);
            map.fitBounds(grupo.getBounds(), { padding: [50, 50] });
        }
    }

    // Event listeners para los botones de filtro
    document.querySelectorAll('.btn-tag').forEach(button => {
        button.addEventListener('click', (e) => {
            document.querySelectorAll('.btn-tag').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            actualizarMarcadores(button.dataset.tag);
        });
    });

    // Actualizar marcadores al cargar la página
    actualizarMarcadores();

    // Funcionalidad de búsqueda
    const searchInput = document.querySelector('.form-control');
    const searchButton = document.querySelector('.btn-search');

    function buscarMarcadores(query) {
        if (!query.trim()) {
            actualizarMarcadores();
            return;
        }
        query = query.toLowerCase();
        marcadoresLayer.clearLayers();
        marcadoresActivos = [];

        window.marcadores.forEach(marcador => {
            if (marcador.nombre.toLowerCase().includes(query) || marcador.descripcion.toLowerCase().includes(query)) {
                const marker = crearMarcador(marcador);
                marcadoresLayer.addLayer(marker);
                marcadoresActivos.push(marker);
            }
        });

        if (marcadoresActivos.length > 0) {
            const grupo = L.featureGroup(marcadoresActivos);
            map.fitBounds(grupo.getBounds(), { padding: [50, 50] });
        }
    }

    searchButton.addEventListener('click', () => buscarMarcadores(searchInput.value));
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') buscarMarcadores(searchInput.value);
    });

    // Obtener la ubicación actual
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userCoords = [position.coords.latitude, position.coords.longitude];
                console.log('Ubicación obtenida:', userCoords);

                if (!currentLocationMarker) {
                    currentLocationMarker = L.marker(userCoords, { title: "Tu ubicación" }).addTo(map);
                } else {
                    currentLocationMarker.setLatLng(userCoords);
                }
                map.setView(userCoords, 14); // Centrar el mapa en la ubicación del usuario
            },
            (error) => {
                console.error('Error al obtener la ubicación:', error);
                let errorMessage = 'No se pudo obtener tu ubicación.';

                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += ' El usuario ha denegado el acceso a la ubicación.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += ' La información de ubicación no está disponible.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += ' Se ha excedido el tiempo de espera para obtener la ubicación.';
                        break;
                    default:
                        errorMessage += ' Error desconocido.';
                        break;
                }

                alert(errorMessage);
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    } else {
        alert('Tu navegador no soporta geolocalización.');
    }

    // Botones de zoom in y zoom out
    document.getElementById('zoomIn')?.addEventListener('click', () => map.setZoom(map.getZoom() + 1));
    document.getElementById('zoomOut')?.addEventListener('click', () => map.setZoom(map.getZoom() - 1));

    // Cambio de capa (normal/satélite)
    document.getElementById('toggleSatellite')?.addEventListener('click', () => {
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === 'normal' ? 'satellite' : 'normal';
        baseLayers[currentLayer].addTo(map);
    });
});