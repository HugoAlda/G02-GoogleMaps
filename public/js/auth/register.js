document.addEventListener('DOMContentLoaded', function () {
    // Obtener los elementos del formulario
    const form = document.getElementById('registerForm');
    const registerButton = document.getElementById('registerButton');
    const errorMessage = document.getElementById('errorMessage');
    const nameError = document.getElementById('nameError');
    const surnameError = document.getElementById('surnameError');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmationError = document.getElementById('passwordConfirmationError');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const nameInput = document.getElementById('name');
    const surnameInput = document.getElementById('surname');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');

    // Manejador del evento submit del formulario
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        // Crear un objeto FormData para enviar los datos del formulario
        let formData = new FormData(form);

        
        // Deshabilitar el botón de registro
        registerButton.disabled = true;

        // Limpiar clases de error previas
        nameError.classList.add('d-none');
        surnameError.classList.add('d-none');
        emailError.classList.add('d-none');
        passwordError.classList.add('d-none');
        passwordConfirmationError.classList.add('d-none');
        errorMessage.classList.add('d-none');
    });
});