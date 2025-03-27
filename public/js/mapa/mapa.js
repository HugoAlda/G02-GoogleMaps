document.addEventListener("DOMContentLoaded", () => {
    // Elementos del DOM
    const imageInput = document.getElementById("imagen");
    const imagePreview = document.getElementById("image-preview");
    const imagePreviewContainer = document.getElementById("image-preview-container");
    const removeImageButton = document.getElementById("remove-image");
    const buttonAddPoint = document.getElementById("button-add-point");
    const form = document.getElementById("form-add-point");

    let map, currentMarker = null, currentLocationMarker, allMarkers = [], currentLayer = "normal";
    
    // Ícono de ubicación del usuario
    const userLocationIcon = L.divIcon({
        className: "custom-user-icon",
        html: '<i class="fa-solid fa-map-pin"></i>',
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

    // Capas base del mapa
    const baseLayers = {
        normal: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", { attribution: "&copy; OpenStreetMap contributors" }),
        satellite: L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", { attribution: "&copy; Esri" })
    };

    /*** Función para previsualizar imagen ***/
    if (imageInput) {
        imageInput.addEventListener("change", event => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove("d-none");
                };
                reader.readAsDataURL(file);
            }
        });
    }

    /*** Función para eliminar imagen seleccionada ***/
    if (removeImageButton) {
        removeImageButton.addEventListener("click", () => {
            imageInput.value = "";
            imagePreview.src = "";
            imagePreviewContainer.classList.add("d-none");
        });
    }

    /*** Función para inicializar el mapa ***/
    function initializeMap(coords) {
        map = L.map("map", { 
            zoomControl: false,
            preferCanvas: true // Mejor rendimiento para muchos marcadores
        }).setView(coords, 16);
        baseLayers[currentLayer].addTo(map);
        loadMarkers();
    }

    /*** Función para actualizar la ubicación del usuario ***/
    function updateLocation(coords) {
        if (!map) initializeMap(coords);
        if (currentLocationMarker) map.removeLayer(currentLocationMarker);
        currentLocationMarker = L.marker(coords, { 
            icon: userLocationIcon,
            zIndexOffset: 1000 // Asegurar que siempre esté encima
        }).addTo(map);
    }

    /*** Obtener ubicación del usuario ***/
    function getLocation() {
        if (!navigator.geolocation) return alert("Tu navegador no soporta geolocalización");
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => updateLocation([coords.latitude, coords.longitude]),
            () => alert("No se pudo obtener tu ubicación. Verifica los permisos."),
            {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );
    }

    /*** Función para crear un marcador ***/
    function createMarker(coords) {
        const redIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });
        return L.marker(coords, { icon: redIcon }).addTo(map);
    }

    /*** Función para eliminar un marcador ***/
    function removeMarker(marker) {
        if (marker && map.hasLayer(marker)) {
            map.removeLayer(marker);
        }
    }

    /*** Cargar los marcadores guardados ***/
    function loadMarkers() {
        if (!window.marcadores) return console.error("No se encontraron marcadores");
        
        // Limpiar marcadores existentes
        allMarkers.forEach(marker => map.removeLayer(marker));
        allMarkers = [];
        
        // Crear nuevos marcadores
        allMarkers = window.marcadores.map(marcador => {
            const marker = L.marker([marcador.latitud, marcador.longitud], {
                title: marcador.nombre,
                alt: marcador.descripcion,
                riseOnHover: true
            }).addTo(map);
            
            marker.bindPopup(`
                <div class="marker-popup">
                    <h4>${marcador.nombre}</h4>
                    <p>${marcador.descripcion}</p>
                </div>
            `);
            
            // Almacenar la etiqueta como propiedad del marcador
            marker.etiqueta = marcador.etiqueta;
            return marker;
        });
    }

    /*** Filtrar los marcadores por etiqueta ***/
    function filterMarkers(tag) {
        allMarkers.forEach(marker => {
            if (tag === "all" || marker.etiqueta === tag) {
                if (!map.hasLayer(marker)) {
                    marker.addTo(map);
                }
            } else {
                if (map.hasLayer(marker)) {
                    map.removeLayer(marker);
                }
            }
        });
        
        updateActiveButton(tag);
        saveActiveFilter(tag);
    }

    /*** Actualizar el botón activo ***/
    function updateActiveButton(activeTag) {
        document.querySelectorAll('.btn-tag').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.tag === activeTag) {
                btn.classList.add('active');
            }
        });
    }

    /*** Guardar filtro activo en sessionStorage ***/
    function saveActiveFilter(tag) {
        try {
            sessionStorage.setItem('activeFilter', tag);
        } catch (e) {
            console.error('Error al guardar el filtro:', e);
        }
    }

    /*** Obtener filtro activo de sessionStorage ***/
    function getActiveFilter() {
        try {
            return sessionStorage.getItem('activeFilter') || 'all';
        } catch (e) {
            console.error('Error al obtener el filtro:', e);
            return 'all';
        }
    }

    /*** Configurar paginación de etiquetas ***/
    function setupTagPagination() {
        const tagsContainer = document.querySelector('.tags-container');
        const tagsBar = document.querySelector('.tags-bar');
        const prevBtn = document.querySelector('.btn-pagination.prev');
        const nextBtn = document.querySelector('.btn-pagination.next');
        const pageIndicator = document.querySelector('.page-indicator');
        
        if (!tagsContainer || !tagsBar || !prevBtn || !nextBtn || !pageIndicator) return;
        
        const allTagButtons = Array.from(document.querySelectorAll('.btn-tag'));
        const tagsPerPage = 2; // Mostrar 2 etiquetas por página (además de "Todos")
        let currentPage = 1;
        
        // Separar el botón "Todos" de las demás etiquetas
        const allButton = allTagButtons.find(btn => btn.dataset.tag === "all");
        const filterButtons = allTagButtons.filter(btn => btn.dataset.tag !== "all");
        
        const totalPages = Math.max(1, Math.ceil(filterButtons.length / tagsPerPage));

        // Función para actualizar la visualización de etiquetas
        function updateTagsDisplay() {
            // 1. Mostrar siempre el botón "Todos"
            allButton.style.display = 'flex';
            
            // 2. Ocultar todas las etiquetas de filtro primero
            filterButtons.forEach(btn => {
                btn.style.display = 'none';
            });

            // 3. Calcular qué etiquetas mostrar para la página actual
            const startIdx = (currentPage - 1) * tagsPerPage;
            const endIdx = startIdx + tagsPerPage;
            const tagsToShow = filterButtons.slice(startIdx, endIdx);

            // 4. Mostrar las etiquetas correspondientes a la página actual
            tagsToShow.forEach(btn => {
                btn.style.display = 'flex';
            });

            // 5. Actualizar estado de los botones de paginación
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage >= totalPages;

            // 6. Actualizar indicador de página
            pageIndicator.textContent = `${currentPage}/${totalPages}`;
            
            // 7. Asegurar que el scroll se mantenga visible al cambiar páginas
            tagsBar.scrollTo(0, 0);
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
    }

    /*** Configurar eventos de los botones de etiquetas ***/
    function setupTagButtons() {
        document.querySelectorAll('.btn-tag').forEach(button => {
            button.addEventListener('click', function() {
                filterMarkers(this.dataset.tag);
            });
        });
    }

    /*** Manejar la adición de un nuevo punto ***/
    if (buttonAddPoint) {
        buttonAddPoint.addEventListener("click", () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById("modal-add-point"));
            modal.hide();

            const pointControls = document.getElementById("point-controls");
            const confirmButton = document.getElementById("confirm-add-point");
            const cancelButton = document.getElementById("cancel-add-point");

            pointControls.style.display = "block";
            confirmButton.disabled = true;

            const mapClickHandler = e => {
                if (currentMarker) removeMarker(currentMarker);
                currentMarker = createMarker(e.latlng);
                confirmButton.textContent = "Confirmar";
                confirmButton.disabled = false;
            };

            map.on("click", mapClickHandler);

            cancelButton.addEventListener("click", () => {
                if (currentMarker) removeMarker(currentMarker);
                pointControls.style.display = "none";
                buttonAddPoint.style.backgroundColor = "#ff0000";
                buttonAddPoint.style.color = "#fff";
                map.off("click", mapClickHandler);
                modal.show();
            }, { once: true });

            confirmButton.addEventListener("click", () => {
                if (!currentMarker) return;
                const savedMarker = currentMarker.getLatLng();
                removeMarker(currentMarker);
                pointControls.style.display = "none";
                map.off("click", mapClickHandler);
                modal.show();
                buttonAddPoint.style.backgroundColor = "#008000";
                buttonAddPoint.style.color = "#fff";

                form.addEventListener("submit", event => submitForm(event, savedMarker), { once: true });
            }, { once: true });
        });
    }

    /*** Enviar formulario con marcador ***/
    function submitForm(event, savedMarker) {
        event.preventDefault();
        const formData = new FormData(form);
        formData.append("latitud", savedMarker.lat);
        formData.append("longitud", savedMarker.lng);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        fetch(form.action, {
            method: "POST",
            body: formData,
            headers: { "X-CSRF-TOKEN": csrfToken }
        })
        .then(response => response.json())
        .then(() => {
            loadMarkers();
            bootstrap.Modal.getInstance(document.getElementById("modal-add-point")).hide();
        })
        .catch(error => console.error("Error al enviar los datos:", error));
    }

    /*** Configurar controles del mapa ***/
    function setupMapControls() {
        const centerUserBtn = document.getElementById("centerUser");
        const zoomInBtn = document.getElementById("zoomIn");
        const zoomOutBtn = document.getElementById("zoomOut");
        const toggleSatelliteBtn = document.getElementById("toggleSatellite");

        if (centerUserBtn) centerUserBtn.addEventListener("click", () => {
            if (currentLocationMarker) {
                map.setView(currentLocationMarker.getLatLng(), 16);
            } else {
                getLocation();
            }
        });

        if (zoomInBtn) zoomInBtn.addEventListener("click", () => map?.setZoom(map.getZoom() + 1));
        if (zoomOutBtn) zoomOutBtn.addEventListener("click", () => map?.setZoom(map.getZoom() - 1));
        
        if (toggleSatelliteBtn) {
            toggleSatelliteBtn.addEventListener("click", () => {
                if (!map) return;
                
                map.removeLayer(baseLayers[currentLayer]);
                currentLayer = currentLayer === "normal" ? "satellite" : "normal";
                baseLayers[currentLayer].addTo(map);
                
                // Actualizar icono del botón
                const icon = toggleSatelliteBtn.querySelector("i");
                if (icon) {
                    if (currentLayer === "satellite") {
                        icon.classList.replace("fa-map", "fa-globe");
                    } else {
                        icon.classList.replace("fa-globe", "fa-map");
                    }
                }
            });
        }
    }

    /*** Inicialización completa ***/
    function init() {
        // Configurar controles
        setupTagButtons();
        setupMapControls();
        setupTagPagination();
        
        // Obtener ubicación inicial
        getLocation();
        
        // Aplicar filtro guardado o 'all' por defecto
        filterMarkers(getActiveFilter());
        
        // Actualizar ubicación periódicamente
        setInterval(getLocation, 2000);
    }

    // Iniciar la aplicación
    init();
});