// Captura de eventos + definición de funciones 
document.getElementById('nombre').onblur = validaNombre;
document.getElementById('apellidos').onblur = validaApellidos;
document.getElementById('username').onblur = validaUsername;
document.getElementById('email').onblur = validaEmail;
document.getElementById('password').onblur = validaPassword;
document.getElementById('password_confirmation').onblur = validaPasswordConfirmacion;
document.getElementById('registerForm').onsubmit = validarFormulario;

// Captura de campos de error por campo
let errorNombre = document.getElementById('nombreError');
let errorApellidos = document.getElementById('apellidosError');
let errorUsername = document.getElementById('usernameError');
let errorEmail = document.getElementById('emailError');
let errorPassword = document.getElementById('passwordError');
let errorPasswordConfirmacion = document.getElementById('passwordConfirmationError');

// Captura de inputs para borde en rojo
let inputNombre = document.getElementById('nombre');
let inputApellidos = document.getElementById('apellidos');
let inputUsername = document.getElementById('username');
let inputEmail = document.getElementById('email');
let inputPassword = document.getElementById('password');
let inputPasswordConfirmacion = document.getElementById('password_confirmation');

// Función para validar nombre
function validaNombre() {
    let nombre = inputNombre.value.trim();
    let regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    if (nombre === "") {
        errorNombre.textContent = "El campo nombre es obligatorio";
        errorNombre.classList.remove('d-none');
        inputNombre.classList.add('is-invalid');
        return false;
    } else if (!regex.test(nombre)) {
        errorNombre.textContent = "El nombre solo puede contener letras";
        errorNombre.classList.remove('d-none');
        inputNombre.classList.add('is-invalid');
        return false;
    } else {
        errorNombre.textContent = "";
        errorNombre.classList.add('d-none');
        inputNombre.classList.remove('is-invalid');
        return true;
    }
}

// Función para validar apellidos
function validaApellidos() {
    let apellidos = inputApellidos.value.trim();
    let regex = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/;

    if (apellidos === "") {
        errorApellidos.textContent = "El campo apellidos es obligatorio";
        errorApellidos.classList.remove('d-none');
        inputApellidos.classList.add('is-invalid');
        return false;
    } else if (!regex.test(apellidos)) {
        errorApellidos.textContent = "El apellido solo puede contener letras";
        errorApellidos.classList.remove('d-none');
        inputApellidos.classList.add('is-invalid');
        return false;
    } else {
        errorApellidos.textContent = "";
        errorApellidos.classList.add('d-none');
        inputApellidos.classList.remove('is-invalid');
        return true;
    }
}

// Función para validar nombre de usuario
function validaUsername() {
    let username = inputUsername.value.trim();
    let regex = /^[a-zA-Z0-9_]{4,15}$/; // Solo letras, números y guion bajo, de 4 a 15 caracteres

    if (username === "") {
        errorUsername.textContent = "El campo nombre de usuario es obligatorio";
        errorUsername.classList.remove('d-none');
        inputUsername.classList.add('is-invalid');
        return false;
    } else if (!regex.test(username)) {
        errorUsername.textContent = "El nombre de usuario debe tener entre 4 y 15 caracteres y solo puede contener letras, números y guion bajo";
        errorUsername.classList.remove('d-none');
        inputUsername.classList.add('is-invalid');
        return false;
    } else {
        errorUsername.textContent = "";
        errorUsername.classList.add('d-none');
        inputUsername.classList.remove('is-invalid');
        return true;
    }
}

// Función para validar email
function validaEmail() {
    let email = inputEmail.value.trim();
    let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (email === "") {
        errorEmail.textContent = "El campo email es obligatorio";
        errorEmail.classList.remove('d-none');
        inputEmail.classList.add('is-invalid');
        return false;
    } else if (!regex.test(email)) {
        errorEmail.textContent = "El email no es válido";
        errorEmail.classList.remove('d-none');
        inputEmail.classList.add('is-invalid');
        return false;
    } else {
        errorEmail.textContent = "";
        errorEmail.classList.add('d-none');
        inputEmail.classList.remove('is-invalid');
        return true;
    }
}

// Función para validar contraseña
function validaPassword() {
    let password = inputPassword.value.trim();

    if (password === "") {
        errorPassword.textContent = "El campo contraseña es obligatorio";
        errorPassword.classList.remove('d-none');
        inputPassword.classList.add('is-invalid');
        return false;
    } else if (password.length < 8) {
        errorPassword.textContent = "La contraseña debe tener al menos 8 caracteres";
        errorPassword.classList.remove('d-none');
        inputPassword.classList.add('is-invalid');
        return false;
    } else {
        errorPassword.textContent = "";
        errorPassword.classList.add('d-none');
        inputPassword.classList.remove('is-invalid');
        return true;
    }
}

// Función para validar confirmación de contraseña
function validaPasswordConfirmacion() {
    let passwordConfirmation = inputPasswordConfirmacion.value.trim();
    let password = inputPassword.value.trim();

    if (passwordConfirmation === "") {
        errorPasswordConfirmacion.textContent = "El campo confirmación de contraseña es obligatorio";
        errorPasswordConfirmacion.classList.remove('d-none');
        inputPasswordConfirmacion.classList.add('is-invalid');
        return false;
    } else if (passwordConfirmation !== password) {
        errorPasswordConfirmacion.textContent = "Las contraseñas no coinciden";
        errorPasswordConfirmacion.classList.remove('d-none');
        inputPasswordConfirmacion.classList.add('is-invalid');
        return false;
    } else {
        errorPasswordConfirmacion.textContent = "";
        errorPasswordConfirmacion.classList.add('d-none');
        inputPasswordConfirmacion.classList.remove('is-invalid');
        return true;
    }
}


// Obtener el token CSRF de la página
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function validarFormulario(event) {
    event.preventDefault();  // Prevenir el comportamiento predeterminado del formulario

    let validacionNombre = validaNombre();
    let validacionApellidos = validaApellidos();
    let validacionUsername = validaUsername();
    let validacionEmail = validaEmail();
    let validacionPassword = validaPassword();
    let validacionPasswordConfirmacion = validaPasswordConfirmacion();

    if (validacionNombre && validacionApellidos && validacionUsername && validacionEmail && validacionPassword && validacionPasswordConfirmacion) {
        const formData = new FormData(document.getElementById('registerForm'));

        fetch('/register', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken, // Token CSRF
            },
            body: formData  // Enviar como FormData
        })
        .then(response => {
            const contentType = response.headers.get("content-type");
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`); // Captura errores HTTP 
            }
            if (contentType && contentType.includes("application/json")) {
                return response.json(); // Convertir solo si es JSON
            } else {
                throw new Error("La respuesta no es JSON. Verifica la ruta o el backend.");
            }
        })
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: "¡Registrado!",
                    text: "Ahora puedes iniciar sesión.",
                    icon: "success",
                    confirmButtonText: "Ok"
                }).then(() => {
                    window.location.href = './'; // Redirigir al login
                });
            } else {
                Swal.fire({
                    title: "Error en el registro",
                    text: data.message || "Hubo un problema, por favor intenta de nuevo.",
                    icon: "error",
                    confirmButtonText: "Cerrar"
                });
            }
        })
        .catch(error => {
            console.error('Error en la solicitud:', error);
            Swal.fire({
                title: "Error de conexión",
                text: "Hubo un problema con el servidor. Intenta más tarde.",
                icon: "error",
                confirmButtonText: "Entendido"
            });
        });
    }
}

// Asignar la función de validación y envío a la acción del formulario
document.getElementById('registerForm').onsubmit = validarFormulario;














