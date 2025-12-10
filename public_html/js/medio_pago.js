/**
 * Medio de Pago - Selección de método de pago y procesamiento
 * Actualizado para usar sessionStorage
 */

import { fetchFromApi } from "./data-manager.js";
import { iniciarTimer, detenerTimer, getTiempoRestante } from "./components/butaca/timer.js";

// ===== ESTADO =====
let datosPelicula = {};
let butacas = [];
let entradasDetalle = [];
let ordenDulceria = [];
let totalEntradas = 0;
let totalDulceria = 0;
let totalGeneral = 0;

// ===== INICIALIZACIÓN =====

async function init() {
  console.log('Inicializando medio de pago...');

  await cargarDatosFuncion();
  cargarDatosCompra();
  renderSidebar();
  configurarFormateoInputs();
  configurarMetodosPago();
  configurarTimer();
  configurarNavegacion();

  console.log('Medio de pago listo');
}

async function cargarDatosFuncion() {
  const idFuncion = sessionStorage.getItem('id_funcion');
  if (!idFuncion) return;

  try {
    const response = await fetchFromApi('funcion', 'id', idFuncion);
    const data = response.data;

    if (data) {
      datosPelicula = {
        titulo: data.pelicula_nombre || '',
        detalles: data.formatos || '',
        cine: data.sede_nombre || '',
        fecha: data.fecha || '',
        hora: data.hora || '',
        sala: `SALA ${data.numero_sala || ''}`,
        poster: data.pelicula_imagen
      };
    }
  } catch (error) {
    console.error('Error cargando función:', error);
  }
}

function cargarDatosCompra() {
  butacas = JSON.parse(sessionStorage.getItem('reservaButacas')) || [];
  entradasDetalle = JSON.parse(sessionStorage.getItem('entradasDetalle')) || [];
  ordenDulceria = JSON.parse(sessionStorage.getItem('ordenDulceria')) || [];

  totalEntradas = parseFloat(sessionStorage.getItem('totalEntradas')) || 0;
  totalDulceria = parseFloat(sessionStorage.getItem('totalDulceria')) || 0;
  totalGeneral = totalEntradas + totalDulceria;
}

function renderSidebar() {
  document.getElementById('movie-title').textContent = datosPelicula.titulo;
  document.getElementById('movie-details').textContent = datosPelicula.detalles;
  document.getElementById('cinema-name').textContent = datosPelicula.cine;
  document.getElementById('showtime-date').textContent = datosPelicula.fecha;
  document.getElementById('showtime-time').textContent = datosPelicula.hora;
  document.getElementById('room-name').textContent = datosPelicula.sala;

  const posterImg = document.getElementById('poster-img');
  if (posterImg && datosPelicula.poster) {
    posterImg.src = datosPelicula.poster;
  }

  const numButacas = sessionStorage.getItem('numeroButacasSeleccionadas') || '0';
  const cantEntradas = sessionStorage.getItem('cantidadEntradas') || '0';

  document.getElementById('numero-butacas').textContent = numButacas;
  document.getElementById('numero-entradas').textContent = cantEntradas;
  document.getElementById('numero-dulceria').textContent = ordenDulceria.length;
  document.getElementById('precio-total-lateral').textContent = `S/${totalGeneral.toFixed(2)}`;
}

// ===== FORMATEO DE INPUTS =====

function configurarFormateoInputs() {
  // Formatear número de tarjeta (espacios cada 4 dígitos)
  const inputTarjeta = document.getElementById('input-tarjeta');
  if (inputTarjeta) {
    inputTarjeta.addEventListener('input', (e) => {
      let valor = e.target.value.replace(/\s/g, '').replace(/\D/g, '');
      valor = valor.substring(0, 16);
      e.target.value = valor.replace(/(\d{4})(?=\d)/g, '$1 ');
    });
  }

  // Solo números en CVV
  const inputCVV = document.getElementById('input-cvv');
  if (inputCVV) {
    inputCVV.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/\D/g, '');
    });
  }

  // Solo números en documento
  const inputDocumento = document.getElementById('input-documento');
  if (inputDocumento) {
    inputDocumento.addEventListener('input', (e) => {
      e.target.value = e.target.value.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    });
  }

  // Solo números en celular
  const inputCelular = document.querySelector('input[placeholder="999 999 999"]');
  if (inputCelular) {
    inputCelular.addEventListener('input', (e) => {
      let valor = e.target.value.replace(/\D/g, '').substring(0, 9);
      if (valor.length > 3 && valor.length <= 6) {
        valor = valor.slice(0, 3) + ' ' + valor.slice(3);
      } else if (valor.length > 6) {
        valor = valor.slice(0, 3) + ' ' + valor.slice(3, 6) + ' ' + valor.slice(6);
      }
      e.target.value = valor;
    });
  }
}

