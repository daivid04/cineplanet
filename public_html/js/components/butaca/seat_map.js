/**
 * Mapa de asientos dinámico desde base de datos
 */

import { fetchFromApi } from '../../data-manager.js';
import {
  state,
  seleccionarAsiento,
  deseleccionarAsiento,
  estaSeleccionado,
  estaOcupado,
  setAsientosOcupados,
  getButacasOrdenadas
} from './seat_state.js';

let mapaAsientos = null;
let listaButacas = null;
let btnContinuar = null;

/**
 * Inicializa el mapa de asientos
 * @param {number} idSala - ID de la sala
 * @param {number} idFuncion - ID de la función
 */
export async function inicializarMapa(idSala, idFuncion) {
  mapaAsientos = document.getElementById('mapa-asientos');
  listaButacas = document.getElementById('lista-butacas');
  btnContinuar = document.getElementById('btn-continuar');

  state.idSala = idSala;
  state.idFuncion = idFuncion;

  if (!mapaAsientos) {
    console.error('No se encontró el contenedor del mapa');
    return;
  }

  mapaAsientos.innerHTML = '<p class="cargando">Cargando asientos...</p>';

  try {
    // Cargar asientos ocupados para esta función
    const ocupadosRes = await fetchFromApi('asiento', { funcion: idFuncion });
    if (ocupadosRes.success) {
      setAsientosOcupados(ocupadosRes.data);
    }

    // Cargar mapa de la sala
    const mapaRes = await fetchFromApi('asiento', { sala: idSala, mapa: 1 });

    if (!mapaRes.success || !mapaRes.data) {
      mapaAsientos.innerHTML = '<p class="error">No hay asientos configurados para esta sala.</p>';
      return;
    }

    renderizarMapa(mapaRes.data);
  } catch (error) {
    console.error('Error al cargar asientos:', error);
    mapaAsientos.innerHTML = '<p class="error">Error al cargar los asientos.</p>';
  }
}

/**
 * Renderiza el mapa de asientos con pasillos
 */
function renderizarMapa(dataMapa) {
  mapaAsientos.innerHTML = '';

  const { mapa, filas, columnas } = dataMapa;

  // Configuración de pasillos: después de qué columnas agregar espacio
  // Esto crea una distribución más natural como: [2 asientos] [pasillo] [6 asientos] [pasillo] [2 asientos]
  const pasillosDesp = [2, 8]; // Pasillo después de columna 2 y 8

  // Escalonar las primeras filas para efecto de cine
  const escalonado = {
    'A': { inicio: 3, fin: 8 },   // Fila A: solo columnas 3-8
    'B': { inicio: 2, fin: 9 },   // Fila B: columnas 2-9
    // El resto usa todo el ancho
  };

  filas.forEach(fila => {
    const filaDiv = document.createElement('div');
    filaDiv.className = 'fila-asientos';

    // Letra de la fila (izquierda)
    const letraIzq = document.createElement('div');
    letraIzq.className = 'letra-fila';
    letraIzq.textContent = fila;
    filaDiv.appendChild(letraIzq);

    // Obtener rango de asientos para esta fila
    const rango = escalonado[fila] || { inicio: 1, fin: columnas };

    // Asientos de esta fila
    for (let col = 1; col <= columnas; col++) {
      // Si está fuera del rango escalonado, agregar espacio vacío
      if (col < rango.inicio || col > rango.fin) {
        const espacio = document.createElement('div');
        espacio.className = 'espacio-pasillo';
        filaDiv.appendChild(espacio);
        continue;
      }

      // Agregar pasillo si corresponde
      if (pasillosDesp.includes(col - 1) && col > rango.inicio) {
        const pasillo = document.createElement('div');
        pasillo.className = 'espacio-pasillo';
        filaDiv.appendChild(pasillo);
      }

      const asientoData = mapa[fila]?.[col];

      if (!asientoData) {
        const espacio = document.createElement('div');
        espacio.className = 'espacio-pasillo';
        filaDiv.appendChild(espacio);
        continue;
      }

      const asientoDiv = crearAsiento(asientoData, fila, col);
      filaDiv.appendChild(asientoDiv);
    }

    // Letra de la fila (derecha)
    const letraDer = document.createElement('div');
    letraDer.className = 'letra-fila';
    letraDer.textContent = fila;
    filaDiv.appendChild(letraDer);

    mapaAsientos.appendChild(filaDiv);
  });
}

/**
 * Crea un elemento de asiento
 */
/**
 * Crea un elemento de asiento
 */
function crearAsiento(asientoData, fila, col) {
  const { id, estado, es_silla_ruedas, codigo } = asientoData;
  const ocupado = estaOcupado(codigo);
  const seleccionado = estaSeleccionado(id);

  const asientoDiv = document.createElement('div');
  asientoDiv.dataset.id = id;
  asientoDiv.dataset.codigo = codigo;
  asientoDiv.dataset.fila = fila;
  asientoDiv.dataset.columna = col;

  // Determinar clase
  let clases = ['asiento'];

  if (ocupado || estado === 'ocupado') {
    clases.push('ocupada');
  } else if (estado === 'reservado') {
    clases.push('reservada');
  } else if (seleccionado) {
    clases.push('seleccionada');
    clases.push('disponible');
  } else {
    clases.push('disponible');
  }

  if (es_silla_ruedas) {
    clases.push('discapacitado');
    asientoDiv.innerHTML = `
      <svg viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h2.2l1.8-2h4l1.8 2H18v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z"/>
      </svg>
    `;
  }

  asientoDiv.className = clases.join(' ');

  // Solo agregar evento click si está disponible
  if (!ocupado && estado !== 'ocupado' && estado !== 'reservado') {
    asientoDiv.addEventListener('click', () => toggleAsiento(asientoDiv));
  }

  return asientoDiv;
}

/**
 * Alterna la selección de un asiento
 */
function toggleAsiento(asientoDiv) {
  const codigo = asientoDiv.dataset.codigo;
  const id = parseInt(asientoDiv.dataset.id);

  if (asientoDiv.classList.contains('seleccionada')) {
    asientoDiv.classList.remove('seleccionada');
    deseleccionarAsiento(id);
  } else {
    asientoDiv.classList.add('seleccionada');
    seleccionarAsiento(id, codigo);
  }

  actualizarListaButacas();
}

/**
 * Actualiza el display de butacas seleccionadas
 */
export function actualizarListaButacas() {
  if (!listaButacas || !btnContinuar) return;

  if (state.butacasSeleccionadas.length === 0) {
    listaButacas.textContent = '-';
    btnContinuar.disabled = true;
  } else {
    listaButacas.textContent = getButacasOrdenadas().join(', ');
    btnContinuar.disabled = false;
  }
}
