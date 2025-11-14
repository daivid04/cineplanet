/* ===== COMPONENTE REUTILIZABLE: BARRA DE FILTROS ===== */

/**
 * Clase para crear y manejar barras de filtros reutilizables
 * Úsala en cualquier sección que necesite filtros similares
 */
class BarraFiltros {
    /**
     * Constructor de la barra de filtros
     * @param {Object} configuracion - Configuración de la barra
     * @param {string} configuracion.contenedorId - ID del contenedor donde se insertará
     * @param {Array} configuracion.filtros - Array de objetos con los filtros
     * @param {Function} configuracion.alFiltrar - Callback cuando se hace click en filtrar
     * @param {Function} configuracion.alSeleccionarFiltro - Callback cuando se selecciona un filtro
     */
    constructor(configuracion) {
        this.contenedorId = configuracion.contenedorId;
        this.filtros = configuracion.filtros || [];
        this.callbackFiltrar = configuracion.alFiltrar || (() => { });
        this.callbackSeleccionar = configuracion.alSeleccionarFiltro || (() => { });
        this.filtrosSeleccionados = {};
    }

    /**
     * Genera el HTML de la barra de filtros
     * @returns {string} HTML de la barra
     */
    generarHTML() {
        const filtrosHTML = this.filtros
            .map(
                (filtro) => `
      <div class="filtro-item">
        <button class="boton-filtro" data-filtro="${filtro.id}">
          <span class="filtro-titulo">${filtro.titulo}</span>
          <svg class="icono-flecha" viewBox="0 0 24 24" fill="currentColor">
            <path d="M7 10l5 5 5-5z" />
          </svg>
        </button>
        <p class="filtro-descripcion">${filtro.descripcion}</p>
      </div>
    `
            )
            .join("");

        return `
      <section class="seccion-filtros">
        <div class="contenedor-filtros">
          <div class="grupo-filtros">
            ${filtrosHTML}
          </div>
          <button class="boton-filtrar">
            <svg class="icono-filtrar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="4" y1="6" x2="20" y2="6" />
              <line x1="4" y1="12" x2="20" y2="12" />
              <line x1="4" y1="18" x2="20" y2="18" />
              <circle cx="7" cy="6" r="2" fill="currentColor" />
              <circle cx="14" cy="12" r="2" fill="currentColor" />
              <circle cx="11" cy="18" r="2" fill="currentColor" />
            </svg>
            <span>Filtrar</span>
          </button>
        </div>
      </section>
    `;
    }

    /**
     * Renderiza la barra de filtros en el contenedor especificado
     */
    renderizar() {
        const contenedor = document.getElementById(this.contenedorId);

        if (!contenedor) {
            console.error(`No se encontró el contenedor: ${this.contenedorId}`);
            return;
        }

        contenedor.innerHTML = this.generarHTML();
        this.inicializarEventos();
    }

    /**
     * Inicializa los eventos de la barra de filtros
     */
    inicializarEventos() {
        const botonesFiltro = document.querySelectorAll(
            `#${this.contenedorId} .boton-filtro`
        );
        const botonFiltrar = document.querySelector(
            `#${this.contenedorId} .boton-filtrar`
        );

        // Eventos para cada botón de filtro
        botonesFiltro.forEach((boton) => {
            boton.addEventListener("click", () => {
                const filtroId = boton.dataset.filtro;
                this.manejarClickFiltro(filtroId);
            });
        });

        // Evento para el botón principal de filtrar
        if (botonFiltrar) {
            botonFiltrar.addEventListener("click", () => {
                this.manejarClickFiltrar();
            });
        }
    }

    /**
     * Maneja el click en un filtro individual
     * @param {string} filtroId - ID del filtro clickeado
     */
    manejarClickFiltro(filtroId) {
        const filtro = this.filtros.find((f) => f.id === filtroId);

        if (filtro) {
            console.log(`Filtro seleccionado: ${filtro.titulo}`);
            // Ejecuta el callback personalizado
            this.callbackSeleccionar(filtroId, filtro);

            // Aquí puedes agregar lógica para mostrar un dropdown o modal
            // con las opciones del filtro
        }
    }

    /**
     * Maneja el click en el botón principal de filtrar
     */
    manejarClickFiltrar() {
        console.log("Aplicando filtros:", this.filtrosSeleccionados);
        // Ejecuta el callback personalizado
        this.callbackFiltrar(this.filtrosSeleccionados);
    }

    /**
     * Establece el valor de un filtro
     * @param {string} filtroId - ID del filtro
     * @param {*} valor - Valor del filtro
     */
    establecerValorFiltro(filtroId, valor) {
        this.filtrosSeleccionados[filtroId] = valor;
        console.log(`Filtro ${filtroId} actualizado:`, valor);
    }

