// ===== ELEMENTOS DEL DOM =====
const formularioLogin = document.getElementById('formulario-login');
const inputNumeroSocio = document.getElementById('numero-socio');
const inputContrasena = document.getElementById('contrasena');
const togglePassword = document.getElementById('toggle-password');

// ===== FUNCIONES DE VALIDACIÓN =====

/**
 * Valida el número de socio
 * @param {string} numeroSocio - Número de socio ingresado
 * @returns {boolean} - True si es válido
 */
function validarNumeroSocio(numeroSocio) {
  // Elimina espacios en blanco
  const numeroLimpio = numeroSocio.trim();

  // Verifica que no esté vacío y tenga al menos 6 caracteres
  if (numeroLimpio.length < 6) {
    return false;
  }

  return true;
}

/**
 * Valida la contraseña
 * @param {string} contrasena - Contraseña ingresada
 * @returns {boolean} - True si es válida
 */
function validarContrasena(contrasena) {
  // Verifica que no esté vacía y tenga al menos 4 caracteres
  if (contrasena.trim().length < 4) {
    return false;
  }

  return true;
}

/**
 * Muestra un mensaje de error en un campo
 * @param {HTMLElement} input - Campo de entrada
 * @param {string} mensaje - Mensaje de error
 */
function mostrarError(input, mensaje) {
  input.classList.add('error');

  // Verifica si ya existe un mensaje de error
  let mensajeError = input.parentElement.querySelector('.mensaje-error');

  if (!mensajeError) {
    mensajeError = document.createElement('p');
    mensajeError.className = 'mensaje-error';
    input.parentElement.appendChild(mensajeError);
  }

  mensajeError.textContent = mensaje;
  mensajeError.classList.add('mostrar');
}

/**
 * Limpia el mensaje de error de un campo
 * @param {HTMLElement} input - Campo de entrada
 */
function limpiarError(input) {
  input.classList.remove('error');

  const mensajeError = input.parentElement.querySelector('.mensaje-error');
  if (mensajeError) {
    mensajeError.classList.remove('mostrar');
  }
}

// ===== TOGGLE DE CONTRASEÑA =====

/**
 * Alterna la visibilidad de la contraseña
 */
function togglePasswordVisibility() {
  const tipo = inputContrasena.type;

  if (tipo === 'password') {
    inputContrasena.type = 'text';
    // Cambia el icono a "ojo cerrado"
    togglePassword.innerHTML = `
            <svg class="icono-ojo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
  } else {
    inputContrasena.type = 'password';
    // Cambia el icono a "ojo abierto"
    togglePassword.innerHTML = `
            <svg class="icono-ojo" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
  }
}

// ===== MANEJO DEL FORMULARIO =====

/**
 * Maneja el envío del formulario
 * @param {Event} e - Evento de envío
 */
function manejarEnvioFormulario(e) {
  e.preventDefault();

  // Limpia errores previos
  limpiarError(inputNumeroSocio);
  limpiarError(inputContrasena);

  const numeroSocio = inputNumeroSocio.value.trim();
  const contrasena = inputContrasena.value.trim();

  let formularioValido = true;

  // Valida número de socio
  if (!validarNumeroSocio(numeroSocio)) {
    mostrarError(inputNumeroSocio, 'Por favor, ingresa un número de socio válido (mínimo 6 caracteres)');
    formularioValido = false;
  }

  // Valida contraseña
  if (!validarContrasena(contrasena)) {
    mostrarError(inputContrasena, 'Por favor, ingresa una contraseña válida (mínimo 4 caracteres)');
    formularioValido = false;
  }

  // Si el formulario es válido, procesa el login
  if (formularioValido) {
    procesarLogin(numeroSocio, contrasena);
  }
}

/**
 * Procesa el inicio de sesión
 * @param {string} numeroSocio - Número de socio (Documento)
 * @param {string} contrasena - Contraseña
 */
async function procesarLogin(numeroSocio, contrasena) {
  console.log('Iniciando sesión...');

  const botonIngresar = formularioLogin.querySelector('.boton-ingresar');
  const textoOriginal = botonIngresar.textContent;
  botonIngresar.textContent = 'Ingresando...';
  botonIngresar.disabled = true;

  try {
    const response = await fetch('../api/login_api.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        documento: numeroSocio,
        contrasena: contrasena
      })
    });

    const data = await response.json();

    if (response.ok && data.ok) {
      // Login exitoso
      console.log('Login exitoso:', data.usuario);

      // Guardar sesión
      localStorage.setItem('usuario_cineplanet', JSON.stringify({
        ...data.usuario,
        fechaLogin: new Date().toISOString()
      }));

      // Redirigir
      window.location.href = '../../index.html';
    } else {
      throw new Error(data.error || 'Credenciales incorrectas');
    }

  } catch (error) {
    console.error('Error de login:', error);
    mostrarError(inputContrasena, error.message);

    // Resetear botón
    botonIngresar.textContent = textoOriginal;
    botonIngresar.disabled = false;
  }
}

// ===== LIMPIEZA DE ERRORES AL ESCRIBIR =====

/**
 * Limpia el error cuando el usuario empieza a escribir
 */
function limpiarErrorAlEscribir() {
  this.classList.remove('error');
  const mensajeError = this.parentElement.querySelector('.mensaje-error');
  if (mensajeError) {
    mensajeError.classList.remove('mostrar');
  }
}

// ===== INICIALIZACIÓN DE EVENTOS =====

/**
 * Inicializa todos los eventos de la página
 */
function inicializarEventos() {
  // Evento de envío del formulario
  if (formularioLogin) {
    formularioLogin.addEventListener('submit', manejarEnvioFormulario);
  }

  // Evento de toggle de contraseña
  if (togglePassword) {
    togglePassword.addEventListener('click', togglePasswordVisibility);
  }

  // Eventos para limpiar errores al escribir
  if (inputNumeroSocio) {
    inputNumeroSocio.addEventListener('input', limpiarErrorAlEscribir);
  }

  if (inputContrasena) {
    inputContrasena.addEventListener('input', limpiarErrorAlEscribir);
  }
}

// ===== VERIFICAR SI YA HAY SESIÓN ACTIVA =====

/**
 * Verifica si el usuario ya tiene sesión activa
 */
function verificarSesionActiva() {
  const usuario = localStorage.getItem('usuario_cineplanet');

  if (usuario) {
    // Si ya hay sesión, podría redirigir automáticamente
    // o mostrar un mensaje
    console.log('Usuario ya tiene sesión activa');
    // Opcional: redirigir automáticamente
    // window.location.href = 'inicio.html';
  }
}

// ===== INICIO DE LA APLICACIÓN =====

/**
 * Inicializa la página de login
 */
function inicializarLogin() {
  console.log('Inicializando página de login...');

  // Verifica si hay sesión activa
  verificarSesionActiva();

  // Inicializa eventos
  inicializarEventos();

  console.log('Página de login lista');
}

// Espera a que el DOM esté cargado
document.addEventListener('DOMContentLoaded', inicializarLogin);
