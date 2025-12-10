/**
 * peliculas.js
 * Controlador principal para la página de Películas
 * Maneja carga de películas, filtros dinámicos y categorías
 */

// ======================================
// CONFIGURACIÓN Y CONSTANTES
// ======================================
const API_BASE = '../api';

// Estado global de la aplicación
const appState = {
    peliculas: [],              // Películas base (todas)
    peliculasConFunciones: [],  // Películas con sus funciones (para filtros de ciudad/cine/día)
    filteredPeliculas: [],
    categoriaActual: 'cartelera',
    diaSeleccionado: null,      // Fecha seleccionada para filtrar
    filtros: {
        ciudades: [],
        cines: [],
        generos: [],
        idiomas: [],
        formatos: [],
        censuras: []
    }
};

// ======================================
// FUNCIONES DE API
// ======================================

/**
 * Fetch genérico para las APIs
 */
async function fetchApi(endpoint, params = {}) {
    try {
        let url = `${API_BASE}/${endpoint}`;
        const queryParams = new URLSearchParams(params);
        if (queryParams.toString()) {
            url += `?${queryParams.toString()}`;
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error(`Error al obtener datos de ${endpoint}:`, error);
        return null;
    }
}

/**
 * Cargar todas las películas
 */
async function cargarPeliculas() {
    const response = await fetchApi('pelicula_api.php', { activos: '1' });
    // getAll devuelve { success, data, total }
    if (response && response.success && Array.isArray(response.data)) {
        appState.peliculas = response.data;
        appState.filteredPeliculas = [...response.data];
    }
    return appState.peliculas;
}

/**
 * Cargar películas en cartelera (con toda la info)
 */
async function cargarCartelera() {
    // Usar endpoint activos que devuelve nombre, duracion, idiomas_texto, formatos_texto
    const response = await fetchApi('pelicula_api.php', { activos: '1' });
    if (response && response.success && Array.isArray(response.data)) {
        return response.data;
    }
    return [];
}

/**
 * Cargar películas con funciones (para filtros de ciudad/cine/día)
 * @param {Object} filtros - Objeto con filtros opcionales {fecha, id_ciudad}
 */
async function cargarPeliculasConFunciones(filtros = {}) {
    const params = { accion: 'agrupadas_por_pelicula' };

    if (filtros.fecha) {
        params.fecha = filtros.fecha;
    }
    if (filtros.id_ciudad) {
        params.id_ciudad = filtros.id_ciudad;
    }

    const response = await fetchApi('funcion_api.php', params);
    if (response && response.success && Array.isArray(response.data)) {
        return response.data;
    }
    return [];
}

// ======================================
// FUNCIONES DE CARGA DE FILTROS
// ======================================

/**
 * Cargar ciudades desde la API
 */
async function cargarCiudades() {
    const response = await fetchApi('ciudad_api.php', { activos: '1' });
    if (response && response.success && Array.isArray(response.data)) {
        renderFiltroOpciones('filtro-ciudad', response.data, 'id_ciudad', 'nombre', 'ciudad');
    }
}

/**
 * Cargar sedes/cines desde la API
 */
async function cargarSedes() {
    const response = await fetchApi('sede_api.php', { activos: '1' });
    if (response && response.success && Array.isArray(response.data)) {
        renderFiltroOpciones('filtro-cine', response.data, 'id_sede', 'nombre', 'cine');
    }
}

/**
 * Cargar idiomas desde la API
 */
async function cargarIdiomas() {
    const response = await fetchApi('idioma_api.php', { activos: '1' });
    if (response && response.success && Array.isArray(response.data)) {
        // Usar texto como valor para comparar con idiomas_texto de películas
        renderFiltroOpciones('filtro-idioma', response.data, 'id_idioma', 'idioma', 'idioma', true);
    }
}

/**
 * Cargar formatos desde la API
 */
async function cargarFormatos() {
    const response = await fetchApi('formato_api.php', { activos: '1' });
    if (response && response.success && Array.isArray(response.data)) {
        // Usar texto como valor para comparar con formatos_texto de películas
        renderFiltroOpciones('filtro-formato', response.data, 'id_formato', 'nombre', 'formato', true);
    }
}

/**
 * Renderizar opciones de filtro dinámicamente
 * @param {boolean} useTextAsValue - Si true, usa el texto como valor (para idiomas/formatos)
 */
function renderFiltroOpciones(containerId, items, valueKey, textKey, name, useTextAsValue = false) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = items.map(item => {
        const value = useTextAsValue ? item[textKey] : item[valueKey];
        return `
            <label class="filtro-opcion">
                <input type="checkbox" name="${name}" value="${value}" data-text="${item[textKey]}" />
                <span>${item[textKey]}</span>
            </label>
        `;
    }).join('');
}

