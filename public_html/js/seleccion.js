
import { fetchFromApi } from "./data-manager.js";
import { loadMovieSelect } from "./components/select/seleccion-template.js";

// ===== ELEMENTOS DEL DOM =====
const tituloPelicula = document.getElementById('titulo-pelicula');
const generoPelicula = document.getElementById('genero-pelicula');
const duracionPelicula = document.getElementById('duracion-pelicula');
const clasificacionPelicula = document.getElementById('clasificacion-pelicula');
const posterPelicula = document.getElementById('poster-pelicula');
const botonComprar = document.getElementById('boton-comprar');
const contenedorCines = document.getElementById('lista-cines');

// Elementos de los filtros (selects nativos)
const selectCiudad = document.getElementById('filtro-ciudad');
const selectCine = document.getElementById('filtro-cine');
const selectFecha = document.getElementById('filtro-fecha');

// ===== ESTADO =====
const state = {
  movieId: null,
  cityId: null,
  cinemaId: null,
  date: null,
  allCities: [],
  allCinemas: [],
  dates: []
};

// ===== INICIALIZACIÓN =====
async function inicializarAplicacion() {
  console.log('Inicializando página de selección...');

  // 1. Obtener ID de película de la URL
  const urlParams = new URLSearchParams(window.location.search);
  state.movieId = urlParams.get('id');

  if (state.movieId) {
    sessionStorage.setItem('movieId', state.movieId);
  } else {
    state.movieId = sessionStorage.getItem('movieId');
  }

  if (!state.movieId) {
    alert("No se ha seleccionado una película");
    window.location.href = "index.html";
    return;
  }

  // 2. Cargar datos de la película (Header)
  await loadMovieSelect();

  // 3. Cargar datos maestros (Ciudades y Sedes)
  await cargarDatosMaestros();

  // 4. Generar fechas (Próximos 7 días)
  generarFechas();

  // 5. Configurar eventos
  inicializarEventos();

  // 6. Procesar filtros de URL o cargar todo
  const urlCity = urlParams.get('ciudad');
  const urlCine = urlParams.get('cine');
  const urlFecha = urlParams.get('fecha');

  if (urlCity || urlCine || urlFecha) {
    await procesarFiltrosURL(urlParams);
  } else {
    // Sin parámetros: seleccionar fecha de hoy y cargar todas las sedes
    state.date = state.dates[0]?.id; // Hoy
    if (selectFecha) selectFecha.value = state.date;
    await cargarTodasLasFunciones();
  }
}

async function cargarDatosMaestros() {
  try {
    const [citiesRes, cinemasRes] = await Promise.all([
      fetchFromApi('ciudad'),
      fetchFromApi('sede')
    ]);

    state.allCities = citiesRes.data || citiesRes || [];
    state.allCinemas = cinemasRes.data || cinemasRes || [];

    renderCiudades();
  } catch (error) {
    console.error("Error cargando datos maestros:", error);
  }
}

function generarFechas() {
  const diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  state.dates = [];
  const hoy = new Date();

  for (let i = 0; i < 7; i++) {
    const fecha = new Date(hoy);
    fecha.setDate(hoy.getDate() + i);

    const diaStr = i === 0 ? 'Hoy' : (i === 1 ? 'Mañana' : diasSemana[fecha.getDay()]);
    const fechaStr = `${diaStr} ${fecha.getDate()} ${meses[fecha.getMonth()]}`;
    const valor = fecha.toISOString().split('T')[0]; // YYYY-MM-DD

    state.dates.push({
      id: valor,
      nombre: fechaStr
    });
  }

  renderFechas();
}

async function procesarFiltrosURL(urlParams) {
  const urlCity = urlParams.get('ciudad');
  const urlCine = urlParams.get('cine');
  const urlFecha = urlParams.get('fecha') || state.dates[0]?.id;

  // Establecer fecha (default: hoy)
  state.date = urlFecha;
  if (selectFecha) selectFecha.value = urlFecha;

  if (urlCity) {
    const ciudad = state.allCities.find(c => c.id_ciudad == urlCity);
    if (ciudad) {
      state.cityId = urlCity;
      if (selectCiudad) selectCiudad.value = urlCity;
      renderCines(urlCity);
    }

    if (urlCine) {
      const cineValido = state.allCinemas.find(c => c.id_sede == urlCine);
      if (cineValido) {
        state.cinemaId = urlCine;
        if (selectCine) selectCine.value = urlCine;
        await cargarFunciones();
        return;
      }
    }
  }

  // Si no hay cine específico, cargar todas las funciones
  await cargarTodasLasFunciones();
}

// ===== RENDERIZADO DE FILTROS =====

