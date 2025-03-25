document.addEventListener('DOMContentLoaded', function () {
    // Manejar el envío del formulario para crear una partida
    document.getElementById('crear_partida').addEventListener('click', function (e) {
        e.preventDefault();
        let juego_id = document.getElementById('juego_id').value;
        let token = document.querySelector('input[name="_token"]').value;

        if (!juego_id) {
            Swal.fire('Atención', 'Selecciona un juego antes de continuar.', 'warning');
            return;
        }

        fetch('/mapa/partida', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ juego_id })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ya existe una partida');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            Swal.fire('Éxito', data.message, 'success');
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', error.message || 'Error al crear la partida', 'error');
        });
    });
});
