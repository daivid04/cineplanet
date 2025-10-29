// ===== DATOS DE PELÍCULAS =====
const peliculas = [
    {
        id: 1,
        nombre: "Teléfono Negro 2",
        genero: "Terror",
        clasificacion: "+16",
        imagenUrl: "../assets/images/telefono-negro2.jpg",
        esEstreno: false,
        tamano: "grande",
        categoria: "cartelera",
    },
    {
        id: 2,
        nombre: "Chainsaw Man",
        genero: "Anime/Acción",
        clasificacion: "+18",
        imagenUrl: "../assets/images/chaninsaw.jpg",
        esEstreno: true,
        tamano: "normal",
        categoria: "cartelera",
    },
    {
        id: 3,
        nombre: "Catástrofe en el Aire",
        genero: "Acción",
        clasificacion: "+13",
        imagenUrl: "../assets/images/catastrofe.jpg",
        esEstreno: true,
        tamano: "normal",
        categoria: "cartelera",
    },

    {
        id: 5,
        nombre: "Good Boy",
        genero: "Terror",
        clasificacion: "+16",
        imagenUrl: "../assets/images/goodboy.jpg",
        esEstreno: true,
        tamano: "mediana",
        categoria: "cartelera",
    },
    {
        id: 6,
        nombre: "Tron",
        genero: "Ciencia Ficción",
        clasificacion: "+13",
        imagenUrl: "../assets/images/tron.jpg",
        esEstreno: false,
        tamano: "normal",
        categoria: "cartelera",
    },
    {
        id: 7,
        nombre: "Próximamente 1",
        genero: "Aventura",
        clasificacion: "+13",
        imagenUrl: "https://via.placeholder.com/300x450/8e44ad/ffffff?text=Próximamente",
        esEstreno: false,
        tamano: "normal",
        categoria: "proximamente",
    },
    {
        id: 8,
        nombre: "Preventa Especial",
        genero: "Drama",
        clasificacion: "ATP",
        imagenUrl: "https://via.placeholder.com/300x450/c0392b/ffffff?text=Preventa",
        esEstreno: true,
        tamano: "normal",
        categoria: "preventa",
    },
];

// ===== FUNCIONES PRINCIPALES =====

/**
 * Crea el HTML de una tarjeta de película
 * @param {Object} pelicula - Objeto con los datos de la película
 * @returns {string} HTML de la tarjeta
 */
function crearTarjetaPelicula(pelicula) {
    const clasesTamano = pelicula.tamano || "normal";
    const etiquetaEstreno = pelicula.esEstreno
        ? '<div class="etiqueta-estreno">Estreno</div>'
        : "";

    return `
    <article class="tarjeta-pelicula ${clasesTamano}" data-id="${pelicula.id}">
      ${etiquetaEstreno}
      <div class="contenedor-imagen-pelicula">
        <img 
          src="${pelicula.imagenUrl}" 
          alt="Póster de ${pelicula.nombre}"
          class="imagen-pelicula"
          loading="lazy"
          onerror="this.src='https://via.placeholder.com/300x450/95a5a6/ffffff?text=Sin+Imagen'"
        />
      </div>
      <div class="informacion-pelicula">
        <h2 class="nombre-pelicula">${pelicula.nombre}</h2>
        <p class="genero-pelicula">${pelicula.genero}</p>
        <span class="clasificacion-pelicula">${pelicula.clasificacion}</span>
      </div>
    </article>
  `;
}

/**
 * Renderiza todas las películas en la galería
 * @param {Array} listaPeliculas - Array de objetos de películas
 */
function renderizarPeliculas(listaPeliculas) {
    const galeria = document.getElementById("galeria-peliculas");

    if (!galeria) {
        console.error("No se encontró el contenedor de la galería");
        return;
    }

    // Muestra mensaje de carga
    galeria.innerHTML = '<p class="mensaje-cargando">Cargando películas...</p>';

    // Simula un pequeño delay para mostrar el estado de carga
    setTimeout(() => {
        if (listaPeliculas.length === 0) {
            galeria.innerHTML =
                '<p class="mensaje-cargando">No hay películas disponibles</p>';
            return;
        }

        // Genera el HTML de todas las tarjetas
        const htmlTarjetas = listaPeliculas
            .map((pelicula) => crearTarjetaPelicula(pelicula))
            .join("");

        galeria.innerHTML = htmlTarjetas;

        // Agrega eventos de click a las tarjetas
        agregarEventosClickTarjetas();
    }, 300);
}

