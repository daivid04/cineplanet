// ===== ESPERA A QUE EL DOM ESTÉ CARGADO =====
document.addEventListener('DOMContentLoaded', () => {

    // ===== ELEMENTOS DEL DOM =====
    const formulario = document.getElementById('formulario-registro');
    const mensajeFormulario = document.getElementById('mensaje-formulario');
    
    // Campos del formulario
    const nombre = document.getElementById('nombre');
    const apellido = document.getElementById('apellido');
    const documento = document.getElementById('documento');
    const correo = document.getElementById('correo');
    const fechaNacimiento = document.getElementById('fecha-nacimiento');
    const contrasena = document.getElementById('contrasena');
    const confirmarContrasena = document.getElementById('confirmar-contrasena');
    const terminos = document.getElementById('terminos');
    
    // Botones de contraseña
    const toggleContrasena = document.getElementById('toggle-contrasena');
    const toggleConfirmarContrasena = document.getElementById('toggle-confirmar-contrasena');

    // ===== MANEJO DE VISIBILIDAD DE CONTRASEÑA =====

    function togglePasswordVisibility(input, button) {
        const tipo = input.type;
        const iconoOjo = button.querySelector('.icono-ojo');

        if (tipo === 'password') {
            input.type = 'text';
            // Cambia el icono a "ojo cerrado"
            iconoOjo.innerHTML = `
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            `;
        } else {
            input.type = 'password';
            // Cambia el icono a "ojo abierto"
            iconoOjo.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            `;
        }
    }

    if (toggleContrasena) {
        toggleContrasena.addEventListener('click', () => {
            togglePasswordVisibility(contrasena, toggleContrasena);
        });
    }

    if (toggleConfirmarContrasena) {
        toggleConfirmarContrasena.addEventListener('click', () => {
            togglePasswordVisibility(confirmarContrasena, toggleConfirmarContrasena);
        });
    }

    // ===== MANEJO DEL FORMULARIO =====
    
    if (formulario) {
        formulario.addEventListener('submit', (e) => {
            e.preventDefault(); // Evita el envío real del formulario
            
            // Limpia errores previos de los campos
            limpiarTodosLosErrores();
            
            // Valida el formulario
            if (validarFormulario()) {
                procesarRegistro();
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

        // 2. Validar Apellido
        if (apellido.value.trim() === '') {
            mostrarError(apellido, 'El apellido es obligatorio.');
            esValido = false;
        }

        // 3. Validar Documento (DNI)
        if (documento.value.trim().length !== 8 || !/^\d+$/.test(documento.value)) {
            mostrarError(documento, 'El documento debe tener 8 dígitos numéricos.');
            esValido = false;
        }

        // 4. Validar Correo
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(correo.value)) {
            mostrarError(correo, 'Ingresa un correo electrónico válido.');
            esValido = false;
        }
        
        // 5. Validar Fecha de Nacimiento
        if (fechaNacimiento.value === '') {
            mostrarError(fechaNacimiento, 'La fecha de nacimiento es obligatoria.');
            esValido = false;
        }

        // 6. Validar Género
        const generoSeleccionado = document.querySelector('input[name="genero"]:checked');
        if (!generoSeleccionado) {
            // Muestra el error en el grupo de radios
            mostrarError(document.querySelector('.grupo-radio'), 'Debes seleccionar un género.');
            esValido = false;
        }

        // 7. Validar Contraseña
        if (contrasena.value.length < 6) {
            mostrarError(contrasena, 'La contraseña debe tener al menos 6 caracteres.');
            esValido = false;
        }

        // 8. Validar Confirmación de Contraseña
        if (confirmarContrasena.value !== contrasena.value) {
            mostrarError(confirmarContrasena, 'Las contraseñas no coinciden.');
            esValido = false;
        }
        
        // 9. Validar Términos y Condiciones
        if (!terminos.checked) {
            mostrarError(terminos.parentElement, 'Debes aceptar los términos y condiciones.');
            esValido = false;
        }

        return esValido;
    }

    /**
     * Procesa el registro (simulación)
     */
    function procesarRegistro() {
        const botonSubmit = formulario.querySelector('button[type="submit"]');
        botonSubmit.textContent = 'Registrando...';
        botonSubmit.disabled = true;

        // Recolecta los datos (listos para enviar a un backend)
        const datosUsuario = {
            // Tabla usuario
            correo: correo.value,
            // Tabla socio
            nombre: nombre.value,
            apellido: apellido.value,
            documento: documento.value,
            fecha_nacimiento: fechaNacimiento.value,
            genero: document.querySelector('input[name="genero"]:checked').value,
            contrasena: contrasena.value // ¡Recuerda añadir esta columna a tu BD!
        };

        console.log('Datos listos para enviar al backend (MySQL):', datosUsuario);

        // Simula una llamada al backend
        setTimeout(() => {
            // Muestra mensaje de éxito
            mostrarMensajeFormulario('¡Registro exitoso! Redirigiendo al inicio de sesión...', 'exito');
            
            // Redirige a login después de 2 segundos
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);

        }, 1500);
    }

    // ===== FUNCIONES AUXILIARES DE ERRORES =====

    /**
     * Muestra un mensaje de error en un campo
     * @param {HTMLElement} inputElement - Campo de entrada o grupo
     * @param {string} mensaje - Mensaje de error
     */
    function mostrarError(inputElement, mensaje) {
        inputElement.classList.add('error'); // Para el borde rojo
        
        // Busca un elemento de mensaje de error existente
        let mensajeError = inputElement.parentElement.querySelector('.mensaje-error-campo');
        if (!mensajeError) {
            mensajeError = document.createElement('p');
            mensajeError.className = 'mensaje-error-campo'; // Usamos una clase CSS de login.css si existe
            // Si no, adaptamos el estilo de .mensaje-error
            mensajeError.style.color = '#e50914';
            mensajeError.style.fontSize = '0.875rem';
            mensajeError.style.marginTop = '0.25rem';
            
            // Inserta el mensaje después del campo o del contenedor del campo
            if(inputElement.type === 'date' || inputElement.classList.contains('grupo-radio') || inputElement.parentElement.classList.contains('filtro-opcion')) {
                 inputElement.parentElement.appendChild(mensajeError);
            } else {
                 inputElement.parentElement.appendChild(mensajeError);
            }
        }
        mensajeError.textContent = mensaje;
    }

    /**
     * Limpia el mensaje de error de un campo
     * @param {HTMLElement} inputElement - Campo de entrada
     */
    function limpiarError(inputElement) {
        inputElement.classList.remove('error');
        const mensajeError = inputElement.parentElement.querySelector('.mensaje-error-campo');
        if (mensajeError) {
            mensajeError.remove();
        }
    }

    /**
     * Limpia todos los errores del formulario
     */
    function limpiarTodosLosErrores() {
        const todosLosInputs = formulario.querySelectorAll('.input-campo, .grupo-radio, .filtro-opcion input');
        todosLosInputs.forEach(input => {
            input.classList.remove('error');
            const mensajeError = input.parentElement.querySelector('.mensaje-error-campo');
            if (mensajeError) {
                mensajeError.remove();
            }
            // Limpia error de grupo-radio
             const radioError = document.querySelector('.grupo-radio + .mensaje-error-campo');
             if(radioError) radioError.remove();
             // Limpia error de terminos
             const terminosError = document.querySelector('.filtro-opcion .mensaje-error-campo');
             if(terminosError) terminosError.remove();
        });
        mensajeFormulario.textContent = '';
        mensajeFormulario.className = '';
    }

    /**
     * Muestra un mensaje general en el formulario (éxito o error)
     * @param {string} mensaje
     * @param {'exito' | 'error'} tipo
     */
    function mostrarMensajeFormulario(mensaje, tipo) {
        mensajeFormulario.textContent = mensaje;
        mensajeFormulario.className = tipo; // Asigna 'exito' o 'error'
    }

});