/**
 * Generar opciones de días (próximos 7 días)
 */
function cargarDias() {
    const container = document.getElementById('filtro-dia');
    if (!container) return;

    const dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

    let html = '';
    const hoy = new Date();

    for (let i = 0; i < 7; i++) {
        const fecha = new Date(hoy);
        fecha.setDate(hoy.getDate() + i);

        const diaNombre = i === 0 ? 'Hoy' : dias[fecha.getDay()];
        const fechaStr = `${diaNombre} ${fecha.getDate()} ${meses[fecha.getMonth()]}`;
        const valorFecha = fecha.toISOString().split('T')[0];

        html += `
            <label class="filtro-opcion">
                <input type="radio" name="dia" value="${valorFecha}" ${i === 0 ? 'checked' : ''} />
                <span>${fechaStr}</span>
            </label>
        `;
    }

    container.innerHTML = html;
}

/**
 * Generar filtros de géneros - Intenta cargar desde API, si no usa estáticos
 */
async function generarFiltrosGenero() {
    const container = document.getElementById('filtro-genero');
    if (!container) return;

    // Intentar cargar desde API (si existe genero_api.php)
    try {
        const response = await fetchApi('genero_api.php', { activos: '1' });
        if (response && response.success && Array.isArray(response.data) && response.data.length > 0) {
            renderFiltroOpciones('filtro-genero', response.data, 'id_genero', 'nombre', 'genero');
            return;
        }
    } catch (e) {
        // API no existe, usar fallback
    }

    // Fallback: géneros estáticos comunes
    const generos = [
        { id: 'accion', nombre: 'Acción' },
        { id: 'animacion', nombre: 'Animación' },
        { id: 'aventura', nombre: 'Aventura' },
        { id: 'ciencia-ficcion', nombre: 'Ciencia Ficción' },
        { id: 'comedia', nombre: 'Comedia' },
        { id: 'drama', nombre: 'Drama' },
        { id: 'fantasia', nombre: 'Fantasía' },
        { id: 'romance', nombre: 'Romance' },
        { id: 'suspenso', nombre: 'Suspenso' },
        { id: 'terror', nombre: 'Terror' }
    ];

    container.innerHTML = generos.map(genero => `
        <label class="filtro-opcion">
            <input type="checkbox" name="genero" value="${genero.id}" />
            <span>${genero.nombre}</span>
        </label>
    `).join('');
}

/**
 * Generar filtros de censura
 */
function cargarCensuras() {
    const container = document.getElementById('filtro-censura');
    if (!container) return;

    const censuras = [
        { value: 'apt', texto: 'Apto para todos' },
        { value: '+7', texto: '+7' },
        { value: '+12', texto: '+12' },
        { value: '+14', texto: '+14' },
        { value: '+18', texto: '+18' }
    ];

    container.innerHTML = censuras.map(c => `
        <label class="filtro-opcion">
            <input type="checkbox" name="censura" value="${c.value}" />
            <span>${c.texto}</span>
        </label>
    `).join('');
}

