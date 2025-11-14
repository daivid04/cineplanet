// ===== ELEMENTOS DEL DOM =====
const botonComprar = document.getElementById('boton-comprar');
const seccionCompra = document.getElementById('seccion-compra');
const contenedorCines = document.getElementById('contenedor-cines');
const botonesIdioma = document.querySelectorAll('.opcion-idioma');

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

// ===== ESTADO DE FILTROS =====
let filtrosSeleccionados = {
    ciudad: null,
    cine: null,
    fecha: null
};

// ===== DATOS ESTÁTICOS (simulados) =====
const ciudadesDisponibles = [
    { id: 'arequipa', nombre: 'Arequipa' },
    { id: 'tacna', nombre: 'Tacna' },
    { id: 'lima', nombre: 'Lima' },
    { id: 'cusco', nombre: 'Cusco' }
];

const cinesPorCiudad = {
    'arequipa': [
        { id: 'alcazar', nombre: 'CP Alcazar' },
        { id: 'mall-plaza', nombre: 'CP Arequipa Mall Plaza' },
        { id: 'paseo-central', nombre: 'CP Arequipa Paseo Central' },
        { id: 'real-plaza', nombre: 'CP Arequipa Real Plaza' }
    ],
    'tacna': [
        { id: 'tacna', nombre: 'CP Tacna' }
    ],
    'lima': [
        { id: 'san-miguel', nombre: 'CP San Miguel' },
        { id: 'jockey-plaza', nombre: 'CP Jockey Plaza' }
    ],
    'cusco': [
        { id: 'cusco', nombre: 'CP Cusco' }
    ]
};

const fechasDisponibles = [
    { id: 'hoy', nombre: 'Hoy Jueves 13' },
    { id: 'manana', nombre: 'Mañana Viernes 14' },
    { id: 'sabado', nombre: 'Sábado 15' },
    { id: 'domingo', nombre: 'Domingo 16' }
];

const funcionesPorCine = {
    'alcazar': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['14:30', '17:00', '20:30']
        },
        {
            formato: '3D',
            tipo: 'DOBLADA',
            horarios: ['15:30', '18:30', '21:30']
        }
    ],
    'mall-plaza': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['15:00', '18:30', '21:00']
        }
    ],
    'paseo-central': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['16:00', '19:00']
        },
        {
            formato: 'PRIME',
            tipo: 'DOBLADA',
            horarios: ['20:00']
        }
    ],
    'real-plaza': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['14:00', '17:30', '20:00']
        },
        {
            formato: '3D',
            tipo: 'DOBLADA',
            horarios: ['16:00', '19:30', '22:00']
        }
    ],
    'tacna': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['17:10', '18:00', '19:20', '20:10', '21:30', '22:20']
        },
        {
            formato: '2D',
            tipo: 'DOBLADA',
            horarios: ['14:00', '16:30', '19:00']
        }
    ],
    'san-miguel': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['13:00', '15:30', '18:00', '20:30']
        }
    ],
    'jockey-plaza': [
        {
            formato: 'PRIME',
            tipo: 'REGULAR',
            horarios: ['14:00', '17:00', '20:00']
        }
    ],
    'cusco': [
        {
            formato: '2D',
            tipo: 'REGULAR',
            horarios: ['15:00', '18:00', '21:00']
        }
    ]
};

// ===== FUNCIONES PRINCIPALES =====

/**
 * Scroll suave al botón comprar
 */
function scrollACompra() {
    seccionCompra.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

/**
 * Cambia el idioma seleccionado
 */
function cambiarIdioma(boton) {
    botonesIdioma.forEach(btn => btn.classList.remove('activa'));
    boton.classList.add('activa');

    const idioma = boton.dataset.idioma;
    console.log('Idioma seleccionado:', idioma);
}

/**
 * Inicializa las opciones de ciudad
 */
function cargarCiudades() {
    const html = ciudadesDisponibles.map(ciudad => `
        <div class="filtro-opcion" data-ciudad="${ciudad.id}">
            ${ciudad.nombre}
        </div>
    `).join('');

    dropdownCiudad.innerHTML = html;

    // Agregar eventos a las opciones
    dropdownCiudad.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarCiudad(opcion.dataset.ciudad);
        });
    });
}

/**
 * Selecciona una ciudad y actualiza los filtros dependientes
 */