function renderCiudades() {
  if (!selectCiudad) return;

  const html = '<option value="">Selecciona ciudad</option>' +
    state.allCities.map(ciudad => `
            <option value="${ciudad.id_ciudad}">${ciudad.nombre}</option>
        `).join('');

  selectCiudad.innerHTML = html;
}

function renderCines(ciudadId) {
  if (!selectCine) return;

  const cinesFiltrados = ciudadId
    ? state.allCinemas.filter(c => c.id_ciudad == ciudadId)
    : state.allCinemas;

  const html = '<option value="">Elige tu Cineplanet</option>' +
    cinesFiltrados.map(cine => `
            <option value="${cine.id_sede}">${cine.nombre}</option>
        `).join('');

  selectCine.innerHTML = html;
}

function renderFechas() {
  if (!selectFecha) return;

  const html = state.dates.map(fecha => `
        <option value="${fecha.id}">${fecha.nombre}</option>
    `).join('');

  selectFecha.innerHTML = html;
}

// ===== LÓGICA DE SELECCIÓN =====

function seleccionarCiudad(ciudadId) {
  state.cityId = ciudadId;
  state.cinemaId = null;

  if (selectCine) selectCine.value = '';

  renderCines(ciudadId);

  // Al seleccionar ciudad, cargar todas las funciones de esa ciudad
  cargarTodasLasFunciones();
}

function seleccionarCine(cineId) {
  if (!cineId) {
    state.cinemaId = null;
    cargarTodasLasFunciones();
    return;
  }

  const cine = state.allCinemas.find(c => c.id_sede == cineId);
  if (!cine) return;

  state.cinemaId = cineId;

  // Si no hay ciudad seleccionada, auto-seleccionar la ciudad del cine
  if (!state.cityId) {
    state.cityId = cine.id_ciudad;
    if (selectCiudad) selectCiudad.value = cine.id_ciudad;
    renderCines(cine.id_ciudad);
    if (selectCine) selectCine.value = cineId;
  }

  // Al seleccionar cine específico, cargar solo sus funciones
  cargarFunciones();
}

function seleccionarFecha(fechaId) {
  state.date = fechaId;

  // Si hay cine seleccionado, cargar solo ese cine; si no, cargar todas las sedes
  if (state.cinemaId) {
    cargarFunciones();
  } else {
    cargarTodasLasFunciones();
  }
}

// ===== CARGA Y RENDERIZADO DE FUNCIONES =====

async function cargarTodasLasFunciones() {
  if (!state.movieId || !state.date) return;

  contenedorCines.innerHTML = '<p class="mensaje-seleccionar">Cargando funciones...</p>';

  try {
    const params = {
      accion: 'con_filtros',
      id_pelicula: state.movieId,
      fecha: state.date
    };

    // Agregar filtro de ciudad si está seleccionada
    if (state.cityId) {
      params.id_ciudad = state.cityId;
    }

    const response = await fetchFromApi('funcion', params);
    const funciones = response.data || response || [];
    renderizarTodasLasFunciones(funciones);

  } catch (error) {
    console.error("Error cargando funciones:", error);
    contenedorCines.innerHTML = '<p class="mensaje-seleccionar">Error al cargar funciones. Intenta nuevamente.</p>';
  }
}

async function cargarFunciones() {
  if (!state.movieId || !state.cinemaId || !state.date) return;

  contenedorCines.innerHTML = '<p class="mensaje-seleccionar">Cargando funciones...</p>';

  try {
    const response = await fetchFromApi('funcion', {
      accion: 'con_filtros',
      id_pelicula: state.movieId,
      id_sede: state.cinemaId,
      fecha: state.date
    });

    const funciones = response.data || response || [];
    renderizarFuncionesUnaSede(funciones);

  } catch (error) {
    console.error("Error cargando funciones:", error);
    contenedorCines.innerHTML = '<p class="mensaje-seleccionar">Error al cargar funciones. Intenta nuevamente.</p>';
  }
}

/**
 * Renderiza funciones agrupadas por SEDE (cuando no se ha seleccionado un cine específico)
 */
