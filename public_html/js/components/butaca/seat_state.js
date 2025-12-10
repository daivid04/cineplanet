/**
 * Estado compartido para la selección de butacas
 */

// Estado global
export const state = {
  butacasSeleccionadas: [], // Array de objetos {id, codigo}
  asientosOcupados: [],
  idFuncion: null,
  idSala: null,
  datosReserva: {}
};

/**
 * Agrega un asiento a la selección
 */
export function seleccionarAsiento(id, codigo) {
  if (!state.butacasSeleccionadas.some(b => b.id === id)) {
    state.butacasSeleccionadas.push({ id, codigo });
  }
}

/**
 * Remueve un asiento de la selección
 */
export function deseleccionarAsiento(id) {
  state.butacasSeleccionadas = state.butacasSeleccionadas.filter(b => b.id !== id);
}

/**
 * Verifica si un asiento está seleccionado
 */
export function estaSeleccionado(id) {
  return state.butacasSeleccionadas.some(b => b.id == id); // Loose equality for string/int IDs
}

/**
 * Verifica si un asiento está ocupado
 */
export function estaOcupado(codigo) {
  return state.asientosOcupados.some(a => a.codigo === codigo);
}

/**
 * Establece los asientos ocupados
 */
export function setAsientosOcupados(ocupados) {
  state.asientosOcupados = ocupados || [];
}

/**
 * Obtiene las butacas seleccionadas ordenadas (solo códigos)
 */
export function getButacasOrdenadas() {
  return [...state.butacasSeleccionadas]
    .sort((a, b) => {
      const filaA = a.codigo.charAt(0);
      const filaB = b.codigo.charAt(0);
      if (filaA !== filaB) return filaA.localeCompare(filaB);
      return parseInt(a.codigo.substring(1)) - parseInt(b.codigo.substring(1));
    })
    .map(b => b.codigo);
}

/**
 * Obtiene los IDs de las butacas seleccionadas
 */
export function getButacasIds() {
  return state.butacasSeleccionadas.map(b => b.id);
}

/**
 * Limpia toda la selección
 */
export function limpiarSeleccion() {
  state.butacasSeleccionadas = [];
}
