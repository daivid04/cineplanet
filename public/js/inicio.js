// ===== FUNCIONES PRINCIPALES =====

/**
 * Obtiene las películas desde la API
 * @returns {Promise<Array>} Lista de películas
 */

URL_wa = "http://localhost:8000/public/api/pelicula_api.php";
async function obtenerPeliculas() {
    try {
        const response = await fetch(URL_wa);
        if (!response.ok) {
            throw new Error(`Error al obtener películas: ${response.statusText}`);
        }
        const peliculas = await response.json();
        return peliculas;
    } catch (error) {
        console.error("Error al obtener películas:", error);
        return [];
    }
}

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
    <article class="tarjeta-pelicula ${clasesTamano}" data-id="${pelicula.id_pelicula}">
      ${etiquetaEstreno}
      <div class="contenedor-imagen-pelicula">
        <img 
          src="${pelicula.url_imagen}" 
          alt="Póster de ${pelicula.nombre}"
          class="imagen-pelicula"
          loading="lazy"
          onerror="this.src='https://via.placeholder.com/300x450/95a5a6/ffffff?text=Sin+Imagen'"
        />
      </div>
      <div class="informacion-pelicula">
        <h2 class="nombre-pelicula">${pelicula.nombre}</h2>
        <p class="genero-pelicula">${pelicula.sinopsis}</p>
        <span class="clasificacion-pelicula">${pelicula.duracion} min</span>
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
 * Inicializa la aplicación cuando el DOM está cargado
 */
async function inicializarAplicacion() {
    console.log("Iniciando aplicación Cineplanet...");

    // Obtener películas desde la API
    const peliculas = await obtenerPeliculas();

    // Renderizar las películas obtenidas
    renderizarPeliculas(peliculas);

    console.log("Aplicación iniciada correctamente");
}

// ===== INICIO DE LA APLICACIÓN =====
// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", inicializarAplicacion);