function renderizarTodasLasFunciones(funciones) {
  if (!Array.isArray(funciones) || funciones.length === 0) {
    contenedorCines.innerHTML = '<p class="mensaje-seleccionar">No hay funciones disponibles para esta fecha.</p>';
    return;
  }

  // Agrupar por sede
  const sedesMap = {};

  funciones.forEach(func => {
    const sedeId = func.id_sede;
    const sedeNombre = func.sede_nombre || 'Cine';

    if (!sedesMap[sedeId]) {
      sedesMap[sedeId] = {
        nombre: sedeNombre,
        horarios: []
      };
    }

    const hora = func.hora ? func.hora.substring(0, 5) : '00:00';
    sedesMap[sedeId].horarios.push({
      id: func.id_funcion,
      hora: hora
    });
  });

  // Ordenar horarios dentro de cada sede
  Object.values(sedesMap).forEach(sede => {
    sede.horarios.sort((a, b) => a.hora.localeCompare(b.hora));
  });

  // Generar HTML para todas las sedes
  const sedesHTML = Object.entries(sedesMap).map(([sedeId, sede]) => {
    const horariosHTML = sede.horarios.map(h => `
            <button class="horario-btn" 
                    data-cine="${sede.nombre}" 
                    data-hora="${h.hora}" 
                    data-id-funcion="${h.id}">
                <span class="horario-hora">${h.hora}</span>
            </button>
        `).join('');

    return `
            <div class="cine-card">
                <button class="cine-header" data-sede-id="${sedeId}">
                    <h3 class="cine-nombre">${sede.nombre}</h3>
                    <svg class="icono-flecha" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 10l-4 4-4-4" />
                    </svg>
                </button>
                <div class="cine-contenido">
                    <div class="funcion-grupo">
                        <div class="info-formato">
                            <span class="badge-formato">2D</span>
                            <span class="texto-formato">DOBLADA</span>
                        </div>
                        <div class="grid-horarios">
                            ${horariosHTML}
                        </div>
                    </div>
                </div>
            </div>
        `;
  }).join('');

  contenedorCines.innerHTML = sedesHTML;
  agregarEventosCineHeaders();
  agregarEventosHorarios();
}

/**
 * Renderiza funciones de UNA SOLA SEDE (cuando ya se seleccionó un cine)
 */
function renderizarFuncionesUnaSede(funciones) {
  if (!Array.isArray(funciones) || funciones.length === 0) {
    contenedorCines.innerHTML = '<p class="mensaje-seleccionar">No hay funciones disponibles para esta fecha.</p>';
    return;
  }

  // Ordenar horarios
  const horarios = funciones.map(func => ({
    id: func.id_funcion,
    hora: func.hora ? func.hora.substring(0, 5) : '00:00'
  })).sort((a, b) => a.hora.localeCompare(b.hora));

  const cineNombre = selectCine?.options[selectCine.selectedIndex]?.text || 'Cine';

  const horariosHTML = horarios.map(h => `
        <button class="horario-btn" 
                data-cine="${cineNombre}" 
                data-hora="${h.hora}" 
                data-id-funcion="${h.id}">
            <span class="horario-hora">${h.hora}</span>
        </button>
    `).join('');

  const cardHTML = `
        <div class="cine-card">
            <button class="cine-header">
                <h3 class="cine-nombre">${cineNombre}</h3>
                <svg class="icono-flecha" viewBox="0 0 24 24" fill="currentColor" style="transform: rotate(180deg);">
                    <path d="M16 10l-4 4-4-4" />
                </svg>
            </button>
            <div class="cine-contenido expandido">
                <div class="funcion-grupo">
                    <div class="info-formato">
                        <span class="badge-formato">2D</span>
                        <span class="texto-formato">DOBLADA</span>
                    </div>
                    <div class="grid-horarios">
                        ${horariosHTML}
                    </div>
                </div>
            </div>
        </div>
    `;

  contenedorCines.innerHTML = cardHTML;
  agregarEventosHorarios();
}

function agregarEventosCineHeaders() {
  document.querySelectorAll('.cine-header').forEach(header => {
    header.addEventListener('click', () => {
      const contenido = header.nextElementSibling;
      const icono = header.querySelector('.icono-flecha');
      contenido.classList.toggle('expandido');
      icono.style.transform = contenido.classList.contains('expandido') ? 'rotate(180deg)' : 'rotate(0deg)';
    });
  });
}

function agregarEventosHorarios() {
  const botonesHorario = document.querySelectorAll('.horario-btn');
  botonesHorario.forEach(boton => {
    boton.addEventListener('click', (e) => {
      e.stopPropagation();
      const idFuncion = boton.dataset.idFuncion;
      sessionStorage.setItem('id_funcion', idFuncion);
      // Redirigir a butacas con el ID de la función
      window.location.href = `butacas.html?id_funcion=${idFuncion}`;
    });
  });
}

// ===== UTILIDADES UI =====

function inicializarEventos() {
  if (botonComprar) {
    botonComprar.addEventListener('click', () => {
      const seccionHorarios = document.querySelector('.seccion-horarios');
      if (seccionHorarios) {
        seccionHorarios.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  }

  // Eventos para los selects
  if (selectCiudad) {
    selectCiudad.addEventListener('change', (e) => {
      seleccionarCiudad(e.target.value);
    });
  }

  if (selectCine) {
    selectCine.addEventListener('change', (e) => {
      seleccionarCine(e.target.value);
    });
  }

  if (selectFecha) {
    selectFecha.addEventListener('change', (e) => {
      seleccionarFecha(e.target.value);
    });
  }
}

// Arrancar
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
  inicializarAplicacion();
}
