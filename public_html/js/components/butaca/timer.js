/**
 * Temporizador de sesión para selección de butacas
 */

let tiempoSesion = 5 * 60; // 5 minutos
let intervaloTemporizador = null;
let elementoTiempo = null;
let onTimeoutCallback = null;

/**
 * Inicializa el temporizador
 * @param {HTMLElement} elemento - Elemento DOM donde mostrar el tiempo
 * @param {Function} onTimeout - Callback cuando se agota el tiempo
 * @param {number} minutos - Minutos para el timer (default 5)
 */
export function iniciarTimer(elemento, onTimeout, minutos = 5) {
  elementoTiempo = elemento;
  onTimeoutCallback = onTimeout;
  tiempoSesion = minutos * 60;

  actualizarDisplay();

  intervaloTemporizador = setInterval(() => {
    tiempoSesion--;
    actualizarDisplay();

    // Cambiar color cuando quede poco tiempo
    if (tiempoSesion <= 60 && elementoTiempo) {
      elementoTiempo.style.color = 'var(--color-rojo, #ff4444)';
    }

    // Cuando se acabe el tiempo
    if (tiempoSesion <= 0) {
      detenerTimer();
      if (onTimeoutCallback) {
        onTimeoutCallback();
      }
    }
  }, 1000);
}

/**
 * Actualiza el display del tiempo
 */
function actualizarDisplay() {
  if (!elementoTiempo) return;

  const minutos = Math.floor(tiempoSesion / 60);
  const segundos = tiempoSesion % 60;
  elementoTiempo.textContent = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;
}

/**
 * Detiene el temporizador
 */
export function detenerTimer() {
  if (intervaloTemporizador) {
    clearInterval(intervaloTemporizador);
    intervaloTemporizador = null;
  }
}

/**
 * Obtiene el tiempo restante en segundos
 */
export function getTiempoRestante() {
  return tiempoSesion;
}