function seleccionarCiudad(ciudadId) {
    const ciudad = ciudadesDisponibles.find(c => c.id === ciudadId);

    filtrosSeleccionados.ciudad = ciudadId;
    filtrosSeleccionados.cine = null;
    filtrosSeleccionados.fecha = null;

    // Actualizar texto
    ciudadSeleccionadaTexto.textContent = ciudad.nombre;
    cineSeleccionadoTexto.textContent = 'Elige tu Cineplanet';
    fechaSeleccionadaTexto.textContent = 'Hoy Jueves 13';

    // Cerrar dropdown
    cerrarTodosDropdowns();

    // Habilitar siguiente filtro
    btnCine.disabled = false;
    btnFecha.disabled = true;

    // Cargar cines de la ciudad
    cargarCines(ciudadId);

    // Limpiar resultados
    contenedorCines.innerHTML = '<p class=\"mensaje-seleccionar\">Selecciona un cine para ver las funciones disponibles</p>';
}

/**
 * Carga los cines de una ciudad
 */
function cargarCines(ciudadId) {
    const cines = cinesPorCiudad[ciudadId] || [];

    const html = cines.map(cine => `
        <div class=\"filtro-opcion\" data-cine=\"${cine.id}\">
            ${cine.nombre}
        </div>
    `).join('');

    dropdownCine.innerHTML = html;

    // Agregar eventos a las opciones
    dropdownCine.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarCine(opcion.dataset.cine);
        });
    });
}

/**
 * Selecciona un cine y actualiza los filtros dependientes
 */
function seleccionarCine(cineId) {
    const cines = cinesPorCiudad[filtrosSeleccionados.ciudad] || [];
    const cine = cines.find(c => c.id === cineId);

    filtrosSeleccionados.cine = cineId;
    filtrosSeleccionados.fecha = null;

    // Actualizar texto
    cineSeleccionadoTexto.textContent = cine.nombre;
    fechaSeleccionadaTexto.textContent = 'Hoy Jueves 13';

    // Cerrar dropdown
    cerrarTodosDropdowns();

    // Habilitar siguiente filtro
    btnFecha.disabled = false;

    // Cargar fechas
    cargarFechas();

    // Limpiar resultados
    contenedorCines.innerHTML = '<p class=\"mensaje-seleccionar\">Selecciona una fecha para ver las funciones disponibles</p>';
}

/**
 * Carga las fechas disponibles
 */
function cargarFechas() {
    const html = fechasDisponibles.map(fecha => `
        <div class=\"filtro-opcion\" data-fecha=\"${fecha.id}\">
            ${fecha.nombre}
        </div>
    `).join('');

    dropdownFecha.innerHTML = html;

    // Agregar eventos a las opciones
    dropdownFecha.querySelectorAll('.filtro-opcion').forEach(opcion => {
        opcion.addEventListener('click', () => {
            seleccionarFecha(opcion.dataset.fecha);
        });
    });
}

/**
 * Selecciona una fecha y muestra los resultados
 */
function seleccionarFecha(fechaId) {
    const fecha = fechasDisponibles.find(f => f.id === fechaId);

    filtrosSeleccionados.fecha = fechaId;

    // Actualizar texto
    fechaSeleccionadaTexto.textContent = fecha.nombre;

    // Cerrar dropdown
    cerrarTodosDropdowns();

    // Mostrar resultados
    renderizarCines();
}

/**
 * Alterna la visibilidad de un dropdown
 */
function toggleDropdown(dropdown, button) {
    const estaAbierto = dropdown.classList.contains('abierto');

    // Cerrar todos los dropdowns
    cerrarTodosDropdowns();

    // Si no estaba abierto, abrirlo
    if (!estaAbierto) {
        dropdown.classList.add('abierto');
        button.classList.add('activo');
    }
}

/**
 * Cierra todos los dropdowns
 */
function cerrarTodosDropdowns() {
    document.querySelectorAll('.filtro-dropdown').forEach(d => d.classList.remove('abierto'));
    document.querySelectorAll('.filtro-header').forEach(b => b.classList.remove('activo'));
}

/**
 * Crea el HTML de una tarjeta de cine con funciones
 */
