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

    // Inicializar el mapa
    map = L.map('map').setView([41.3526, 2.1083], 14);

    // Añadir capa de OpenStreetMap
    baseLayers[currentLayer].addTo(map);

    // Almacenar todos los marcadores
    let marcadoresLayer = L.layerGroup().addTo(map);
    let marcadoresActivos = [];

    // Función para crear un marcador con icono personalizado según la etiqueta
    function crearMarcador(marcador) {
        let iconoClass = 'fa-info'; // Icono por defecto

        // Asignar icono según la etiqueta
        switch(marcador.etiqueta.nombre) {
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

    // Función para actualizar los marcadores visibles
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

        // Si hay marcadores visibles, ajustar el mapa para mostrarlos todos
        if (marcadoresActivos.length > 0) {
            const grupo = L.featureGroup(marcadoresActivos);
            map.fitBounds(grupo.getBounds(), { padding: [50, 50] });
        }
    }

    // Manejar clics en los botones de etiquetas
    document.querySelectorAll('.btn-tag').forEach(button => {
        button.addEventListener('click', (e) => {
            // Remover clase active de todos los botones
            document.querySelectorAll('.btn-tag').forEach(btn => {
                btn.classList.remove('active');
            });

            // Añadir clase active al botón clickeado
            button.classList.add('active');

            // Actualizar marcadores
            const etiquetaNombre = button.dataset.tag;
            actualizarMarcadores(etiquetaNombre);
        });
    });

    // Inicializar marcadores
    actualizarMarcadores();

    // Búsqueda de marcadores
    const searchInput = document.querySelector('.form-control');
    const searchButton = document.querySelector('.btn-search');

    function buscarMarcadores(query) {
        if (!query.trim()) {
            actualizarMarcadores(); // Si la búsqueda está vacía, mostrar todos
            return;
        }

        query = query.toLowerCase();
        
        marcadoresLayer.clearLayers();
        marcadoresActivos = [];

        window.marcadores.forEach(marcador => {
            if (marcador.nombre.toLowerCase().includes(query) || 
                marcador.descripcion.toLowerCase().includes(query)) {
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

    searchButton.addEventListener('click', () => {
        buscarMarcadores(searchInput.value);
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            buscarMarcadores(searchInput.value);
        }
    });

    // Evento para el botón de centrar en la ubicación actual
    document.getElementById('centerUser').addEventListener('click', () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userCoords = [position.coords.latitude, position.coords.longitude];
                    
                    if (!currentLocationMarker) {
                        currentLocationMarker = L.marker(userCoords).addTo(map);
                    } else {
                        currentLocationMarker.setLatLng(userCoords);
                    }

                    map.setView(userCoords, 17);
                },
                (error) => {
                    console.error('Error:', error);
                    alert('No se pudo obtener tu ubicación. Verifica los permisos de ubicación.');
                }
            );
        } else {
            alert('Tu navegador no soporta geolocalización');
        }
    });

    // Eventos para los botones de zoom
    document.getElementById('zoomIn').addEventListener('click', () => {
        if (map) map.setZoom(map.getZoom() + 1);
    });

    document.getElementById('zoomOut').addEventListener('click', () => {
        if (map) map.setZoom(map.getZoom() - 1);
    });

    // Evento para cambiar el tipo de mapa
    document.getElementById('toggleSatellite').addEventListener('click', () => {
        if (!map) return;
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === 'normal' ? 'satellite' : 'normal';
        baseLayers[currentLayer].addTo(map);
    });

    // Estilos para los iconos personalizados
    const style = document.createElement('style');
    style.textContent = `
        .custom-div-icon {
            background: none;
            border: none;
        }
        .custom-div-icon i {
            font-size: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .marker-popup {
            padding: 5px;
        }
        .marker-popup h5 {
            margin: 0 0 5px 0;
            color: #0066CC;
        }
        .marker-popup p {
            margin: 0;
            font-size: 14px;
        }
    `;
    document.head.appendChild(style);
});