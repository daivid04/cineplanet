/**
 * Dulcería - Página de combos y productos
 * Patrón simple como el resto de la app
 */

import { fetchFromApi } from "./data-manager.js";
import { iniciarTimer, detenerTimer, getTiempoRestante } from "./components/butaca/timer.js";

// ===== ESTADO =====
let combos = [];
let ordenDulceria = [];
let totalDulceria = 0;
let descuentoSocio = 0;
let nombreSocio = '';

// ===== INICIALIZACIÓN =====

async function init() {
  console.log('Inicializando dulcería...');

  cargarDatosFuncion();
  await cargarDescuentosUsuario();
  await cargarCombos();
  configurarPestanas();
  configurarNavegacion();
  configurarTimer();
  actualizarOrden();

  console.log('Dulcería lista');
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

      // Poster
      const posterImg = document.getElementById('poster-img');
      if (posterImg && data.pelicula_imagen) {
        posterImg.src = data.pelicula_imagen;
      }
    }
  } catch (error) {
    console.error('Error cargando función:', error);
  }

  // Cargar info de butacas/entradas
  const numButacas = sessionStorage.getItem('numeroButacasSeleccionadas') || '0';
  const numEntradas = sessionStorage.getItem('cantidadEntradas') || '0';
  document.getElementById('numero-butacas').textContent = numButacas;
  document.getElementById('numero-entradas').textContent = numEntradas;
}

