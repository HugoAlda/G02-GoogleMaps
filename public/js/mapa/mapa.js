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
  
    // Elementos de paginación y etiquetas
    const tagsContainer = document.querySelector('.tags-container');
    const tagsBar = document.querySelector('.tags-bar');
    const prevBtn = document.querySelector('.btn-pagination.prev');
    const nextBtn = document.querySelector('.btn-pagination.next');
    const pageIndicator = document.querySelector('.page-indicator');
  
    // Elemento para el filtro por radio (select)
    const radiusSelect = document.getElementById("radiusSelect");
  
    // Variables globales
    let map,
        currentMarker = null,
        currentLocationMarker,
        allMarkers = [],
        currentLayer = "normal";
    let selectedMarker = null;
    let routingControl = null;
    let currentPage = 1;
    const tagsPerPage = 2;
    let allTagButtons = [];
    let radiusCircle = null; // Para el círculo del radio
  
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
      normal: L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution: "&copy; OpenStreetMap contributors"
      }),
      satellite: L.tileLayer("https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}", {
        attribution: "&copy; Esri"
      })
    };
  
    /*** Previsualizar imagen ***/
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
  
    /*** Eliminar imagen seleccionada ***/
    if (removeImageButton) {
      removeImageButton.addEventListener("click", () => {
        imageInput.value = "";
        imagePreview.src = "";
        imagePreviewContainer.classList.add("d-none");
      });
    }
  
    /*** Inicializar el mapa ***/
    function initializeMap(coords){
      map = L.map("map", {
        zoomControl: false,
        preferCanvas: true
      }).setView(coords, 16);
      baseLayers[currentLayer].addTo(map);
      // Se aplica el filtrado combinado al iniciar
      filterMarkersCombined();
    }
  
    /*** Actualizar la ubicación del usuario ***/
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
          { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
      });
    }
  
    /*** Crear un marcador ***/
    function createMarker(coords) {
      const redIcon = L.icon({
        iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png",
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34]
      });
      return L.marker(coords, { icon: redIcon }).addTo(map);
    }
  
    /*** Eliminar un marcador ***/
    function removeMarker(marker) {
      if (marker && map.hasLayer(marker)) {
        map.removeLayer(marker);
      }
    }
  
    /*** Mostrar información en el modal ***/
    function showMarkerInfo(markerData) {
      const lat = typeof markerData.latitud === 'string' ? parseFloat(markerData.latitud) : markerData.latitud;
      const lng = typeof markerData.longitud === 'string' ? parseFloat(markerData.longitud) : markerData.longitud;
  
      selectedMarker = markerData;
      markerModalTitle.textContent = markerData.nombre || 'Sin nombre';
  
      const descripcion = markerData.descripcion
        ? markerData.descripcion.replace(/\n/g, '<br>').replace(/\s\s/g, ' &nbsp;')
        : 'Sin descripción';
  
      // Se añade el botón "Añadir a Favoritos" al contenido del modal
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
              <button id="favButton" class="btn btn-success mb-4">Añadir a Favoritos</button>
            </div>
          </div>
          <div class="col-md-6">
            <div id="miniMap" style="height: 300px; width: 100%; border-radius: 8px; border: 1px solid #ddd;"></div>
          </div>
        </div>
      `;
  
      markerModal.show();
      map.setView([lat, lng], 16);
  
      // Asociar el evento del botón de Favoritos
      document.getElementById('favButton').addEventListener('click', () => {
        addToFavorites(markerData);
      });
  
      document.getElementById('markerModal').addEventListener('shown.bs.modal', () => {
        const miniMap = L.map('miniMap').setView([lat, lng], 15);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(miniMap);
  
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
  
        getLocation().then(userLoc => {
          const userPoint = L.latLng(userLoc[0], userLoc[1]);
          const markerPoint = L.latLng(lat, lng);
          L.marker(userPoint, { icon: blueIcon }).addTo(miniMap);
          L.marker(markerPoint, { icon: redIcon }).addTo(miniMap);
  
          const osrm = L.Routing.osrmv1({ serviceUrl: 'https://router.project-osrm.org/route/v1' });
          osrm.route([userPoint, markerPoint], (err, routes) => {
            if (err) {
              console.error("Error al calcular la ruta:", err);
              return;
            }
            if (routes && routes.length > 0) {
              const bestRoute = routes[0];
              const routeLine = L.Routing.line(bestRoute, {
                styles: [{ color: '#ff0000', weight: 4, opacity: 0.7 }]
              }).addTo(miniMap);
              miniMap.fitBounds(routeLine.getBounds());
            }
          });
        }).catch(error => {
          console.error("Error al obtener la ubicación para el mini mapa:", error);
        });
  
        setTimeout(() => {
          miniMap.invalidateSize();
        }, 100);
      }, { once: true });
    }
  
    /*** Agregar a Favoritos (insertar en la etiqueta "Favoritos") ***/
    function addToFavorites(markerData) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");
        fetch('/mapa/api/favorites', {
          method: 'POST',
          body: JSON.stringify({ marker_id: markerData.id }),
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrfToken
          }
        })
        .then(response => response.json())
        .then(data => {
          // Mostrar SweetAlert de éxito
          Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          });
          // Actualizar el arreglo global de marcadores para que el marcador se marque como favorito.
          window.marcadores = window.marcadores.map(m => {
            if (m.id === markerData.id) {
              m.etiqueta = "Favoritos";
            }
            return m;
          });
          // Volver a aplicar el filtro combinado para actualizar la vista si se está filtrando por "Favoritos"
          filterMarkersCombined();
        })
        .catch(error => {
          // Mostrar SweetAlert de error
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al añadir a favoritos'
          });
          console.error('Error al añadir a favoritos:', error);
        });
      }                
  
    /*** Obtener clase CSS según la etiqueta ***/
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
  
    /***** Filtrado combinado (etiqueta + radio) *****/
    function filterMarkersCombined() {
      let tag = getActiveFilter(); // "all" o la etiqueta seleccionada
      let radiusValue = radiusSelect ? radiusSelect.value : "all";
      // Eliminar marcadores actuales del mapa
      allMarkers.forEach(marker => map.removeLayer(marker));
      allMarkers = [];
      // Si se ha seleccionado un radio válido, filtrar marcadores dentro del radio y que cumplan la etiqueta
      if (radiusValue !== "all" && parseInt(radiusValue, 10) > 0) {
        getLocation().then(userLoc => {
          const userLatLng = L.latLng(userLoc[0], userLoc[1]);
          // Dibujar o actualizar el círculo de radio
          if (radiusCircle) { map.removeLayer(radiusCircle); }
          radiusCircle = L.circle(userLatLng, {
            radius: parseInt(radiusValue, 10),
            color: '#3388ff',
            fillColor: '#3388ff',
            fillOpacity: 0.2,
            weight: 2
          }).addTo(map);
          // Filtrar marcadores que cumplan la etiqueta y estén dentro del radio
          const filtered = window.marcadores.filter(marcador => {
            const tagMatch = (tag === "all") || (marcador.etiqueta === tag);
            const mLat = parseFloat(marcador.latitud);
            const mLng = parseFloat(marcador.longitud);
            const markerLatLng = L.latLng(mLat, mLng);
            const distance = userLatLng.distanceTo(markerLatLng);
            return tagMatch && (distance <= parseInt(radiusValue, 10));
          });
          filtered.forEach(marcador => {
            const mLat = parseFloat(marcador.latitud);
            const mLng = parseFloat(marcador.longitud);
            const marker = L.marker([mLat, mLng], {
              title: marcador.nombre,
              alt: marcador.descripcion,
              riseOnHover: true
            }).addTo(map);
            marker.markerData = marcador;
            marker.on('click', () => showMarkerInfo(marcador));
            marker.etiqueta = marcador.etiqueta;
            allMarkers.push(marker);
          });
        }).catch(error => {
          console.error("Error al obtener la ubicación para el filtro de radio:", error);
        });
      } else {
        // Si no hay filtro por radio ("all" o valor no válido), eliminar el círculo y filtrar solo por etiqueta
        if (radiusCircle) {
          map.removeLayer(radiusCircle);
          radiusCircle = null;
        }
        const filtered = window.marcadores.filter(marcador => {
          return (tag === "all") || (marcador.etiqueta === tag);
        });
        filtered.forEach(marcador => {
          const mLat = parseFloat(marcador.latitud);
          const mLng = parseFloat(marcador.longitud);
          const marker = L.marker([mLat, mLng], {
            title: marcador.nombre,
            alt: marcador.descripcion,
            riseOnHover: true
          }).addTo(map);
          marker.markerData = marcador;
          marker.on('click', () => showMarkerInfo(marcador));
          marker.etiqueta = marcador.etiqueta;
          allMarkers.push(marker);
        });
      }
    }
    
    /***** Guardar/obtener el filtro activo de etiquetas *****/
    function saveActiveFilter(tag) {
      try {
        sessionStorage.setItem('activeFilter', tag);
      } catch (e) {
        console.error('Error al guardar el filtro:', e);
      }
    }
    function getActiveFilter() {
      try {
        return sessionStorage.getItem('activeFilter') || 'all';
      } catch (e) {
        console.error('Error al obtener el filtro:', e);
        return 'all';
      }
    }
    
    /*** Configurar eventos de botones de etiquetas ***/
    function setupTagButtons() {
      document.querySelectorAll('.btn-tag').forEach(button => {
        button.addEventListener('click', function() {
          saveActiveFilter(this.dataset.tag);
          updateActiveButton(this.dataset.tag);
          filterMarkersCombined();
        });
      });
    }
    
    /*** Actualizar botón activo ***/
    function updateActiveButton(activeTag) {
      document.querySelectorAll('.btn-tag').forEach(btn => {
        btn.classList.remove('active');
        if (btn.dataset.tag === activeTag) {
          btn.classList.add('active');
        }
      });
    }
    
    /*** Escuchar cambios en el select de radio ***/
    if (radiusSelect) {
      radiusSelect.addEventListener("change", function() {
        filterMarkersCombined();
        // Si se selecciona "all", se elimina el círculo de radio
        if (this.value === "all") {
          if (radiusCircle) {
            map.removeLayer(radiusCircle);
            radiusCircle = null;
          }
        }
      });
    }
    
    /*** Configurar controles del mapa ***/
    function setupMapControls() {
      const centerUserBtn = document.getElementById("centerUser");
      const zoomInBtn = document.getElementById("zoomIn");
      const zoomOutBtn = document.getElementById("zoomOut");
      const toggleSatelliteBtn = document.getElementById("toggleSatellite");
      if (centerUserBtn)
        centerUserBtn.addEventListener("click", () => {
          if (currentLocationMarker) {
            map.setView(currentLocationMarker.getLatLng(), 16);
          } else {
            getLocation();
          }
        });
      if (zoomInBtn)
        zoomInBtn.addEventListener("click", () => map?.setZoom(map.getZoom() + 1));
      if (zoomOutBtn)
        zoomOutBtn.addEventListener("click", () => map?.setZoom(map.getZoom() - 1));
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
    
    /*** Inicialización completa ***/
    function init() {
      // Por defecto, se establece el filtro activo en "all"
      saveActiveFilter("all");
      updateActiveButton("all");
      setupTagButtons();
      setupMapControls();
      getLocation();
      // Al iniciar se aplica el filtrado combinado (filtro etiqueta "all" y radio según select)
      filterMarkersCombined();
      setInterval(getLocation, 2000);
    }
    
    init();
  });  