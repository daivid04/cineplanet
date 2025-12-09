
import { fetchFromApi } from "./data-manager.js";
import { loadMovieSelect } from "./components/select/seleccion-template.js";

// ===== ELEMENTOS DEL DOM =====
const tituloPelicula = document.getElementById('titulo-pelicula');
const generoPelicula = document.getElementById('genero-pelicula');
const duracionPelicula = document.getElementById('duracion-pelicula');
const clasificacionPelicula = document.getElementById('clasificacion-pelicula');
const posterPelicula = document.getElementById('poster-pelicula');
const botonComprar = document.getElementById('boton-comprar');
const seccionCompra = document.getElementById('seccion-compra');
const contenedorCines = document.getElementById('contenedor-cines');

// Elementos de la barra de filtros
const btnCiudad = document.getElementById('btn-ciudad');
const btnCine = document.getElementById('btn-cine');
const btnFecha = document.getElementById('btn-fecha');
const dropdownCiudad = document.getElementById('dropdown-ciudad');
const dropdownCine = document.getElementById('dropdown-cine');
const dropdownFecha = document.getElementById('dropdown-fecha');
const ciudadSeleccionadaTexto = document.getElementById('ciudad-seleccionada');
const cineSeleccionadoTexto = document.getElementById('cine-seleccionado');
const fechaSeleccionadaTexto = document.getElementById('fecha-seleccionada');

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
        fechaSeleccionadaTexto.textContent = state.dates[0]?.nombre || 'Hoy';
        btnCine.disabled = false;
        btnFecha.disabled = false;
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
    const fechaObj = state.dates.find(f => f.id == urlFecha);
    if (fechaObj) {
        fechaSeleccionadaTexto.textContent = fechaObj.nombre;
    }
    btnFecha.disabled = false;

    if (urlCity) {
        const ciudad = state.allCities.find(c => c.id_ciudad == urlCity);
        if (ciudad) {
            state.cityId = urlCity;
            ciudadSeleccionadaTexto.textContent = ciudad.nombre;
            renderCines(urlCity);
            btnCine.disabled = false;
        }
        
        if (urlCine) {
            const cineValido = state.allCinemas.find(c => c.id_sede == urlCine);
            if (cineValido) {
                state.cinemaId = urlCine;
                cineSeleccionadoTexto.textContent = cineValido.nombre;
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
    const html = state.allCities.map(ciudad => `
        <div class="filtro-opcion" data-ciudad="${ciudad.id_ciudad}">
            ${ciudad.nombre}
        </div>
    `).join('');

    dropdownCiudad.innerHTML = html;

    dropdownCiudad.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarCiudad(opcion.dataset.ciudad);
        });
    });
}

function renderCines(ciudadId) {
    const cinesFiltrados = state.allCinemas.filter(c => c.id_ciudad == ciudadId);
    
    const html = cinesFiltrados.map(cine => `
        <div class="filtro-opcion" data-cine="${cine.id_sede}">
            ${cine.nombre}
        </div>
    `).join('');

    const pelicula = peliculasData.find(p => p.id === peliculaId) || peliculasData[0];

    dropdownCine.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarCine(opcion.dataset.cine);
        });
    });
}

function renderFechas() {
    const html = state.dates.map(fecha => `
        <div class="filtro-opcion" data-fecha="${fecha.id}">
            ${fecha.nombre}
        </div>
    `).join('');

    cinesFiltrados.forEach(cine => {
        const cineItem = document.createElement('div');
        cineItem.className = 'cine-item'; // Por defecto cerrado

        let gruposHtml = '';
        cine.grupos.forEach(grupo => {
            let botonesHtml = '';
            grupo.horarios.forEach(hora => {
                botonesHtml += `
                    <button class="btn-horario" onclick="seleccionarHorario('${hora}', '${cine.nombre}', '${grupo.formato}', '${grupo.tipo}')">
                        ${hora} <i class="fas fa-couch"></i>
                    </button>
                `;
            });

    dropdownFecha.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarFecha(opcion.dataset.fecha);
        });
    });
}

// ===== LÓGICA DE SELECCIÓN =====

function seleccionarCiudad(ciudadId) {
    const ciudad = state.allCities.find(c => c.id_ciudad == ciudadId);
    if (!ciudad) return;

    state.cityId = ciudadId;
    state.cinemaId = null;

    ciudadSeleccionadaTexto.textContent = ciudad.nombre;
    cineSeleccionadoTexto.textContent = 'Todos los cines';

    cerrarTodosDropdowns();

    btnCine.disabled = false;
    btnFecha.disabled = false;

    renderCines(ciudadId);
    
    // Al seleccionar ciudad, cargar todas las funciones de esa ciudad
    cargarTodasLasFunciones();
}

function seleccionarCine(cineId) {
    const cine = state.allCinemas.find(c => c.id_sede == cineId);
    if (!cine) return;

    state.cinemaId = cineId;

    // Si no hay ciudad seleccionada, auto-seleccionar la ciudad del cine
    if (!state.cityId) {
        state.cityId = cine.id_ciudad;
        const ciudad = state.allCities.find(c => c.id_ciudad == cine.id_ciudad);
        if (ciudad) {
            ciudadSeleccionadaTexto.textContent = ciudad.nombre;
            renderCines(cine.id_ciudad);
        }
    }

    cineSeleccionadoTexto.textContent = cine.nombre;

    cerrarTodosDropdowns();

    btnFecha.disabled = false;
    
    // Al seleccionar cine específico, cargar solo sus funciones
    cargarFunciones();
}

function seleccionarFecha(fechaId) {
    const fecha = state.dates.find(f => f.id == fechaId);
    if (!fecha) return;

    state.date = fechaId;
    fechaSeleccionadaTexto.textContent = fecha.nombre;

    cerrarTodosDropdowns();
    
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

    const cineNombre = cineSeleccionadoTexto.textContent;
    
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
            
            // Redirigir a butacas con el ID de la función
            window.location.href = `butacas.html?id_funcion=${idFuncion}`;
        });
    });
}

// ===== UTILIDADES UI =====

function toggleDropdown(dropdown, button) {
    const estaAbierto = dropdown.classList.contains('abierto');
    cerrarTodosDropdowns();
    if (!estaAbierto) {
        dropdown.classList.add('abierto');
        button.classList.add('activo');
    }
}

function cerrarTodosDropdowns() {
    document.querySelectorAll('.filtro-dropdown').forEach(d => d.classList.remove('abierto'));
    document.querySelectorAll('.filtro-header').forEach(b => b.classList.remove('activo'));
}

function inicializarEventos() {
    if (botonComprar) {
        botonComprar.addEventListener('click', () => {
            seccionCompra.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    btnCiudad.addEventListener('click', () => toggleDropdown(dropdownCiudad, btnCiudad));
    btnCine.addEventListener('click', () => {
        if (!btnCine.disabled) toggleDropdown(dropdownCine, btnCine);
    });
    btnFecha.addEventListener('click', () => {
        if (!btnFecha.disabled) toggleDropdown(dropdownFecha, btnFecha);
    });

function inicializarEventos() {
    selectCiudad.addEventListener('change', () => {
        selectCine.value = ""; // Resetear cine al cambiar ciudad
        renderizarHorarios();
    });
    selectCine.addEventListener('change', renderizarHorarios);
    selectFecha.addEventListener('change', renderizarHorarios);

// Arrancar
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
    inicializarAplicacion();
}