// ===== MÉTODOS DE PAGO =====

function configurarMetodosPago() {
  // Selección de método (acordeón)
  window.seleccionarMetodo = function (metodo) {
    document.querySelectorAll('.opcion-pago').forEach(el => el.classList.remove('activa'));
    const selected = document.getElementById(`opcion-${metodo}`);
    if (selected) selected.classList.add('activa');
  };

  // Selección de billetera
  window.seleccionarBilletera = function (element, billetera) {
    document.querySelectorAll('.btn-billetera').forEach(el => el.classList.remove('seleccionada'));
    element.classList.add('seleccionada');
    element.closest('.contenido-pago').dataset.selectedWallet = billetera;
  };

  // Procesar pago - Llama al stored procedure via API
  window.procesarPago = async function (metodo) {
    const nombre = document.getElementById('input-nombre').value.trim();
    const email = document.getElementById('input-email').value.trim();
    const terminos = document.getElementById('check-terminos').checked;

    if (!nombre || !email) {
      alert('Por favor, complete sus datos personales.');
      return;
    }

    if (!terminos) {
      alert('Debe aceptar los Términos y Condiciones.');
      return;
    }

    if (metodo === 'billetera') {
      const walletContainer = document.getElementById('opcion-billetera').querySelector('.contenido-pago');
      if (!walletContainer.dataset.selectedWallet) {
        alert('Por favor, seleccione una billetera electrónica.');
        return;
      }
    }

    const btn = event.target;
    btn.textContent = 'Procesando...';
    btn.disabled = true;

    try {
      // Preparar datos para el stored procedure
      const asientosIds = JSON.parse(sessionStorage.getItem('asientosIds') || '[]');
      const idMetodo = metodo === 'tarjeta' ? 1 : (metodo === 'billetera' ? 4 : 1);

      // Aplanar lista de tipos de entrada para asignar a cada asiento
      // De: [{id: 1, cantidad: 2}, {id: 2, cantidad: 1}] -> [1, 1, 2]
      const tiposEntradaFlat = [];
      entradasDetalle.forEach(detalle => {
        for (let i = 0; i < detalle.cantidad; i++) {
          tiposEntradaFlat.push(detalle.id_tipo_entrada);
        }
      });

      // Formatear asientos para el procedure
      const asientosFormateados = asientosIds.map((id, index) => ({
        id_asiento: id,
        id_tipo_entrada: tiposEntradaFlat[index] || 1 // Default a General si falla
      }));

      // Formatear dulcería para el procedure
      const dulceriaFormateada = ordenDulceria.map(item => ({
        id_combo: item.id,
        precio: item.precio
      }));

      const compraData = {
        id_usuario: parseInt(sessionStorage.getItem('id_usuario')) || 1,
        id_metodo: idMetodo,
        id_funcion: parseInt(sessionStorage.getItem('id_funcion')),
        id_sala: parseInt(sessionStorage.getItem('id_sala')) || 1,
        id_sede: parseInt(sessionStorage.getItem('id_sede')) || 1,
        precio_total_boleto: totalEntradas,
        asientos: asientosFormateados,
        dulceria: dulceriaFormateada,
        precio_total_dulceria: totalDulceria
      };

      // Llamar a la API que ejecuta el stored procedure
      const response = await fetch('../api/compra_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(compraData)
      });

      const result = await response.json();

      if (result.success) {
        // Guardar datos del comprador
        sessionStorage.setItem('nombreComprador', nombre);
        sessionStorage.setItem('emailComprador', email);
        sessionStorage.setItem('metodoPago', metodo);
        sessionStorage.setItem('idCompra', result.id_compra);

        // Mostrar modal con ticket
        popularTicketPreview(nombre, result.id_compra);
        document.getElementById('modal-exito').style.display = 'flex';
        detenerTimer();
      } else {
        alert('Error al procesar la compra: ' + result.message);
        btn.textContent = 'Pagar';
        btn.disabled = false;
      }

    } catch (error) {
      console.error('Error:', error);
      alert('Error al conectar con el servidor');
      btn.textContent = 'Pagar';
      btn.disabled = false;
    }
  };
}