    /**
     * Obtiene el valor de un filtro
     * @param {string} filtroId - ID del filtro
     * @returns {*} Valor del filtro
     */
    obtenerValorFiltro(filtroId) {
        return this.filtrosSeleccionados[filtroId];
    }

    /**
     * Limpia todos los filtros
     */
    limpiarFiltros() {
        this.filtrosSeleccionados = {};
        console.log("Filtros limpiados");
    }

    /**
     * Obtiene todos los filtros seleccionados
     * @returns {Object} Objeto con todos los filtros
     */
    obtenerFiltros() {
        return { ...this.filtrosSeleccionados };
    }
}

// ===== EJEMPLO DE USO =====

/**
 * Ejemplo 1: Barra de filtros para películas
 */
function crearBarraFiltrosPeliculas() {
    const barraFiltrosPeliculas = new BarraFiltros({
        contenedorId: "contenedor-filtros-peliculas",
        filtros: [
            {
                id: "pelicula",
                titulo: "Por película",
                descripcion: "Qué quieres ver",
            },
            {
                id: "ciudad",
                titulo: "Por ciudad",
                descripcion: "Dónde estás",
            },
            {
                id: "cine",
                titulo: "Por cine",
                descripcion: "Elige tu Cineplanet",
            },
            {
                id: "fecha",
                titulo: "Por fecha",
                descripcion: "Elige un día",
            },
        ],
        alSeleccionarFiltro: (filtroId, filtro) => {
            console.log(`Usuario clickeó en: ${filtro.titulo}`);
            // Aquí mostrarías un dropdown con opciones
            alert(`Selecciona ${filtro.descripcion}`);
        },
        alFiltrar: (filtros) => {
            console.log("Aplicando filtros de películas:", filtros);
            // Aquí aplicarías los filtros a tus películas
            alert("Filtrando películas...");
        },
    });

    barraFiltrosPeliculas.renderizar();
    return barraFiltrosPeliculas;
}

/**
 * Ejemplo 2: Barra de filtros para dulcería
 */
function crearBarraFiltrosDulceria() {
    const barraFiltrosDulceria = new BarraFiltros({
        contenedorId: "contenedor-filtros-dulceria",
        filtros: [
            {
                id: "categoria",
                titulo: "Por categoría",
                descripcion: "Tipo de producto",
            },
            {
                id: "precio",
                titulo: "Por precio",
                descripcion: "Rango de precio",
            },
            {
                id: "combo",
                titulo: "Combos",
                descripcion: "Ver combos disponibles",
            },
        ],
        alSeleccionarFiltro: (filtroId, filtro) => {
            console.log(`Filtro dulcería: ${filtro.titulo}`);
        },
        alFiltrar: (filtros) => {
            console.log("Aplicando filtros de dulcería:", filtros);
        },
    });

    barraFiltrosDulceria.renderizar();
    return barraFiltrosDulceria;
}

/**
 * Ejemplo 3: Barra de filtros para cines
 */
function crearBarraFiltrosCines() {
    const barraFiltrosCines = new BarraFiltros({
        contenedorId: "contenedor-filtros-cines",
        filtros: [
            {
                id: "ciudad",
                titulo: "Por ciudad",
                descripcion: "Dónde quieres ir",
            },
            {
                id: "distrito",
                titulo: "Por distrito",
                descripcion: "Elige tu zona",
            },
            {
                id: "servicios",
                titulo: "Servicios",
                descripcion: "IMAX, 4D, VIP",
            },
        ],
        alSeleccionarFiltro: (filtroId, filtro) => {
            console.log(`Filtro cines: ${filtro.titulo}`);
        },
        alFiltrar: (filtros) => {
            console.log("Aplicando filtros de cines:", filtros);
        },
    });

    barraFiltrosCines.renderizar();
    return barraFiltrosCines;
}

// ===== USO EN TU PROYECTO =====

/*
// En tu HTML, crea un contenedor:
<div id="contenedor-filtros-peliculas"></div>

// En tu JavaScript, después de DOMContentLoaded:
document.addEventListener('DOMContentLoaded', () => {
  const barraFiltros = crearBarraFiltrosPeliculas();
  
  // Puedes establecer valores de filtros programáticamente:
  barraFiltros.establecerValorFiltro('ciudad', 'Lima');
  barraFiltros.establecerValorFiltro('fecha', '2025-10-28');
  
  // Obtener todos los filtros:
  const filtrosActuales = barraFiltros.obtenerFiltros();
  console.log(filtrosActuales);
  
  // Limpiar filtros:
  barraFiltros.limpiarFiltros();
});
*/
