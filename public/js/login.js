

document.addEventListener('DOMContentLoaded', function () {
    // Ocultar la contraseña
    // Recoger el icono y el input de la contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    // Añadir un evento al icono para que al hacer click, se muestre o se oculte la contraseña
    togglePassword.addEventListener('click', function (e) {
        // Cambiar el tipo de input
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el icono
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });
});