async function cargarDescuentosUsuario() {
  const idUsuario = sessionStorage.getItem('id_usuario');
  if (!idUsuario) return;

  try {
    const response = await fetchFromApi('tipo_socio', { usuario: idUsuario });
    if (response.success && response.data) {
      descuentoSocio = parseFloat(response.data.desc_dulces) || 0;
      nombreSocio = response.data.nombre;
      console.log(`Usuario socio: ${nombreSocio}, Descuento Dulcería: ${descuentoSocio}%`);

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
  banner.innerHTML = `<i class="fas fa-crown"></i> ¡Eres socio <strong>${nombreSocio}</strong>! Tienes <strong>${descuentoSocio}% de descuento</strong> en dulcería.`;
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

  const titulo = document.querySelector('h2');
  if (titulo && titulo.parentNode) {
    titulo.parentNode.insertBefore(banner, titulo.nextSibling);
  } else if (container) {
    container.insertBefore(banner, container.firstChild);
  }
}

async function cargarCombos() {
  const galeria = document.getElementById('galeria-productos');
  galeria.innerHTML = '<p class="cargando">Cargando combos...</p>';

  try {
    const response = await fetchFromApi('combo');

    if (response.success && response.data) {
      combos = response.data;
      renderizarCombos();
    } else {
      galeria.innerHTML = '<p class="error">No hay combos disponibles</p>';
    }
  } catch (error) {
    console.error('Error cargando combos:', error);
    galeria.innerHTML = '<p class="error">Error al cargar combos</p>';
  }
}

function renderizarCombos() {
  const galeria = document.getElementById('galeria-productos');
  galeria.innerHTML = '';

  if (combos.length === 0) {
    galeria.innerHTML = '<p class="vacio">No hay combos disponibles</p>';
    return;
  }

  combos.forEach(combo => {
    const precioOriginal = parseFloat(combo.precio);
    let precioFinal = precioOriginal;
    let htmlPrecio = `S/${precioOriginal.toFixed(2)}`;

    if (descuentoSocio > 0) {
      precioFinal = precioOriginal * (1 - descuentoSocio / 100);
      htmlPrecio = `
        <span class="precio-tachado" style="text-decoration: line-through; color: #999; font-size: 0.8em;">S/${precioOriginal.toFixed(2)}</span>
        <span class="precio-descuento" style="color: #e91e63; font-weight: bold;">S/${precioFinal.toFixed(2)}</span>
      `;
    }

    const card = document.createElement('div');
    card.className = 'tarjeta-producto';
    card.innerHTML = `
      <div class="imagen-producto">
        <img src="${combo.url_combo || '../assets/images/placeholder.svg'}" alt="${combo.nombre}" 
             onerror="this.src='../assets/images/placeholder.svg'">
      </div>
      <div class="info-producto">
        <h4>${combo.nombre}</h4>
        <p class="descripcion">${combo.descripcion || 'Delicioso combo'}</p>
        <div class="precio">${htmlPrecio}</div>
        <button class="btn-agregar" onclick="agregarCombo(${combo.id_combo}, '${combo.nombre}', ${precioFinal})">
          <i class="fas fa-shopping-cart"></i> Agregar
        </button>
      </div>
    `;
    galeria.appendChild(card);
  });
}

// ===== ORDEN =====

// ===== ORDEN =====

window.agregarCombo = function (id, nombre, precio) {
  // Límite: máximo combos = cantidad de entradas
  const cantidadEntradas = parseInt(sessionStorage.getItem('cantidadEntradas')) || 10;

  if (ordenDulceria.length >= cantidadEntradas) {
    mostrarNotificacion(`Máximo ${cantidadEntradas} combos (1 por entrada)`);
    return;
  }

  ordenDulceria.push({
    id: id,
    nombre: nombre,
    precio: parseFloat(precio)
  });

  totalDulceria += parseFloat(precio);
  actualizarOrden();
  mostrarNotificacion(`${nombre} agregado (${ordenDulceria.length}/${cantidadEntradas})`);
}

window.eliminarItem = function (id) {
  const index = ordenDulceria.findIndex(item => item.id == id);
  if (index > -1) {
    totalDulceria -= ordenDulceria[index].precio;
    ordenDulceria.splice(index, 1);
    actualizarOrden();
  }
}

function actualizarOrden() {
  const lista = document.getElementById('lista-orden');
  const mensaje = document.getElementById('mensaje-orden-vacia');
  const totalEntradas = parseFloat(sessionStorage.getItem('totalEntradas')) || 0;

  // Actualizar total
  const totalGeneral = totalEntradas + totalDulceria;
  document.getElementById('precio-total').textContent = `S/${totalGeneral.toFixed(2)}`;

  if (ordenDulceria.length === 0) {
    lista.style.display = 'none';
    mensaje.style.display = 'block';
    return;
  }

  lista.style.display = 'block';
  mensaje.style.display = 'none';
  lista.innerHTML = '';

  // Agrupar por ID
  const agrupado = {};
  ordenDulceria.forEach(item => {
    if (!agrupado[item.id]) {
      agrupado[item.id] = { ...item, cantidad: 0 };
    }
    agrupado[item.id].cantidad++;
  });

  Object.values(agrupado).forEach(item => {
    const div = document.createElement('div');
    div.className = 'item-orden';
    div.innerHTML = `
      <div class="detalle-item">
        <span class="cantidad-item">${item.cantidad} x</span>
        <span class="nombre-item">${item.nombre}</span>
      </div>
      <div class="precio-acciones">
        <span class="precio-item">S/${(item.precio * item.cantidad).toFixed(2)}</span>
        <button class="btn-eliminar-item" data-id="${item.id}">
          <i class="far fa-trash-alt"></i>
        </button>
      </div>
    `;

    div.querySelector('.btn-eliminar-item').addEventListener('click', () => eliminarItem(item.id));
    lista.appendChild(div);
  });
}

// ===== NAVEGACIÓN =====

function configurarPestanas() {
  // Por ahora solo una categoría (combos)
  // Se puede expandir para productos individuales
}

function configurarNavegacion() {
  document.getElementById('btn-cerrar')?.addEventListener('click', () => {
    if (confirm('¿Deseas salir de la compra?')) {
      detenerTimer();
      sessionStorage.clear();
      window.location.href = '../../index.html';
    }
  });

  document.getElementById('btn-continuar-dulceria')?.addEventListener('click', () => {
    // Guardar orden
    sessionStorage.setItem('ordenDulceria', JSON.stringify(ordenDulceria));
    sessionStorage.setItem('totalDulceria', totalDulceria.toFixed(2));
    sessionStorage.setItem('tiempoRestante', getTiempoRestante().toString());

    detenerTimer();
    window.location.href = 'pago.html';
  });
}

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

function mostrarNotificacion(mensaje) {
  const notif = document.createElement('div');
  notif.className = 'notificacion';
  notif.textContent = mensaje;
  notif.style.cssText = `
    position: fixed; bottom: 100px; right: 20px;
    background: #333; color: white; padding: 12px 24px;
    border-radius: 4px; z-index: 10000;
  `;
  document.body.appendChild(notif);
  setTimeout(() => notif.remove(), 2000);
}

// ===== INIT =====
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
