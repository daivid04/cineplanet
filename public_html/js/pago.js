/**
 * Pago - Página de resumen y confirmación de pago
 * Usa sessionStorage para datos de la compra
 */

import { fetchFromApi } from "./data-manager.js";
import { iniciarTimer, detenerTimer, getTiempoRestante } from "./components/butaca/timer.js";

// ===== INICIALIZACIÓN =====

async function init() {
  console.log('Inicializando página de pago...');

  await cargarDatosFuncion();
  cargarResumenCompra();
  configurarTimer();
  configurarNavegacion();

  console.log('Página de pago lista');
}

async function cargarDatosFuncion() {
  const idFuncion = sessionStorage.getItem('id_funcion');
  if (!idFuncion) return;

  try {
    const response = await fetchFromApi('funcion', 'id', idFuncion);
    const data = response.data;

    if (data) {
      document.getElementById('movie-title').textContent = data.pelicula_nombre || '';
      document.getElementById('movie-details').textContent = data.formatos || '';
      document.getElementById('cinema-name').textContent = data.sede_nombre || '';
      document.getElementById('showtime-date').textContent = data.fecha || '';
      document.getElementById('showtime-time').textContent = data.hora || '';
      document.getElementById('room-name').textContent = `SALA ${data.numero_sala || ''}`;

      const posterImg = document.getElementById('poster-img');
      if (posterImg && data.pelicula_imagen) {
        posterImg.src = data.pelicula_imagen;
      }
    }
  } catch (error) {
    console.error('Error cargando función:', error);
  }
}

function cargarResumenCompra() {
  // === BUTACAS ===
  const butacas = JSON.parse(sessionStorage.getItem('reservaButacas')) || [];
  const numButacas = sessionStorage.getItem('numeroButacasSeleccionadas') || '0';

  document.getElementById('numero-butacas').textContent = numButacas;
  document.getElementById('cant-butacas').textContent = numButacas;
  document.getElementById('resumen-butacas').textContent =
    Array.isArray(butacas) ? butacas.join(', ') : butacas;

  // === ENTRADAS ===
  const entradasDetalle = JSON.parse(sessionStorage.getItem('entradasDetalle')) || [];
  const totalEntradas = parseFloat(sessionStorage.getItem('totalEntradas')) || 0;
  const cantEntradas = sessionStorage.getItem('cantidadEntradas') || '0';

  document.getElementById('numero-entradas').textContent = cantEntradas;

  // Renderizar detalle de entradas
  const seccionEntradas = document.querySelector('.seccion-resumen:nth-child(2)');
  if (seccionEntradas && entradasDetalle.length > 0) {
    let htmlEntradas = '<h4>Entradas:</h4>';

    entradasDetalle.forEach(entrada => {
      const subtotal = entrada.cantidad * entrada.precio;
      htmlEntradas += `
        <div class="fila-resumen">
          <div class="detalle-producto">
            <span>${entrada.tipo}</span>
            <small class="categoria-${entrada.categoria}">${getCategoriaLabel(entrada.categoria)}</small>
          </div>
          <span class="cantidad-resumen">Cant. ${entrada.cantidad}</span>
          <span class="precio-resumen">S/${subtotal.toFixed(2)}</span>
        </div>
      `;
    });

    htmlEntradas += `
      <div class="subtotal-resumen">
        <span>Sub-Total <span>S/${totalEntradas.toFixed(2)}</span></span>
      </div>
    `;

    seccionEntradas.innerHTML = htmlEntradas;
  }

  // === DULCERÍA ===
  const ordenDulceria = JSON.parse(sessionStorage.getItem('ordenDulceria')) || [];
  const totalDulceria = parseFloat(sessionStorage.getItem('totalDulceria')) || 0;

  document.getElementById('numero-dulceria').textContent = ordenDulceria.length;

  const seccionDulceria = document.getElementById('seccion-dulceria-resumen');
  if (seccionDulceria) {
    seccionDulceria.innerHTML = '<h4>Dulcería:</h4>';

    if (ordenDulceria.length > 0) {
      // Agrupar por ID
      const agrupado = {};
      ordenDulceria.forEach(item => {
        if (!agrupado[item.id]) {
          agrupado[item.id] = { ...item, cantidad: 0 };
        }
        agrupado[item.id].cantidad++;
      });

      Object.values(agrupado).forEach(item => {
        const subtotal = item.precio * item.cantidad;
        seccionDulceria.innerHTML += `
          <div class="fila-resumen">
            <div class="detalle-producto">
              <span>${item.nombre}</span>
            </div>
            <span class="cantidad-resumen">Cant. ${item.cantidad}</span>
            <span class="precio-resumen">S/${subtotal.toFixed(2)}</span>
          </div>
        `;
      });

      seccionDulceria.innerHTML += `
        <div class="subtotal-resumen">
          <span>Sub-Total <span>S/${totalDulceria.toFixed(2)}</span></span>
        </div>
      `;
    } else {
      seccionDulceria.innerHTML += '<p class="sin-productos">No seleccionaste productos.</p>';
    }
  }

  // === TOTAL GENERAL ===
  const totalGeneral = totalEntradas + totalDulceria;
  document.getElementById('precio-total-lateral').textContent = `S/${totalGeneral.toFixed(2)}`;
  document.getElementById('precio-total-final').textContent = `S/${totalGeneral.toFixed(2)}`;

  // Guardar total final en sessionStorage
  sessionStorage.setItem('totalCompra', totalGeneral.toFixed(2));
}

function getCategoriaLabel(categoria) {
  const labels = {
    'adulto': '',
    'nino': '(Niño)',
    'mayor': '(60+ años)',
    'conadis': '(Conadis)'
  };
  return labels[categoria] || '';
}

// ===== TIMER =====

function configurarTimer() {
  const tiempoGuardado = sessionStorage.getItem('tiempoRestante');
  const minutos = tiempoGuardado ? Math.ceil(parseInt(tiempoGuardado) / 60) : 4;

  const timerDisplay = document.getElementById('timer-display');
  iniciarTimer(timerDisplay, onTiempoAgotado, minutos);
}

function onTiempoAgotado() {
  alert('Se agotó el tiempo. Serás redirigido al inicio.');
  sessionStorage.clear();
  window.location.href = '../../index.html';
}

// ===== NAVEGACIÓN =====

function configurarNavegacion() {
  // Cerrar
  document.getElementById('btn-cerrar')?.addEventListener('click', () => {
    if (confirm('¿Deseas salir de la compra?')) {
      detenerTimer();
      sessionStorage.clear();
      window.location.href = '../../index.html';
    }
  });

  // Usuario
  document.getElementById('btn-usuario')?.addEventListener('click', () => {
    window.location.href = 'login.html';
  });

  // Cerrar resumen (volver atrás)
  document.querySelector('.btn-cerrar-resumen')?.addEventListener('click', () => {
    window.location.href = 'dulceria.html';
  });

  // Continuar al método de pago
  document.querySelector('.btn-continuar-pago')?.addEventListener('click', () => {
    sessionStorage.setItem('tiempoRestante', getTiempoRestante().toString());
    detenerTimer();
    window.location.href = 'medio_pago.html';
  });
}

// ===== INIT =====
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
