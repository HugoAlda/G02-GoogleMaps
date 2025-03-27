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

    /*** Función para eliminar imagen seleccionada ***/
    removeImageButton.addEventListener("click", () => {
        imageInput.value = "";
        imagePreview.src = "";
        imagePreviewContainer.classList.add("d-none");
    });

    /*** Función para inicializar el mapa ***/
    function initializeMap(coords) {
        map = L.map("map", { zoomControl: false }).setView(coords, 16);
        baseLayers[currentLayer].addTo(map);
        loadMarkers();
    }

    /*** Función para actualizar la ubicación del usuario ***/
    function updateLocation(coords) {
        if (!map) initializeMap(coords);
        if (currentLocationMarker) map.removeLayer(currentLocationMarker);
        currentLocationMarker = L.marker(coords, { icon: userLocationIcon }).addTo(map);
    }

    /*** Obtener ubicación del usuario ***/
    function getLocation() {
        if (!navigator.geolocation) return alert("Tu navegador no soporta geolocalización");
        navigator.geolocation.getCurrentPosition(
            ({ coords }) => updateLocation([coords.latitude, coords.longitude]),
            () => alert("No se pudo obtener tu ubicación. Verifica los permisos.")
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
        if (marker) map.removeLayer(marker);
    }

    /*** Cargar los marcadores guardados ***/
    function loadMarkers() {
        if (!window.marcadores) return console.error("No se encontraron marcadores");
        allMarkers = window.marcadores.map(({ latitud, longitud, nombre, descripcion }) =>
            L.marker([latitud, longitud]).addTo(map).bindPopup(`<strong>${nombre}</strong><br>${descripcion}`)
        );
    }

    /*** Filtrar los marcadores por etiqueta ***/
    function filterMarkers(tag) {
        allMarkers.forEach((marker, index) => {
            const marcador = window.marcadores[index];
            marcador.etiqueta === tag || tag === "all" ? marker.addTo(map) : map.removeLayer(marker);
        });
    }

    /*** Manejar la adición de un nuevo punto ***/
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

            // Cambiamos el color del botón a rojo
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
    
    
    // Función para validar el formulario
    function isValidForm(formData) {
        // Validar que los campos requeridos estén llenos
        const requiredFields = ["nombre", "descripcion", "latitud", "longitud", "direccion", "icono"];
        return requiredFields.every(field => formData.get(field));
    }

    // Función para mostrar mensaje de error
    function showError(formData) {
        // Obtenemos los campos requeridos
        const fields = ["nombre", "descripcion", "latitud", "longitud", "direccion", "icono"];

        // Recorremos los campos requeridos y mostramos el mensaje de error
        fields.forEach(field => {
            const errorMessage = document.getElementById(`error-${field}`);
            errorMessage.textContent = formData.get(field);
            errorMessage.classList.remove("d-none");
        });
    }

    /*** Enviar formulario con marcador ***/
    function submitForm(event, savedMarker) {
        event.preventDefault();
        const formData = new FormData(form);
        formData.append("latitud", savedMarker.lat);
        formData.append("longitud", savedMarker.lng);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        if (isValidForm(formData)) {
            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-CSRF-TOKEN": csrfToken }
            })
        .then(response => response.json())
        .then(() => {
            loadMarkers();
            bootstrap.Modal.getInstance(document.getElementById("modal-add-point")).hide();
        }).catch(error => 
            console.error("Error al enviar los datos:", error));
        } else {
            showError(formData);
        }
    }

    /*** Controles del mapa ***/
    document.getElementById("centerUser").addEventListener("click", () => map?.setView(currentLocationMarker.getLatLng(), 16));
    document.getElementById("zoomIn").addEventListener("click", () => map?.setZoom(map.getZoom() + 1));
    document.getElementById("zoomOut").addEventListener("click", () => map?.setZoom(map.getZoom() - 1));
    document.getElementById("toggleSatellite").addEventListener("click", () => {
        if (!map) return;
        map.removeLayer(baseLayers[currentLayer]);
        currentLayer = currentLayer === "normal" ? "satellite" : "normal";
        baseLayers[currentLayer].addTo(map);
    });

    /*** Filtrar marcadores con botones ***/
    document.querySelectorAll(".filter-tag").forEach(button => {
        button.addEventListener("click", function () {
            filterMarkers(this.dataset.tag);
            document.querySelectorAll(".btn-tag").forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Obtener ubicación inicial y actualizar cada 2 segundos
    getLocation();
    setInterval(getLocation, 2000);
});