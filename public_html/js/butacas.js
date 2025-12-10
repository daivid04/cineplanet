/**
 * Butacas - Página de selección de asientos
 * Orquestador de componentes modulares
 */

// Componentes
import { renderButaca } from './components/butaca/render_butaca.js';
import { inicializarMapa } from './components/butaca/seat_map.js';
import { iniciarTimer } from './components/butaca/timer.js';
import { inicializarNavegacion } from './components/butaca/navigation.js';
import { state } from './components/butaca/seat_state.js';

/**
 * Inicializa la aplicación de butacas
 */
async function inicializarAplicacion() {
  console.log('Inicializando página de selección de butacas...');

  // 1. Obtener datos de la función desde sessionStorage
  const idFuncion = sessionStorage.getItem('id_funcion');

  if (!idFuncion) {
    alert('No se ha seleccionado una función');
    window.location.href = 'seleccion.html';
    return;
  }

  // 2. Renderizar información de la película
  const datosFuncion = await renderButaca();

  // Guardar datos para reserva
  if (datosFuncion) {
    state.datosReserva = {
      idFuncion: idFuncion,
      idSala: datosFuncion.id_sala,
      titulo: datosFuncion.pelicula_nombre,
      imagenUrl: datosFuncion.pelicula_imagen,
      cine: datosFuncion.sede_nombre,
      hora: datosFuncion.hora,
      fecha: datosFuncion.fecha,
      formato: datosFuncion.formatos,
      sala: `SALA ${datosFuncion.numero_sala}`
    };

    // 3. Cargar mapa de asientos desde BD
    await inicializarMapa(datosFuncion.id_sala, idFuncion);
  }

  // 4. Iniciar temporizador (5 minutos)
  const tiempoRestante = document.getElementById('tiempo-restante');
  iniciarTimer(tiempoRestante, onTiempoAgotado, 5);

  // 5. Inicializar navegación
  inicializarNavegacion();

  console.log('Página de butacas lista');
}

/**
 * Callback cuando se agota el tiempo
 */
function onTiempoAgotado() {
  alert('Se ha agotado el tiempo de selección. Serás redirigido a la página anterior.');
  window.location.href = 'seleccion.html';
}

// ===== INICIALIZACIÓN =====
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
  inicializarAplicacion();
}
