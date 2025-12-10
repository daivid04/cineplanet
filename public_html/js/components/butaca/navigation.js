/**
 * Navegación para la página de butacas
 */

import { state, getButacasOrdenadas, getButacasIds } from './seat_state.js';
import { detenerTimer } from './timer.js';

/**
 * Maneja el clic en el botón Atrás
 */
export function irAtras() {
  if (state.butacasSeleccionadas.length > 0) {
    const confirmar = confirm('¿Estás seguro de que deseas salir? Perderás tu selección de butacas.');
    if (!confirmar) return;
  }
  detenerTimer();
  window.location.href = 'seleccion.html';
}

/**
 * Maneja el clic en el botón Cerrar
 */
export function cerrarVentana() {
  if (state.butacasSeleccionadas.length > 0) {
    const confirmar = confirm('¿Estás seguro de que deseas salir? Perderás tu selección de butacas.');
    if (!confirmar) return;
  }
  detenerTimer();
  sessionStorage.clear();
  window.location.href = '../../index.html';
}

/**
 * Maneja el clic en el botón Continuar
 */
export async function continuar() {
  if (state.butacasSeleccionadas.length === 0) {
    return;
  }

  // Guardar selección en sessionStorage
  sessionStorage.setItem('reservaButacas', JSON.stringify(getButacasOrdenadas()));
  sessionStorage.setItem('asientosIds', JSON.stringify(getButacasIds()));
  sessionStorage.setItem('numeroButacasSeleccionadas', state.butacasSeleccionadas.length.toString());

  // Guardar tiempo restante para continuar en siguiente página
  const { getTiempoRestante } = await import('./timer.js');
  sessionStorage.setItem('tiempoRestante', getTiempoRestante().toString());

  detenerTimer();
  window.location.href = 'entradas.html';
}

/**
 * Inicializa los eventos de navegación
 */
export function inicializarNavegacion() {
  const btnAtras = document.getElementById('btn-atras');
  const btnCerrar = document.getElementById('btn-cerrar');
  const btnUsuario = document.getElementById('btn-usuario');
  const btnContinuar = document.getElementById('btn-continuar');

  if (btnAtras) btnAtras.addEventListener('click', irAtras);
  if (btnCerrar) btnCerrar.addEventListener('click', cerrarVentana);
  if (btnUsuario) btnUsuario.addEventListener('click', () => window.location.href = 'login.html');
  if (btnContinuar) btnContinuar.addEventListener('click', continuar);
}