/**
 * Filtra y renderiza películas por categoría
 * @param {string} categoria - Categoría de películas a mostrar
 */
function filtrarPorCategoria(categoria) {
    const peliculasFiltradas = peliculas.filter(
        (pelicula) => pelicula.categoria === categoria
    );

    renderizarPeliculas(peliculasFiltradas);
}

/**
 * Maneja el cambio de pestaña
 * @param {string} categoria - Categoría seleccionada
 */
function cambiarPestana(categoria) {
    // Actualiza el estado visual de las pestañas
    const pestanas = document.querySelectorAll(".pestana");
    pestanas.forEach((pestana) => {
        if (pestana.dataset.categoria === categoria) {
            pestana.classList.add("activa");
        } else {
            pestana.classList.remove("activa");
        }
    });

    // Filtra y muestra las películas de esa categoría
    filtrarPorCategoria(categoria);
}

/**
 * Agrega eventos de click a todas las tarjetas de películas
 */
function agregarEventosClickTarjetas() {
    const tarjetas = document.querySelectorAll(".tarjeta-pelicula");

    tarjetas.forEach((tarjeta) => {
        tarjeta.addEventListener("click", () => {
            const idPelicula = tarjeta.dataset.id;
            manejarClickPelicula(idPelicula);
        });
    });
}

/**
 * Maneja el evento de click en una tarjeta de película
 * @param {string} idPelicula - ID de la película seleccionada
 */
function manejarClickPelicula(idPelicula) {
    const peliculaSeleccionada = peliculas.find(
        (pelicula) => pelicula.id === Number(idPelicula)
    );

    if (peliculaSeleccionada) {
        console.log("Película seleccionada:", peliculaSeleccionada);
        // Aquí puedes agregar la lógica para mostrar detalles o redirigir
        alert(`Has seleccionado: ${peliculaSeleccionada.nombre}`);
    }
}

/**
 * Maneja el evento de click en el botón filtrar
 */
function manejarClickFiltrar() {
    console.log("Botón filtrar clickeado");
    // Aquí puedes agregar la lógica de filtrado
    alert("Función de filtrado en desarrollo");
}

/**
 * Maneja el evento de click en los botones de filtro
 * @param {string} tipoFiltro - Tipo de filtro (cine, ciudad, fecha)
 */
function manejarClickBotonFiltro(tipoFiltro) {
    console.log(`Filtro seleccionado: ${tipoFiltro}`);
    // Aquí puedes agregar la lógica para mostrar opciones de filtro
    alert(`Filtro por ${tipoFiltro} en desarrollo`);
}

/**
 * Inicializa los eventos de los botones de filtro
 */
function inicializarEventosFiltros() {
    const botonesFiltro = document.querySelectorAll(".boton-filtro");
    const botonFiltrar = document.querySelector(".boton-filtrar");

    botonesFiltro.forEach((boton) => {
        boton.addEventListener("click", () => {
            const tipoFiltro = boton.dataset.filtro;
            manejarClickBotonFiltro(tipoFiltro);
        });
    });

    if (botonFiltrar) {
        botonFiltrar.addEventListener("click", manejarClickFiltrar);
    }
}/**
 * Inicializa los eventos de las pestañas de categorías
 */
function inicializarEventosPestanas() {
    const pestanas = document.querySelectorAll(".pestana");

    pestanas.forEach((pestana) => {
        pestana.addEventListener("click", () => {
            const categoria = pestana.dataset.categoria;
            cambiarPestana(categoria);
        });
    });
}

/**
 * Inicializa el evento del botón "Ver más"
 */
function inicializarEventoVerMas() {
    const botonVerMas = document.querySelector(".boton-ver-mas");

    if (botonVerMas) {
        botonVerMas.addEventListener("click", () => {
            console.log("Ver más películas clickeado");
            alert("Cargando más películas...");
            // Aquí puedes agregar lógica para cargar más películas
        });
    }
}

/**
 * Inicializa la aplicación cuando el DOM está cargado
 */
function inicializarAplicacion() {
    console.log("Iniciando aplicación Cineplanet...");

    // Inicializa eventos de filtros
    inicializarEventosFiltros();

    // Inicializa eventos de pestañas
    inicializarEventosPestanas();

    // Inicializa evento del botón ver más
    inicializarEventoVerMas();

    // Renderiza las películas de la categoría inicial (cartelera)
    filtrarPorCategoria("cartelera");

    console.log("Aplicación iniciada correctamente");
}

// ===== INICIO DE LA APLICACIÓN =====
// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", inicializarAplicacion);