// ======================================
// FUNCIONES DE RENDERIZADO
// ======================================

/**
 * Formatear duración en horas y minutos
 */
function formatearDuracion(minutos) {
    if (!minutos) return '';
    const horas = Math.floor(minutos / 60);
    const mins = minutos % 60;
    if (horas > 0) {
        return `${horas}h ${mins}min`;
    }
    return `${mins}min`;
}

/**
 * Template de tarjeta de película
 */
function crearTarjetaPelicula(pelicula, esEstreno = false) {
    const duracionFormateada = formatearDuracion(pelicula.duracion);
    // Extraer primer formato como género visual
    const formato = pelicula.formatos_texto ? pelicula.formatos_texto.split(',')[0].trim() : 'General';
    // Clasificación (por ahora +14 como ejemplo, se podría agregar campo en BD)
    const clasificacion = '+14';

    return `
        <article class="tarjeta-pelicula" data-id="${pelicula.id_pelicula}">
            ${esEstreno ? '<span class="etiqueta-estreno">Estreno</span>' : ''}
            <div class="contenedor-imagen-pelicula">
                <img src="${pelicula.url_imagen || '../assets/images/placeholder.svg'}" 
                     alt="${pelicula.nombre}" 
                     class="imagen-pelicula"
                     onerror="this.src='../assets/images/placeholder.svg'" />
            </div>
            <div class="informacion-pelicula">
                <h3 class="nombre-pelicula">${pelicula.nombre || 'Sin título'}</h3>
                <p class="genero-pelicula">${formato}, ${duracionFormateada}, ${clasificacion}</p>
            </div>
        </article>
    `;
}

/**
 * Renderizar galería de películas
 */
function renderizarPeliculas(peliculas) {
    const galeria = document.getElementById('galeria-peliculas');
    if (!galeria) return;

    if (!peliculas || peliculas.length === 0) {
        galeria.innerHTML = `
            <div class="mensaje-cargando">
                No se encontraron películas con los filtros seleccionados.
            </div>
        `;
        return;
    }

    galeria.innerHTML = peliculas.map((pelicula, index) =>
        crearTarjetaPelicula(pelicula, index < 3) // Marcar las primeras 3 como estreno
    ).join('');
}

/**
 * Mostrar mensaje de carga
 */
function mostrarCargando() {
    const galeria = document.getElementById('galeria-peliculas');
    if (galeria) {
        galeria.innerHTML = `
            <div class="mensaje-cargando">
                <span>Cargando películas...</span>
            </div>
        `;
    }
}

// ======================================
// FUNCIONES DE FILTROS
// ======================================

/**
 * Configurar eventos de expansión/colapso de filtros
 */
function setupFiltrosAccordion() {
    const headers = document.querySelectorAll('.filtro-header');

    headers.forEach(header => {
        header.addEventListener('click', () => {
            const filtroNombre = header.dataset.filtro;
            const contenido = document.getElementById(`filtro-${filtroNombre}`);

            if (contenido) {
                // Toggle clase expandido
                header.classList.toggle('expandido');
                contenido.classList.toggle('expandido');
            }
        });
    });

    // Expandir el primer filtro por defecto
    const primerHeader = document.querySelector('.filtro-header');
    const primerContenido = document.querySelector('.filtro-contenido');
    if (primerHeader && primerContenido) {
        primerHeader.classList.add('expandido');
        primerContenido.classList.add('expandido');
    }
}

/**
 * Obtener filtros seleccionados
 */
