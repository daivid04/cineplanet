/**
 * Entradas - Página de selección de tipos de entrada
 * Carga tipos de entrada desde la base de datos
 */

import { fetchFromApi } from "./data-manager.js";
import { iniciarTimer, detenerTimer, getTiempoRestante } from "./components/butaca/timer.js";

// ===== ESTADO =====
let cantButacasReservadas = 0;
let cantEntradasSeleccionadas = 0;
let entradasDetalle = []; // Array de { id_tipo_entrada, tipo, categoria, cantidad, precio }
let tiposEntrada = []; // Tipos de BD
let descuentoSocio = 0;
let nombreSocio = '';

// ===== ELEMENTOS DOM =====
let numeroButacasSpan;
let textoResumenEntradas;
let btnContinuar;
let timerDisplay;
let contenedorEntradas;

// ===== INICIALIZACIÓN =====

async function cargarDatosFuncion() {
  const idFuncion = sessionStorage.getItem('id_funcion');
  if (!idFuncion) {
    console.error('No hay función seleccionada');
    return;
  }

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

      if (data.pelicula_imagen) {
        const posterDiv = document.getElementById('movie-poster');
        if (posterDiv) {
          posterDiv.style.backgroundImage = `url('${data.pelicula_imagen}')`;
        }
      }
    }
  } catch (error) {
    console.error('Error al cargar datos de función:', error);
  }
}

async function cargarTiposEntrada() {
  try {
    const response = await fetchFromApi('tipo_entrada');

    if (response.success && response.data) {
      tiposEntrada = response.data;
      renderizarTiposEntrada();
    } else {
      console.error('Error al cargar tipos de entrada');
    }
  } catch (error) {
    console.error('Error al cargar tipos de entrada:', error);
  }
}

function renderizarTiposEntrada() {
  contenedorEntradas = document.getElementById('entradas-generales');
  if (!contenedorEntradas) return;

  // Limpiar contenido actual excepto el título
  const titulo = contenedorEntradas.querySelector('h2');
  contenedorEntradas.innerHTML = '';
  if (titulo) contenedorEntradas.appendChild(titulo);

  // Renderizar cada tipo de entrada desde la BD
  tiposEntrada.forEach(tipo => {
    const itemHTML = crearItemEntrada(tipo);
    contenedorEntradas.insertAdjacentHTML('beforeend', itemHTML);
  });

  // Configurar eventos después de renderizar
  configurarControlesCantidad();
}

async function cargarDescuentosUsuario() {
  const idUsuario = sessionStorage.getItem('id_usuario');
  if (!idUsuario) return;

  try {
    const response = await fetchFromApi('tipo_socio', { usuario: idUsuario });
    if (response.success && response.data) {
      descuentoSocio = parseFloat(response.data.desc_boleto) || 0;
      nombreSocio = response.data.nombre;
      console.log(`Usuario socio: ${nombreSocio}, Descuento: ${descuentoSocio}%`);

      if (descuentoSocio > 0) {
        mostrarNotificacionDescuento();
      }
    }
  } catch (error) {
    console.error('Error al cargar descuentos:', error);
  }
}

function mostrarNotificacionDescuento() {
  const container = document.querySelector('.container');
  const banner = document.createElement('div');
  banner.className = 'banner-descuento';
  banner.innerHTML = `<i class="fas fa-crown"></i> ¡Eres socio <strong>${nombreSocio}</strong>! Tienes <strong>${descuentoSocio}% de descuento</strong> en entradas.`;
  banner.style.cssText = `
    background: linear-gradient(90deg, #6a1b9a, #8e24aa);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    animation: slideDown 0.5s ease-out;
  `;

  const titulo = document.getElementById('titulo-entradas');
  if (titulo && titulo.parentNode) {
    titulo.parentNode.insertBefore(banner, titulo.nextSibling);
  } else if (container) {
    container.insertBefore(banner, container.firstChild);
  }
}

function crearItemEntrada(tipo) {
  const categoriaLabel = {
    'adulto': '',
    'nino': '<span class="tag-categoria nino">Niño</span>',
    'mayor': '<span class="tag-categoria mayor">60+ años</span>',
    'conadis': '<span class="tag-categoria conadis">Conadis</span>'
  };

  const precioOriginal = parseFloat(tipo.precio);
  let precioFinal = precioOriginal;
  let htmlPrecio = `S/${precioOriginal.toFixed(2)}`;

  if (descuentoSocio > 0) {
    precioFinal = precioOriginal * (1 - descuentoSocio / 100);
    htmlPrecio = `
      <span class="precio-tachado" style="text-decoration: line-through; color: #999; font-size: 0.9em;">S/${precioOriginal.toFixed(2)}</span>
      <span class="precio-descuento" style="color: #e91e63; font-weight: bold;">S/${precioFinal.toFixed(2)}</span>
    `;
  }

  return `
    <div class="item-entrada" 
         data-id="${tipo.id_tipo_entrada}" 
         data-precio="${precioFinal.toFixed(2)}" 
         data-categoria="${tipo.categoria}">
      <div class="info">
        <h4>${tipo.nombre} ${categoriaLabel[tipo.categoria] || ''}</h4>
        <span class="descripcion">${tipo.descripcion || ''}</span>
        <div class="precio-normal">${htmlPrecio}</div>
      </div>
      <div class="control-cantidad">
        <button class="btn-restar" disabled>-</button>
        <span class="cantidad">0</span>
        <button class="btn-sumar">+</button>
      </div>
    </div>
  `;
}

