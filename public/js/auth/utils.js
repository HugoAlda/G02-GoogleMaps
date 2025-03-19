// Ocultar la contraseña
// Recoger todos los iconos y inputs de contraseña
const togglePasswordElements = document.querySelectorAll('[id^="togglePassword"]');

// Añadir evento a cada icono de contraseña
togglePasswordElements.forEach(togglePassword => {
    // Obtener el ID del campo de contraseña correspondiente
    const passwordId = togglePassword.id === 'togglePasswordConfirm' ? 'password_confirmation' : 'password';
    const password = document.querySelector(`#${passwordId}`);

    // Añadir evento click para mostrar/ocultar contraseña
    togglePassword.addEventListener('click', function (e) {
        // Cambiar el tipo de input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el icono
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
});