function obtenerFiltrosSeleccionados() {
    const filtros = {
        ciudades: [],
        cines: [],
        dia: null,
        generos: [],
        idiomas: [],
        formatos: [],
        censuras: []
    };

    // Ciudades
    document.querySelectorAll('input[name="ciudad"]:checked').forEach(input => {
        filtros.ciudades.push(input.value);
    });

    // Cines
    document.querySelectorAll('input[name="cine"]:checked').forEach(input => {
        filtros.cines.push(input.value);
    });

    // Día
    const diaSeleccionado = document.querySelector('input[name="dia"]:checked');
    if (diaSeleccionado) {
        filtros.dia = diaSeleccionado.value;
    }

    // Géneros
    document.querySelectorAll('input[name="genero"]:checked').forEach(input => {
        filtros.generos.push(input.value);
    });

    // Idiomas
    document.querySelectorAll('input[name="idioma"]:checked').forEach(input => {
        filtros.idiomas.push(input.value);
    });

    // Formatos
    document.querySelectorAll('input[name="formato"]:checked').forEach(input => {
        filtros.formatos.push(input.value);
    });

    // Censuras
    document.querySelectorAll('input[name="censura"]:checked').forEach(input => {
        filtros.censuras.push(input.value);
    });

    return filtros;
}

/**
 * Aplicar filtros a las películas
 * Combina filtros de funciones (ciudad/cine/día) con filtros de película (idioma/formato)
 */
async function aplicarFiltros() {
    const filtros = obtenerFiltrosSeleccionados();

    // Si hay filtros de ciudad, cine o día, necesitamos usar las funciones
    const usarFiltrosFunciones = filtros.ciudades.length > 0 ||
        filtros.cines.length > 0 ||
        filtros.dia;

    let peliculasFiltradas;

    if (usarFiltrosFunciones) {
        // Construir parámetros para la API
        const apiParams = {};

        if (filtros.dia) {
            apiParams.fecha = filtros.dia;
        }

        // Si solo hay una ciudad seleccionada, filtrar desde la API
        if (filtros.ciudades.length === 1) {
            apiParams.id_ciudad = filtros.ciudades[0];
        }

        // Cargar películas con funciones
        const peliculasConFunciones = await cargarPeliculasConFunciones(apiParams);

        // Filtrar por ciudad (si hay múltiples ciudades seleccionadas)
        if (filtros.ciudades.length > 1) {
            peliculasFiltradas = peliculasConFunciones.filter(p => {
                if (!p.funciones || p.funciones.length === 0) return false;
                return p.funciones.some(f =>
                    filtros.ciudades.includes(String(f.id_ciudad))
                );
            });
        } else {
            peliculasFiltradas = [...peliculasConFunciones];
        }

        // Filtrar por cine (sede)
        if (filtros.cines.length > 0) {
            peliculasFiltradas = peliculasFiltradas.filter(p => {
                if (!p.funciones || p.funciones.length === 0) return false;
                return p.funciones.some(f =>
                    filtros.cines.includes(String(f.id_sede))
                );
            });
        }

        // Enriquecer películas con datos completos (idiomas, formatos)
        peliculasFiltradas = peliculasFiltradas.map(pf => {
            const peliculaCompleta = appState.peliculas.find(p => p.id_pelicula === pf.id_pelicula);
            return {
                ...pf,
                idiomas_texto: peliculaCompleta?.idiomas_texto || '',
                formatos_texto: peliculaCompleta?.formatos_texto || ''
            };
        });
    } else {
        // Si no hay filtros de funciones, usar las películas base
        peliculasFiltradas = [...appState.peliculas];
    }

    // Filtrar por idioma (si la película tiene idiomas_texto)
    if (filtros.idiomas.length > 0) {
        peliculasFiltradas = peliculasFiltradas.filter(p => {
            if (!p.idiomas_texto) return false;
            const idiomasPelicula = p.idiomas_texto.toLowerCase();
            return filtros.idiomas.some(idiomaId => {
                return idiomasPelicula.includes(idiomaId.toLowerCase());
            });
        });
    }

    // Filtrar por formato
    if (filtros.formatos.length > 0) {
        peliculasFiltradas = peliculasFiltradas.filter(p => {
            if (!p.formatos_texto) return false;
            const formatosPelicula = p.formatos_texto.toLowerCase();
            return filtros.formatos.some(formatoId => {
                return formatosPelicula.includes(formatoId.toLowerCase());
            });
        });
    }

    appState.filteredPeliculas = peliculasFiltradas;
    renderizarPeliculas(peliculasFiltradas);
}

