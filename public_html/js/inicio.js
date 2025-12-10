import { setupMovies } from "./components/movie/peliculas.js";
import { setupFilter } from "./components/filtro/filter.js";

// ===== DATOS DE PELÍCULAS =====

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
 * Maneja el evento de click en los botones de filtro
 * @param {string} tipoFiltro - Tipo de filtro (cine, ciudad, fecha)
 */
function manejarClickBotonFiltro(tipoFiltro) {
    console.log(`Filtro seleccionado: ${tipoFiltro}`);
    // Aquí puedes agregar la lógica para mostrar opciones de filtro
    alert(`Filtro por ${tipoFiltro} en desarrollo`);
}

/**
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
    
    // Inicializa filtros
    setupFilter();
    
    // Renderiza películas
    setupMovies();

    // Inicializa eventos de pestañas
    inicializarEventosPestanas();

    // Inicializa evento del botón ver más
    inicializarEventoVerMas();

    console.log("Aplicación iniciada correctamente");
}

// ===== INICIO DE LA APLICACIÓN =====
// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", inicializarAplicacion);