function crearTarjetaCine(cine, funciones) {
    const funcionesHTML = funciones.map(funcion => `
        <div class="funcion-grupo">
            <div class="info-formato">
                <span class="badge-formato">${funcion.formato}</span>
                <span class="texto-formato">${funcion.tipo}</span>
            </div>
            <div class="grid-horarios">
                ${funcion.horarios.map(hora => `
                    <button class="horario-btn" data-cine="${cine.nombre}" data-hora="${hora}" data-formato="${funcion.formato}">
                        <span class="horario-hora">${hora}</span>
                    </button>
                `).join('')}
            </div>
        </div>
    `).join('');

    return `
        <div class="cine-card">
            <button class="cine-header" data-cine-nombre="${cine.nombre}">
                <h3 class="cine-nombre">${cine.nombre}</h3>
                <svg class="icono-flecha" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 10l-4 4-4-4" />
                </svg>
            </button>
            <div class="cine-contenido">
                ${funcionesHTML}
            </div>
        </div>
    `;
}

function renderizarCines() {
    if (!filtrosSeleccionados.ciudad || !filtrosSeleccionados.cine || !filtrosSeleccionados.fecha) {
        contenedorCines.innerHTML = '<p class="mensaje-seleccionar">Selecciona ciudad, cine y fecha para ver las funciones disponibles</p>';
        return;
    }

    const cines = cinesPorCiudad[filtrosSeleccionados.ciudad] || [];
    const cineSeleccionado = cines.find(c => c.id === filtrosSeleccionados.cine);

    if (!cineSeleccionado) {
        contenedorCines.innerHTML = '<p class="mensaje-seleccionar">No se encontró el cine seleccionado</p>';
        return;
    }

    const funciones = funcionesPorCine[filtrosSeleccionados.cine] || [];

    if (funciones.length === 0) {
        contenedorCines.innerHTML = '<p class="mensaje-seleccionar">No hay funciones disponibles para este cine</p>';
        return;
    }

    const html = crearTarjetaCine(cineSeleccionado, funciones);
    contenedorCines.innerHTML = html;
    agregarEventosCineHeaders();
    agregarEventosHorarios();
}

function agregarEventosCineHeaders() {
    const headers = document.querySelectorAll('.cine-header');
    headers.forEach(header => {
        header.addEventListener('click', () => {
            const contenido = header.nextElementSibling;
            contenido.classList.toggle('expandido');
            const icono = header.querySelector('.icono-flecha');
            if (contenido.classList.contains('expandido')) {
                icono.style.transform = 'rotate(180deg)';
            } else {
                icono.style.transform = 'rotate(0deg)';
            }
        });
    });
}

function agregarEventosHorarios() {
    const botonesHorario = document.querySelectorAll('.horario-btn');
    botonesHorario.forEach(boton => {
        boton.addEventListener('click', (e) => {
            e.stopPropagation();
            const cine = boton.dataset.cine;
            const hora = boton.dataset.hora;
            const formato = boton.dataset.formato;
            console.log(`Función seleccionada: ${cine} - ${hora} - ${formato}`);
            alert(`Has seleccionado la función de ${hora} (${formato}) en ${cine}\n\nRedirigiendo a la selección de asientos...`);
        });
    });
}

function cargarDatosPelicula() {
    const urlParams = new URLSearchParams(window.location.search);
    const peliculaId = urlParams.get('id');
    if (peliculaId) {
        console.log('Cargando película con ID:', peliculaId);
    }
}

function inicializarEventosFiltros() {
    btnCiudad.addEventListener('click', () => {
        toggleDropdown(dropdownCiudad, btnCiudad);
    });

    btnCine.addEventListener('click', () => {
        if (!btnCine.disabled) {
            toggleDropdown(dropdownCine, btnCine);
        }
    });

    btnFecha.addEventListener('click', () => {
        if (!btnFecha.disabled) {
            toggleDropdown(dropdownFecha, btnFecha);
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.filtro-item')) {
            cerrarTodosDropdowns();
        }
    });
}

function inicializarEventos() {
    if (botonComprar) {
        botonComprar.addEventListener('click', scrollACompra);
    }
    botonesIdioma.forEach(boton => {
        boton.addEventListener('click', () => {
            cambiarIdioma(boton);
        });
    });
    inicializarEventosFiltros();
}

function inicializarAplicacion() {
    console.log('Inicializando página de selección...');
    cargarDatosPelicula();
    cargarCiudades();
    inicializarEventos();
    console.log('Página de selección lista');
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
    inicializarAplicacion();
}