/**
 * Configurar eventos de cambio en filtros
 */
function setupFiltrosEventos() {
    const panelFiltros = document.querySelector('.panel-filtros');
    if (!panelFiltros) return;

    panelFiltros.addEventListener('change', async (e) => {
        if (e.target.matches('input[type="checkbox"], input[type="radio"]')) {
            // Mostrar carga para filtros que requieren API (ciudad, cine, día)
            const nombreFiltro = e.target.name;
            if (['ciudad', 'cine', 'dia'].includes(nombreFiltro)) {
                mostrarCargando();
            }
            await aplicarFiltros();
        }
    });
}

// ======================================
// FUNCIONES DE PESTAÑAS/CATEGORÍAS
// ======================================

/**
 * Configurar eventos de pestañas
 */
function setupPestanas() {
    const pestanas = document.querySelectorAll('.pestana');

    pestanas.forEach(pestana => {
        pestana.addEventListener('click', async () => {
            // Remover clase activa de todas
            pestanas.forEach(p => p.classList.remove('activa'));
            // Agregar clase activa a la seleccionada
            pestana.classList.add('activa');

            const categoria = pestana.dataset.categoria;
            appState.categoriaActual = categoria;

            mostrarCargando();

            // Cargar películas según categoría
            await cargarPeliculasPorCategoria(categoria);
        });
    });
}

/**
 * Cargar películas según categoría
 */
async function cargarPeliculasPorCategoria(categoria) {
    switch (categoria) {
        case 'cartelera':
            // Películas activas (en cartelera)
            appState.peliculas = await cargarCartelera();
            break;
        case 'preventa':
            // Para preventa, por ahora mostramos mensaje
            appState.peliculas = [];
            break;
        case 'proximamente':
            // Para próximamente, películas con fecha futura
            appState.peliculas = [];
            break;
        default:
            appState.peliculas = await cargarCartelera();
    }

    appState.filteredPeliculas = [...appState.peliculas];
    await aplicarFiltros();
}

// ======================================
// FUNCIONES DE EVENTOS
// ======================================

/**
 * Configurar clic en tarjeta de película
 */
function setupClickPeliculas() {
    const galeria = document.getElementById('galeria-peliculas');
    if (!galeria) return;

    galeria.addEventListener('click', (e) => {
        const tarjeta = e.target.closest('.tarjeta-pelicula');
        if (tarjeta) {
            const peliculaId = tarjeta.dataset.id;
            if (peliculaId) {
                // Guardar ID en sessionStorage para la siguiente página
                sessionStorage.setItem('movieId', peliculaId);
                // Redirigir a la página de selección
                window.location.href = `seleccion.html?id=${peliculaId}`;
            }
        }
    });
}

// ======================================
// INICIALIZACIÓN
// ======================================

/**
 * Inicializar la página
 */
async function init() {
    console.log('Inicializando página de Películas...');

    // Mostrar estado de carga
    mostrarCargando();

    // Configurar UI
    setupFiltrosAccordion();
    setupPestanas();
    setupFiltrosEventos();
    setupClickPeliculas();

    // Cargar filtros dinámicos en paralelo
    await Promise.all([
        cargarCiudades(),
        cargarSedes(),
        cargarIdiomas(),
        cargarFormatos(),
        generarFiltrosGenero()
    ]);

    // Cargar filtros estáticos
    cargarDias();
    cargarCensuras();

    // Cargar películas iniciales (cartelera)
    await cargarPeliculasPorCategoria('cartelera');

    console.log('Página de Películas inicializada correctamente');
}

// Ejecutar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', init);
