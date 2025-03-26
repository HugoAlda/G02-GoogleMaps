document.addEventListener('DOMContentLoaded', () => {
    // Elementos del DOM para la carga y vista previa de imágenes
    const imageInput = document.getElementById("imagen");
    const imagePreview = document.getElementById("image-preview");
    const imagePreviewContainer = document.getElementById("image-preview-container");
    const removeImageButton = document.getElementById("remove-image");

    // Evento para mostrar la vista previa de la imagen seleccionada
    imageInput.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreviewContainer.classList.remove("d-none");
            };
            reader.readAsDataURL(file);
        }
    });

    // Evento para eliminar la imagen seleccionada
    removeImageButton.addEventListener("click", () => {
        imageInput.value = "";
        imagePreview.src = "";
        imagePreviewContainer.classList.add("d-none");
    });

    // Botón para añadir un punto en el mapa
    const buttonAddPoint = document.getElementById("button-add-point");
    buttonAddPoint.addEventListener("click", () => {
        let modal = bootstrap.Modal.getInstance(document.getElementById("modal-add-point"));
        modal.hide();

        const pointControls = document.getElementById("point-controls");
        const confirmButton = document.getElementById("confirm-add-point");
        const cancelButton = document.getElementById("cancel-add-point");

        pointControls.style.display = "block";
        confirmButton.disabled = true;

        // Manejador de eventos para capturar coordenadas en el mapa
        const mapClickHandler = (e) => console.log("Coordenadas del clic:", e.latlng);
        map.on("click", mapClickHandler);

        // Evento para cancelar la adición de un punto
        cancelButton.addEventListener("click", () => {
            pointControls.style.display = "none";
            map.off("click", mapClickHandler);
            modal.show();
        }, { once: true });
    });

    // Variables del mapa y marcador de ubicación actual
    let map, currentLocationMarker, currentLayer = 'normal', allMarkers = [];
    const userLocationIcon = L.divIcon({
        className: 'custom-user-icon',
        html: '<i class="fa-solid fa-map-pin"></i>',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    // Capas base del mapa (Normal y Satélite)
    const baseLayers = {
        normal: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { attribution: '&copy; Esri' })
    };

    // Inicializa el mapa con coordenadas dadas
    function initializeMap(coords) {
        map = L.map('map', { zoomControl: false }).setView(coords, 16);
        baseLayers[currentLayer].addTo(map);
        loadMarkers();
    }

    // Actualiza la ubicación del usuario en el mapa
    function updateLocation(coords) {
        if (!map) initializeMap(coords);
        if (currentLocationMarker) map.removeLayer(currentLocationMarker);
        currentLocationMarker = L.marker(coords, { icon: userLocationIcon }).addTo(map);
    }

    // Obtiene la ubicación actual del usuario
    function getLocation() {
        if (!navigator.geolocation) return alert('Tu navegador no soporta geolocalización');
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => updateLocation([coords.latitude, coords.longitude]),
            () => alert('No se pudo obtener tu ubicación. Verifica los permisos.')
        );
    }

    // Carga los marcadores predefinidos en el mapa
    function loadMarkers() {
        if (!window.marcadores) return console.error("No se encontraron marcadores");
        allMarkers = window.marcadores.map(marcador => {
            const marker = L.marker([marcador.latitud, marcador.longitud])
                .addTo(map)
                .bindPopup(`<strong>${marcador.nombre}</strong><br>${marcador.descripcion}`);
            
            // Añadir la etiqueta como propiedad del marcador
            marker.etiqueta = marcador.etiqueta;
            return marker;
        });
    }

    // Filtra los marcadores según una etiqueta
    function filterMarkers(tag) {
        allMarkers.forEach(marker => {
            tag === "all" || marker.etiqueta === tag ? 
                marker.addTo(map) : 
                map.removeLayer(marker);
        });
    }

    // Evento para filtrar los marcadores con los botones de etiquetas
    document.querySelectorAll('.filter-tag').forEach(button => {
        button.addEventListener('click', function () {
            filterMarkers(this.dataset.tag);
            document.querySelectorAll('.btn-tag').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Controles del mapa: centrar usuario, zoom in/out y alternar satélite
    document.getElementById('centerUser').addEventListener('click', getLocation);
    document.getElementById('zoomIn').addEventListener('click', () => map?.setZoom(map.getZoom() + 1));
    document.getElementById('zoomOut').addEventListener('click', () => map?.setZoom(map.getZoom() - 1));
    document.getElementById('toggleSatellite').addEventListener('click', () => {
        if (!map) return;
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === 'normal' ? 'satellite' : 'normal';
        baseLayers[currentLayer].addTo(map);
    });
    
    // Paginación de etiquetas
    const tagsContainer = document.querySelector('.tags-container');
    const tagsBar = document.querySelector('.tags-bar');
    const prevBtn = document.querySelector('.btn-pagination.prev');
    const nextBtn = document.querySelector('.btn-pagination.next');
    const pageIndicator = document.querySelector('.page-indicator');
    
    if (tagsContainer && tagsBar && prevBtn && nextBtn && pageIndicator) {
        const allTags = Array.from(document.querySelectorAll('.filter-tag'));
        const tagsPerPage = 2; // Mostrar 2 etiquetas por página (además de "Todos")
        let currentPage = 1;
        const totalPages = Math.ceil((allTags.length) / tagsPerPage);

        // Separar el botón "Todos" de las demás etiquetas
        const allButton = document.querySelector('.btn-tag[data-tag="all"]');
        const filterButtons = allTags.filter(btn => btn.dataset.tag !== "all");

        // Función para actualizar la visualización de etiquetas
        function updateTagsDisplay() {
            // Ocultar todas las etiquetas de filtro primero
            filterButtons.forEach(btn => {
                btn.style.display = 'none';
            });

            // Mostrar siempre el botón "Todos"
            allButton.style.display = 'flex';

            // Calcular qué etiquetas mostrar para la página actual
            const startIdx = (currentPage - 1) * tagsPerPage;
            const endIdx = startIdx + tagsPerPage;
            const tagsToShow = filterButtons.slice(startIdx, endIdx);

            // Mostrar las etiquetas correspondientes a la página actual
            tagsToShow.forEach(btn => {
                btn.style.display = 'flex';
            });

            // Actualizar estado de los botones de paginación
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage === totalPages;

            // Actualizar indicador de página
            pageIndicator.textContent = `${currentPage}/${totalPages}`;
        }

        // Eventos para los botones de paginación
        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                updateTagsDisplay();
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                updateTagsDisplay();
            }
        });

        // Inicializar la visualización
        updateTagsDisplay();

        // Asegurar que el scroll se mantenga visible al cambiar páginas
        tagsBar.scrollTo(0, 0);
    }

    // Obtiene la ubicación del usuario al cargar la página y la actualiza cada 2 segundos
    getLocation();
    setInterval(getLocation, 2000);
});