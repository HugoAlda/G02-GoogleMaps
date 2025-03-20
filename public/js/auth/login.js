document.addEventListener('DOMContentLoaded', function () {
    // Obtener los elementos del formulario
    const form = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const errorMessage = document.getElementById('errorMessage');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Manejador del evento submit del formulario
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // Crear un objeto FormData para enviar los datos del formulario
        let formData = new FormData(form);

        // Deshabilitar el botón de inicio de sesión
        loginButton.disabled = true;

        // Limpiar clases de error previas
        emailInput.classList.remove('is-invalid');
        passwordInput.classList.remove('is-invalid');
        emailError.classList.add('d-none');
        passwordError.classList.add('d-none');
        errorMessage.classList.add('d-none');

        // Realizar la solicitud AJAX al servidor
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            // Si el login es exitoso
            if (status === 200) {
                // Redirigir a la página de inicio si es exitoso
                window.location.href = '/mapa'; // TODO: Cambiar a la ruta real
            }

            // Si el status es 401, mostrar el mensaje de error
            if (status === 401) {
                errorMessage.classList.remove('d-none');
                errorMessage.querySelector('span').textContent = body.errors.invalid;
            }

            // Si el status es 422, manejar errores de validación
            if (status === 422) {
                if (body.errors.email) {
                    emailError.textContent = body.errors.email.join(' ');
                    emailError.classList.remove('d-none');
                    emailInput.classList.add('is-invalid');
                }
                if (body.errors.password) {
                    passwordError.textContent = body.errors.password.join(' ');
                    passwordError.classList.remove('d-none');
                    passwordInput.classList.add('is-invalid');
                }
            }
        })
        .catch(errors => {
            // Mostrar el mensaje de error general
            errorMessage.classList.remove('d-none');
            errorMessage.querySelector('span').textContent = "Error al intentar iniciar sesión, por favor intente nuevamente.";
        })
        .finally(() => {
            // Rehabilitar el botón de inicio de sesión
            loginButton.disabled = false;
        });
    });
});