function inicializarElementos() {
  numeroButacasSpan = document.getElementById('numero-butacas');
  textoResumenEntradas = document.getElementById('texto-resumen-entradas');
  btnContinuar = document.getElementById('btn-continuar');
  timerDisplay = document.getElementById('timer-display');

  cantButacasReservadas = parseInt(sessionStorage.getItem('numeroButacasSeleccionadas')) || 2;
  numeroButacasSpan.textContent = cantButacasReservadas;
}

// ===== LÓGICA DE ENTRADAS =====

function actualizarResumen() {
  textoResumenEntradas.textContent = `Entradas seleccionadas: ${cantEntradasSeleccionadas} de ${cantButacasReservadas}`;

  if (cantEntradasSeleccionadas === cantButacasReservadas) {
    btnContinuar.disabled = false;
    btnContinuar.classList.add('enabled');
  } else {
    btnContinuar.disabled = true;
    btnContinuar.classList.remove('enabled');
  }

  const botonesSum = document.querySelectorAll('.btn-sumar');
  botonesSum.forEach(btn => {
    const cantidad = parseInt(btn.previousElementSibling.textContent);
    btn.disabled = (cantEntradasSeleccionadas >= cantButacasReservadas && cantidad === 0);
  });
}

function configurarControlesCantidad() {
  const controles = document.querySelectorAll('.control-cantidad');

  controles.forEach(control => {
    const btnRestar = control.querySelector('.btn-restar');
    const btnSumar = control.querySelector('.btn-sumar');
    const cantidadSpan = control.querySelector('.cantidad');
    const itemEntrada = control.closest('.item-entrada');

    const idTipoEntrada = parseInt(itemEntrada.dataset.id);
    const tipoEntrada = itemEntrada.querySelector('h4').textContent.trim();
    const precio = parseFloat(itemEntrada.dataset.precio);
    const categoria = itemEntrada.dataset.categoria;

    let cantidad = 0;

    btnSumar.addEventListener('click', () => {
      if (cantEntradasSeleccionadas < cantButacasReservadas) {
        cantidad++;
        cantEntradasSeleccionadas++;
        cantidadSpan.textContent = cantidad;
        btnRestar.disabled = false;

        actualizarEntradasDetalle(idTipoEntrada, tipoEntrada, categoria, cantidad, precio);
        actualizarResumen();
      }
    });

    btnRestar.addEventListener('click', () => {
      if (cantidad > 0) {
        cantidad--;
        cantEntradasSeleccionadas--;
        cantidadSpan.textContent = cantidad;
        btnRestar.disabled = (cantidad === 0);

        actualizarEntradasDetalle(idTipoEntrada, tipoEntrada, categoria, cantidad, precio);
        actualizarResumen();
      }
    });
  });
}

function actualizarEntradasDetalle(idTipo, tipo, categoria, cantidad, precio) {
  const index = entradasDetalle.findIndex(e => e.id_tipo_entrada === idTipo);

  if (cantidad === 0) {
    if (index !== -1) entradasDetalle.splice(index, 1);
  } else if (index !== -1) {
    entradasDetalle[index].cantidad = cantidad;
  } else {
    entradasDetalle.push({ id_tipo_entrada: idTipo, tipo, categoria, cantidad, precio });
  }
}

function guardarEntradasEnSession() {
  const total = entradasDetalle.reduce((sum, e) => sum + (e.cantidad * e.precio), 0);

  sessionStorage.setItem('entradasDetalle', JSON.stringify(entradasDetalle));
  sessionStorage.setItem('totalEntradas', total.toFixed(2));
  sessionStorage.setItem('cantidadEntradas', cantEntradasSeleccionadas.toString());
}

// ===== TEMPORIZADOR =====

function configurarTemporizador() {
  const tiempoGuardado = sessionStorage.getItem('tiempoRestante');
  const minutos = tiempoGuardado ? Math.ceil(parseInt(tiempoGuardado) / 60) : 4;

  iniciarTimer(timerDisplay, onTiempoAgotado, minutos);
}

function onTiempoAgotado() {
  alert('Se ha agotado el tiempo. Serás redirigido al inicio.');
  sessionStorage.clear();
  window.location.href = '../../index.html';
}

// ===== NAVEGACIÓN =====

function configurarNavegacion() {
  const btnUsuario = document.getElementById('btn-usuario');
  const btnCerrar = document.getElementById('btn-cerrar');

  if (btnUsuario) {
    btnUsuario.addEventListener('click', () => window.location.href = 'login.html');
  }

  if (btnCerrar) {
    btnCerrar.addEventListener('click', () => {
      if (confirm('¿Deseas salir de la compra?')) {
        detenerTimer();
        sessionStorage.clear();
        window.location.href = '../../index.html';
      }
    });
  }

  btnContinuar.addEventListener('click', onContinuar);
}

function onContinuar() {
  if (btnContinuar.disabled) return;

  guardarEntradasEnSession();
  sessionStorage.setItem('tiempoRestante', getTiempoRestante().toString());

  detenerTimer();
  window.location.href = 'dulceria.html';
}

// ===== MAIN =====

async function inicializarPagina() {
  console.log('Inicializando página de entradas...');

  inicializarElementos();
  await cargarDatosFuncion();
  await cargarDescuentosUsuario(); // Cargar descuentos antes de renderizar
  await cargarTiposEntrada();
  configurarTemporizador();
  configurarNavegacion();
  actualizarResumen();

  console.log('Página de entradas lista');
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarPagina);
} else {
  inicializarPagina();
}
