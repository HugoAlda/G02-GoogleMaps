document.addEventListener("DOMContentLoaded", () => {
    // Elementos del DOM
    const imageInput = document.getElementById("imagen");
    const imagePreview = document.getElementById("image-preview");
    const imagePreviewContainer = document.getElementById("image-preview-container");
    const removeImageButton = document.getElementById("remove-image");
    const buttonAddPoint = document.getElementById("button-add-point");
    const form = document.getElementById("form-add-point");
    
    // Elementos para el modal de información
    const markerModal = new bootstrap.Modal(document.getElementById('markerModal'));
    const markerModalTitle = document.getElementById('markerModalTitle');
    const markerModalBody = document.getElementById('markerModalBody');
    const getDirectionsBtn = document.getElementById('getDirectionsBtn');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // Elementos de paginación
    const tagsContainer = document.querySelector('.tags-container');
    const tagsBar = document.querySelector('.tags-bar');
    const prevBtn = document.querySelector('.btn-pagination.prev');
    const nextBtn = document.querySelector('.btn-pagination.next');
    const pageIndicator = document.querySelector('.page-indicator');

    let map, currentMarker = null, currentLocationMarker, allMarkers = [], currentLayer = "normal";
    let selectedMarker = null;
    let routingControl = null;
    let currentPage = 1;
    const tagsPerPage = 2;
    let allTagButtons = [];
    
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
            preferCanvas: true
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
            zIndexOffset: 1000
        }).addTo(map);
        return coords;
    }

    /*** Obtener ubicación del usuario ***/
    function getLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                alert("Tu navegador no soporta geolocalización");
                reject("Geolocation not supported");
                return;
            }
            
            navigator.geolocation.getCurrentPosition(
                ({ coords }) => resolve(updateLocation([coords.latitude, coords.longitude])),
                () => {
                    alert("No se pudo obtener tu ubicación. Verifica los permisos.");
                    reject("Could not get location");
                },
                {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        });
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

    /*** Función para mostrar información del marcador en el modal ***/
    function showMarkerInfo(markerData) {
        // Convertir coordenadas a números si son strings
        const lat = typeof markerData.latitud === 'string' ? parseFloat(markerData.latitud) : markerData.latitud;
        const lng = typeof markerData.longitud === 'string' ? parseFloat(markerData.longitud) : markerData.longitud;
        
        selectedMarker = markerData;
        markerModalTitle.textContent = markerData.nombre || 'Sin nombre';
        
        // Procesar la descripción manteniendo saltos de línea y espacios
        const descripcion = markerData.descripcion 
            ? markerData.descripcion.replace(/\n/g, '<br>').replace(/\s\s/g, ' &nbsp;')
            : 'Sin descripción';
        
        markerModalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <div class="marker-info-header mb-3">
                        <span class="badge bg-${getTagColorClass(markerData.etiqueta)}">
                            ${markerData.etiqueta || 'Sin etiqueta'}
                        </span>
                    </div>
                    <div class="marker-info-body">
                        <p class="mt-2"><strong>Descripción:</strong> ${markerData.descripcion || 'Sin descripción'}</p>
                        ${markerData.imagen ? `<img src="${markerData.imagen}" alt="${markerData.nombre || 'Marcador'}" class="img-fluid mb-3 mt-2">` : ''}
                        <p class="mt-2"><strong>Dirección:</strong> ${markerData.direccion || 'Sin dirección especificada'}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="miniMap" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #ddd;"></div>
                </div>
            </div>
        `;
        
        // Mostrar el modal
        markerModal.show();
        
        // Centrar el mapa principal en el marcador
        map.setView([lat, lng], 16);
        
        // Una vez que el modal se muestre completamente, inicializamos el mini mapa
        document.getElementById('markerModal').addEventListener('shown.bs.modal', () => {
            // Crear el mini mapa
            const miniMap = L.map('miniMap').setView([lat, lng], 15);
            L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(miniMap);
            
            // Definir iconos personalizados
            const redIcon = L.icon({
                iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });
            const blueIcon = L.icon({
                iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png",
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34]
            });
            
            // Obtener la ubicación actual del usuario para el mini mapa
            getLocation().then(userLoc => {
                const userPoint = L.latLng(userLoc[0], userLoc[1]);
                const markerPoint = L.latLng(lat, lng);
                
                // Agregar marcadores en el mini mapa
                L.marker(userPoint, { icon: blueIcon }).addTo(miniMap);
                L.marker(markerPoint, { icon: redIcon }).addTo(miniMap);
                
                // Dibujar una línea simple entre ambos puntos
                const line = L.polyline([userPoint, markerPoint], {
                    color: '#ff0000',
                    weight: 4,
                    opacity: 0.7
                }).addTo(miniMap);
                
                miniMap.fitBounds([userPoint, markerPoint]);
            }).catch(error => {
                console.error("Error al obtener la ubicación para el mini mapa:", error);
            });
            
            // Forzar el redimensionamiento del mini mapa
            setTimeout(() => {
                miniMap.invalidateSize();
            }, 100);
        }, { once: true });
    }        

    /*** Función auxiliar para obtener clase CSS según la etiqueta ***/
    function getTagColorClass(tag) {
        const tagColors = {
            'restaurante': 'danger',
            'hotel': 'primary',
            'monumento': 'warning',
            'naturaleza': 'success',
            'otros': 'secondary'
        };
        return tagColors[tag.toLowerCase()] || 'info';
    }

    /*** Cargar los marcadores guardados ***/
    function loadMarkers() {
        if (!window.marcadores) {
            console.error("No se encontraron marcadores");
            return;
        }
        
        allMarkers.forEach(marker => map.removeLayer(marker));
        allMarkers = [];
        
        allMarkers = window.marcadores.map(marcador => {
            // Convertir coordenadas a números si son strings
            const lat = typeof marcador.latitud === 'string' ? parseFloat(marcador.latitud) : marcador.latitud;
            const lng = typeof marcador.longitud === 'string' ? parseFloat(marcador.longitud) : marcador.longitud;
            
            const marker = L.marker([lat, lng], {
                title: marcador.nombre,
                alt: marcador.descripcion,
                riseOnHover: true
            }).addTo(map);
            
            // Almacenar todos los datos del marcador
            marker.markerData = marcador;
            
            // Evento para hacer clic en el marcador
            marker.on('click', () => {
                showMarkerInfo(marcador);
            });
            
            marker.etiqueta = marcador.etiqueta;
            return marker;
        });
    }

    /*** Configurar paginación de etiquetas ***/
    function setupTagPagination() {
        if (!tagsContainer || !tagsBar || !prevBtn || !nextBtn || !pageIndicator) return;
    
        allTagButtons = Array.from(document.querySelectorAll('.btn-tag'));
        const allButton = allTagButtons.find(btn => btn.dataset.tag === "all");
        const filterButtons = allTagButtons.filter(btn => btn.dataset.tag !== "all");
    
        const totalPages = Math.max(1, Math.ceil(filterButtons.length / tagsPerPage));
    
        function updateTagsDisplay() {
            // Mostrar siempre el botón "Todos"
            if (allButton) allButton.style.display = 'flex';
    
            // Ocultar todas las etiquetas primero
            filterButtons.forEach(btn => {
                btn.style.display = 'none';
            });
    
            // Calcular las etiquetas a mostrar en la página actual
            const startIdx = (currentPage - 1) * tagsPerPage;
            const endIdx = startIdx + tagsPerPage;
            const tagsToShow = filterButtons.slice(startIdx, endIdx);
    
            // Mostrar las etiquetas correspondientes
            tagsToShow.forEach(btn => {
                btn.style.display = 'flex';
            });
    
            // Actualizar estado de los botones y el indicador de página
            prevBtn.disabled = currentPage === 1;
            nextBtn.disabled = currentPage >= totalPages;
            pageIndicator.textContent = `${currentPage}/${totalPages}`;
    
            // Reiniciar el scroll de la barra
            tagsBar.scrollTo(0, 0);
        }
    
        // Agregar listeners una sola vez usando una bandera
        if (!prevBtn.dataset.listenerAdded) {
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateTagsDisplay();
                }
            });
            prevBtn.dataset.listenerAdded = true;
        }
    
        if (!nextBtn.dataset.listenerAdded) {
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateTagsDisplay();
                }
            });
            nextBtn.dataset.listenerAdded = true;
        }
    
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
        
        // Aplicar filtro guardado
        const activeFilter = getActiveFilter();
        filterMarkers(activeFilter);
        updateActiveButton(activeFilter);
        
        // Configurar evento para cerrar el modal
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => {
                markerModal.hide();
                if (routingControl) {
                    map.removeControl(routingControl);
                    routingControl = null;
                }
                // Reiniciamos la paginación a la página 1
                currentPage = 1;
                setupTagPagination();
            });
        }        
    
        // Actualizar ubicación periódicamente
        setInterval(getLocation, 2000);
    }

    // Iniciar la aplicación
    init();
});