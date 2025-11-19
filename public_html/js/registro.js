// Actualicé este archivo para que llame a tu API de registro real
document.addEventListener('DOMContentLoaded', () => {

    // ===== ELEMENTOS DEL DOM =====
    const formulario = document.getElementById('formulario-registro');
    const mensajeFormulario = document.getElementById('mensaje-formulario');
    
    // ... (todos los demás campos: nombre, apellido, etc.)
    const nombre = document.getElementById('nombre');
    const apellido = document.getElementById('apellido');
    const documento = document.getElementById('documento');
    const correo = document.getElementById('correo');
    const fechaNacimiento = document.getElementById('fecha-nacimiento');
    const contrasena = document.getElementById('contrasena');
    const confirmarContrasena = document.getElementById('confirmar-contrasena');
    const terminos = document.getElementById('terminos');
    
    const toggleContrasena = document.getElementById('toggle-contrasena');
    const toggleConfirmarContrasena = document.getElementById('toggle-confirmar-contrasena');

    // ===== MANEJO DE VISIBILIDAD DE CONTRASEÑA (igual que antes) =====
    function togglePasswordVisibility(input, button) {
        const tipo = input.type;
        const iconoOjo = button.querySelector('.icono-ojo');
        if (tipo === 'password') {
            input.type = 'text';
            iconoOjo.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>`;
        } else {
            input.type = 'password';
            iconoOjo.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>`;
        }
    }
    if (toggleContrasena) {
        toggleContrasena.addEventListener('click', () => togglePasswordVisibility(contrasena, toggleContrasena));
    }
    if (toggleConfirmarContrasena) {
        toggleConfirmarContrasena.addEventListener('click', () => togglePasswordVisibility(confirmarContrasena, toggleConfirmarContrasena));
    }

    // ===== MANEJO DEL FORMULARIO =====
    if (formulario) {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault();
            limpiarTodosLosErrores();
            if (validarFormulario()) {
                // ¡Llama a la API real!
                procesarRegistroConAPI();
            } else {
                mostrarMensajeFormulario('Por favor, corrige los errores en el formulario.', 'error');
            }
        });
    }

    /**
     * Valida todos los campos del formulario
     * @returns {boolean} - True si todo es válido
     */
    function validarFormulario() {
        let esValido = true;
        
        // 1. Validar Nombre
        if (nombre.value.trim() === '') {
            mostrarError(nombre, 'El nombre es obligatorio.');
            esValido = false;
        }
        // ... (resto de validaciones)
        if (documento.value.trim().length !== 8 || !/^\d+$/.test(documento.value)) {
            mostrarError(documento, 'El documento debe tener 8 dígitos numéricos.');
            esValido = false;
        }
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo.value)) {
            mostrarError(correo, 'Ingresa un correo electrónico válido.');
            esValido = false;
        }
        if (fechaNacimiento.value === '') {
            mostrarError(fechaNacimiento, 'La fecha de nacimiento es obligatoria.');
            esValido = false;
        }
        if (!document.querySelector('input[name="genero"]:checked')) {
            mostrarError(document.querySelector('.grupo-radio'), 'Debes seleccionar un género.');
            esValido = false;
        }
        if (contrasena.value.length < 6) {
            mostrarError(contrasena, 'La contraseña debe tener al menos 6 caracteres.');
            esValido = false;
        }
        if (confirmarContrasena.value !== contrasena.value) {
            mostrarError(confirmarContrasena, 'Las contraseñas no coinciden.');
            esValido = false;
        }
        if (!terminos.checked) {
            mostrarError(terminos.parentElement, 'Debes aceptar los términos y condiciones.');
            esValido = false;
        }
        return esValido;
    }

    /**
     * Procesa el registro llamando a tu API
     */
    async function procesarRegistroConAPI() {
        const botonSubmit = formulario.querySelector('button[type="submit"]');
        botonSubmit.textContent = 'Registrando...';
        botonSubmit.disabled = true;

        const datosUsuario = {
            // Tabla usuario
            correo: correo.value,
            // Tabla socio
            nombre: nombre.value,
            apellido: apellido.value,
            documento: documento.value,
            fecha_nacimiento: fechaNacimiento.value,
            genero: document.querySelector('input[name="genero"]:checked').value,
            contrasena: contrasena.value, // Enviamos la contraseña
            id_tipo_socio: 1 // Asumimos '1' como tipo socio por defecto
        };

        console.log('Datos listos para enviar al backend (MySQL):', datosUsuario);

        const apiURL = '../api/socio.php';

        try {
            const response = await fetch(apiURL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosUsuario)
            });

            const data = await response.json();

            if (response.ok && data.ok) {
                mostrarMensajeFormulario('¡Registro exitoso! Redirigiendo al inicio de sesión...', 'exito');
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                throw new Error(data.error || "Error desconocido al registrar");
            }

        } catch (error) {
            console.error('Error en el registro:', error);
            mostrarMensajeFormulario(error.message, 'error');
            botonSubmit.textContent = 'Registrarme';
            botonSubmit.disabled = false;
        }
    }

    // ===== FUNCIONES AUXILIARES DE ERRORES (igual que antes) =====
    function mostrarError(inputElement, mensaje) {
        inputElement.classList.add('error');
        let mensajeError = inputElement.parentElement.querySelector('.mensaje-error-campo');
        if (!mensajeError) {
            mensajeError = document.createElement('p');
            mensajeError.className = 'mensaje-error-campo';
            mensajeError.style.color = '#e50914';
            mensajeError.style.fontSize = '0.875rem';
            mensajeError.style.marginTop = '0.25rem';
            if(inputElement.type === 'date' || inputElement.classList.contains('grupo-radio') || inputElement.parentElement.classList.contains('filtro-opcion')) {
                 inputElement.parentElement.appendChild(mensajeError);
            } else {
                 inputElement.parentElement.appendChild(mensajeError);
            }
        }
        mensajeError.textContent = mensaje;
    }

    function limpiarTodosLosErrores() {
        const todosLosInputs = formulario.querySelectorAll('.input-campo, .grupo-radio, .filtro-opcion input');
        todosLosInputs.forEach(input => {
            input.classList.remove('error');
            const mensajeError = input.parentElement.querySelector('.mensaje-error-campo');
            if (mensajeError) mensajeError.remove();
        });
        const radioError = document.querySelector('.grupo-radio + .mensaje-error-campo');
        if(radioError) radioError.remove();
        const terminosError = document.querySelector('.filtro-opcion .mensaje-error-campo');
        if(terminosError) terminosError.remove();
        mensajeFormulario.textContent = '';
        mensajeFormulario.className = '';
    }
    
    function mostrarMensajeFormulario(mensaje, tipo) {
        mensajeFormulario.textContent = mensaje;
        mensajeFormulario.className = tipo;
    }
});