function popularTicketPreview(userName, idCompra) {
  const ticketId = idCompra ? `CPT${idCompra}` : 'CPT' + Math.floor(Math.random() * 100000);
  const butacasStr = Array.isArray(butacas) ? butacas.join(', ') : butacas;

  document.getElementById('pdf-ticket-id').textContent = ticketId;
  document.getElementById('pdf-movie-title').textContent = datosPelicula.titulo;
  document.getElementById('pdf-user-name').textContent = userName;
  document.getElementById('pdf-cinema').textContent = datosPelicula.cine;
  document.getElementById('pdf-date').textContent = datosPelicula.fecha;
  document.getElementById('pdf-time').textContent = datosPelicula.hora;
  document.getElementById('pdf-room').textContent = datosPelicula.sala;
  document.getElementById('pdf-seats').textContent = butacasStr;
  document.getElementById('pdf-total').textContent = `S/${totalGeneral.toFixed(2)}`;

  // Lista de items
  const itemsContainer = document.getElementById('pdf-items-list');
  itemsContainer.innerHTML = '';

  // Entradas
  entradasDetalle.forEach(entrada => {
    const row = document.createElement('div');
    row.className = 'detail-row';
    row.innerHTML = `
      <span>${entrada.tipo}</span>
      <span>Cant: ${entrada.cantidad}</span>
      <span>S/${(entrada.cantidad * entrada.precio).toFixed(2)}</span>
    `;
    itemsContainer.appendChild(row);
  });

  // Dulcería
  if (ordenDulceria.length > 0) {
    const agrupado = {};
    ordenDulceria.forEach(item => {
      if (!agrupado[item.id]) agrupado[item.id] = { ...item, cantidad: 0 };
      agrupado[item.id].cantidad++;
    });

    Object.values(agrupado).forEach(item => {
      const row = document.createElement('div');
      row.className = 'detail-row';
      row.innerHTML = `
        <span>${item.nombre}</span>
        <span>Cant: ${item.cantidad}</span>
        <span>S/${(item.precio * item.cantidad).toFixed(2)}</span>
      `;
      itemsContainer.appendChild(row);
    });
  }
}

// ===== MODAL =====

window.cerrarModal = function () {
  document.getElementById('modal-exito').style.display = 'none';
};

window.finalizarCompra = function () {
  sessionStorage.clear();
  window.location.href = '../../index.html';
};

window.descargarPDF = function () {
  const element = document.getElementById('ticket-template');

  const container = document.createElement('div');
  container.style.cssText = 'position:absolute;left:-9999px;top:0;width:600px;';
  document.body.appendChild(container);

  const clone = element.cloneNode(true);
  clone.style.cssText = 'display:block;width:100%;height:auto;overflow:visible;box-shadow:none;border:none;';
  container.appendChild(clone);

  const opt = {
    margin: 10,
    filename: 'ticket-cineplanet.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2, useCORS: true, scrollY: 0 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
  };

  const btn = document.querySelector('.btn-descargar');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
  btn.disabled = true;

  html2pdf().set(opt).from(clone).save().then(() => {
    btn.innerHTML = originalText;
    btn.disabled = false;
    document.body.removeChild(container);
  }).catch(err => {
    console.error(err);
    btn.innerHTML = originalText;
    btn.disabled = false;
    if (document.body.contains(container)) document.body.removeChild(container);
  });
};

// ===== TIMER =====

function configurarTimer() {
  const tiempoGuardado = sessionStorage.getItem('tiempoRestante');
  const minutos = tiempoGuardado ? Math.ceil(parseInt(tiempoGuardado) / 60) : 3;

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
  document.getElementById('btn-cerrar')?.addEventListener('click', () => {
    if (confirm('¿Deseas salir de la compra?')) {
      detenerTimer();
      sessionStorage.clear();
      window.location.href = '../../index.html';
    }
  });

  document.getElementById('btn-usuario')?.addEventListener('click', () => {
    window.location.href = 'login.html';
  });
}

// ===== INIT =====
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
