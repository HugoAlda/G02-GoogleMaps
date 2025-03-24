// Recoger el boton de crear nuevo punto

const buttonCreatePoint = document.getElementById('button-add-point');
const buttonAddEtiqueta = document.getElementById('btn-create-etiqueta');

// Crear nuevo punto
buttonCreatePoint.addEventListener('click', () => {

});

// Crear nueva etiqueta
buttonAddEtiqueta.addEventListener('click', function() {
    const buttonAddEtiqueta = document.getElementById('btn-create-etiqueta');
    const etiquetaSelect = document.getElementById('etiqueta-select');
    const newEtiquetaNameContainer = document.getElementById('new-etiqueta-name-container');

    if (buttonAddEtiqueta) {
        buttonAddEtiqueta.addEventListener('click', function () {
            // Ocultar el select de etiquetas existentes
            etiquetaSelect.classList.add('d-none');

            // Mostrar el input para crear una nueva etiqueta
            newEtiquetaNameContainer.classList.remove('d-none');
        });
    }
});