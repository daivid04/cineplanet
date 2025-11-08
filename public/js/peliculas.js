// ===== DATOS DE PELÍCULAS =====
const peliculas = [
    {
        id: 1,
        nombre: "Tron Ares",
        genero: "Ciencia Ficción",
        clasificacion: "+14",
        duracion: "2h",
        imagenUrl: "../assets/images/tron.jpg",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima", "arequipa"],
        cine: ["alcazar"],
        generoFiltro: "ciencia-ficcion",
        idioma: "doblada",
        formato: "2d",
        censura: "14"
    },
    {
        id: 2,
        nombre: "Amores Perros 25 Aniversario",
        genero: "Thriller",
        clasificacion: "+18",
        duracion: "2h 34min",
        imagenUrl: "https://images.unsplash.com/photo-1594909122845-11baa439b7bf?w=400&h=600&fit=crop",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima"],
        cine: ["brasil"],
        generoFiltro: "drama",
        idioma: "subtitulada",
        formato: "regular",
        censura: "14"
    },
    {
        id: 3,
        nombre: "Dora: Aventura Mágicas en el Reino de las Sirenas",
        genero: "Animación",
        clasificacion: "APT",
        duracion: "55min",
        imagenUrl: "https://images.unsplash.com/photo-1571847140471-1d7766e825ea?w=400&h=600&fit=crop",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima", "cusco"],
        cine: ["alcazar", "arequipa-mall"],
        generoFiltro: "anime",
        idioma: "doblada",
        formato: "2d",
        censura: "14"
    },
    {
        id: 4,
        nombre: "Downton Abbey El Gran Final",
        genero: "Drama",
        clasificacion: "+13",
        duracion: "2h 5min",
        imagenUrl: "https://images.unsplash.com/photo-1594908900066-3f47337549d8?w=400&h=600&fit=crop",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima", "trujillo"],
        cine: ["brasil"],
        generoFiltro: "drama",
        idioma: "subtitulada",
        formato: "prime",
        censura: "14"
    },
    {
        id: 5,
        nombre: "El Poder del Exorcista",
        genero: "Terror",
        clasificacion: "+14",
        duracion: "1h 36min",
        imagenUrl: "https://images.unsplash.com/photo-1509347528160-9a9e33742cdb?w=400&h=600&fit=crop",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima", "arequipa"],
        cine: ["alcazar"],
        generoFiltro: "terror",
        idioma: "doblada",
        formato: "2d",
        censura: "14"
    },
    {
        id: 6,
        nombre: "It (Eso) [2017]",
        genero: "Terror",
        clasificacion: "+14",
        duracion: "2h 21min",
        imagenUrl: "https://images.unsplash.com/photo-1574267432644-f74f8ec5ba38?w=400&h=600&fit=crop",
        esEstreno: true,
        categoria: "cartelera",
        ciudad: ["lima", "tacna"],
        cine: ["brasil"],
        generoFiltro: "terror",
        idioma: "subtitulada",
        formato: "3d",
        censura: "14"
    },
    {
        id: 7,
        nombre: "Chainsaw Man",
        genero: "Anime/Acción",
        clasificacion: "+18",
        imagenUrl: "../assets/images/chaninsaw.jpg",
        esEstreno: true,
        duracion: "1h 45min",
        categoria: "preventa",
        ciudad: ["lima"],
        cine: ["alcazar"],
        generoFiltro: "anime",
        idioma: "subtitulada",
        formato: "2d",
        censura: "14"
    },
    {
        id: 8,
        nombre: "Catástrofe en el Aire",
        genero: "Acción",
        clasificacion: "+13",
        imagenUrl: "../assets/images/catastrofe.jpg",
        esEstreno: false,
        duracion: "2h 10min",
        categoria: "preventa",
        ciudad: ["lima", "arequipa"],
        cine: ["brasil"],
        generoFiltro: "ciencia-ficcion",
        idioma: "doblada",
        formato: "regular",
        censura: "14"
    },
    {
        id: 9,
        nombre: "Good Boy",
        genero: "Terror",
        clasificacion: "+16",
        imagenUrl: "../assets/images/goodboy.jpg",
        esEstreno: true,
        duracion: "1h 28min",
        categoria: "proximamente",
        ciudad: ["lima"],
        cine: ["alcazar"],
        generoFiltro: "terror",
        idioma: "doblada",
        formato: "2d",
        censura: "14"
    },
    {
        id: 10,
        nombre: "Teléfono Negro 2",
        genero: "Terror",
        clasificacion: "+16",
        imagenUrl: "../assets/images/telefono-negro2.jpg",
        esEstreno: false,
        duracion: "1h 52min",
        categoria: "proximamente",
        ciudad: ["lima", "cusco"],
        cine: ["brasil"],
        generoFiltro: "terror-gore",
        idioma: "subtitulada",
        formato: "prime",
        censura: "14"
    }
];

// ===== ESTADO DE LA APLICACIÓN =====
let categoriaActual = "cartelera";
let filtrosActivos = {
    ciudad: [],
    cine: [],
    dia: null,
    genero: [],
    idioma: [],
    formato: [],
    censura: []
};

// ===== ELEMENTOS DEL DOM =====
const galeria = document.getElementById('galeria-peliculas');
const pestanas = document.querySelectorAll('.pestana');
const botonesFiltrHeader = document.querySelectorAll('.filtro-header');

// ===== FUNCIONES PRINCIPALES =====

/**
 * Crea el HTML de una tarjeta de película
 */
function crearTarjetaPelicula(pelicula) {
    const etiquetaEstreno = pelicula.esEstreno
        ? '<div class="etiqueta-estreno">Estreno</div>'
        : "";

    return `
        <article class="tarjeta-pelicula" data-id="${pelicula.id}">
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
                <p class="genero-pelicula">${pelicula.genero}, ${pelicula.duracion}</p>
                <span class="clasificacion-pelicula">${pelicula.clasificacion}</span>
            </div>
        </article>
    `;
}

/**
 * Filtra las películas según los filtros activos
 */
function filtrarPeliculas(listaPeliculas) {
    return listaPeliculas.filter(pelicula => {
        // Filtro por ciudad
        if (filtrosActivos.ciudad.length > 0) {
            const tieneciudad = filtrosActivos.ciudad.some(ciudad =>
                pelicula.ciudad.includes(ciudad)
            );
            if (!tieneciudad) return false;
        }

        // Filtro por cine
        if (filtrosActivos.cine.length > 0) {
            const tieneCine = filtrosActivos.cine.some(cine =>
                pelicula.cine.includes(cine)
            );
            if (!tieneCine) return false;
        }

        // Filtro por género
        if (filtrosActivos.genero.length > 0) {
            if (!filtrosActivos.genero.includes(pelicula.generoFiltro)) {
                return false;
            }
        }

        // Filtro por idioma
        if (filtrosActivos.idioma.length > 0) {
            if (!filtrosActivos.idioma.includes(pelicula.idioma)) {
                return false;
            }
        }

        // Filtro por formato
        if (filtrosActivos.formato.length > 0) {
            if (!filtrosActivos.formato.includes(pelicula.formato)) {
                return false;
            }
        }

        // Filtro por censura
        if (filtrosActivos.censura.length > 0) {
            if (!filtrosActivos.censura.includes(pelicula.censura)) {
                return false;
            }
        }

        return true;
    });
}

/**
 * Renderiza las películas en la galería
 */
function renderizarPeliculas() {
    if (!galeria) return;

    galeria.innerHTML = '<p class="mensaje-cargando">Cargando películas...</p>';

    setTimeout(() => {
        // Filtra por categoría
        let peliculasFiltradas = peliculas.filter(
            pelicula => pelicula.categoria === categoriaActual
        );

        // Aplica filtros adicionales
        peliculasFiltradas = filtrarPeliculas(peliculasFiltradas);

        if (peliculasFiltradas.length === 0) {
            galeria.innerHTML = '<p class="mensaje-cargando">No hay películas disponibles con los filtros seleccionados</p>';
            return;
        }

        const htmlTarjetas = peliculasFiltradas
            .map(pelicula => crearTarjetaPelicula(pelicula))
            .join("");

        galeria.innerHTML = htmlTarjetas;

        // Agrega eventos de click
        agregarEventosClickTarjetas();
    }, 300);
}

/**
 * Cambia la categoría activa
 */
function cambiarCategoria(categoria) {
    categoriaActual = categoria;

    // Actualiza el estado visual de las pestañas
    pestanas.forEach(pestana => {
        if (pestana.dataset.categoria === categoria) {
            pestana.classList.add('activa');
        } else {
            pestana.classList.remove('activa');
        }
    });

    renderizarPeliculas();
}

/**
 * Maneja el toggle de los filtros
 */
function toggleFiltro(boton) {
    const contenido = boton.nextElementSibling;
    const estaExpandido = contenido.classList.contains('expandido');

    if (estaExpandido) {
        contenido.classList.remove('expandido');
        boton.classList.remove('expandido');
    } else {
        contenido.classList.add('expandido');
        boton.classList.add('expandido');
    }
}

/**
 * Maneja los cambios en los checkboxes de filtros
 */
function manejarCambioFiltro(event) {
    const input = event.target;
    const nombre = input.name;
    const valor = input.value;

    if (input.type === 'checkbox') {
        if (input.checked) {
            if (!filtrosActivos[nombre].includes(valor)) {
                filtrosActivos[nombre].push(valor);
            }
        } else {
            filtrosActivos[nombre] = filtrosActivos[nombre].filter(v => v !== valor);
        }
    } else if (input.type === 'radio') {
        filtrosActivos[nombre] = valor;
    }

    console.log('Filtros activos:', filtrosActivos);
    renderizarPeliculas();
}

/**
 * Agrega eventos de click a las tarjetas
 */
function agregarEventosClickTarjetas() {
    const tarjetas = document.querySelectorAll('.tarjeta-pelicula');

    tarjetas.forEach(tarjeta => {
        tarjeta.addEventListener('click', () => {
            const idPelicula = tarjeta.dataset.id;
            const pelicula = peliculas.find(p => p.id === Number(idPelicula));

            if (pelicula) {
                console.log('Película seleccionada:', pelicula);
                alert(`Has seleccionado: ${pelicula.nombre}`);
                // Aquí puedes redirigir a la página de detalles
                // window.location.href = `pelicula-detalle.html?id=${idPelicula}`;
            }
        });
    });
}

/**
 * Inicializa los eventos de las pestañas
 */
function inicializarEventosPestanas() {
    pestanas.forEach(pestana => {
        pestana.addEventListener('click', () => {
            const categoria = pestana.dataset.categoria;
            cambiarCategoria(categoria);
        });
    });
}

/**
 * Inicializa los eventos de los filtros
 */
function inicializarEventosFiltros() {
    // Eventos para expandir/colapsar filtros
    botonesFiltrHeader.forEach(boton => {
        boton.addEventListener('click', () => {
            toggleFiltro(boton);
        });
    });

    // Eventos para checkboxes y radios
    const inputsFiltros = document.querySelectorAll('.filtro-opcion input');
    inputsFiltros.forEach(input => {
        input.addEventListener('change', manejarCambioFiltro);
    });
}

/**
 * Inicializa la aplicación
 */
function inicializarAplicacion() {
    console.log('Inicializando página de películas...');

    // Inicializa eventos
    inicializarEventosPestanas();
    inicializarEventosFiltros();

    // Renderiza películas iniciales
    renderizarPeliculas();

    console.log('Aplicación iniciada correctamente');
}

// ===== INICIO DE LA APLICACIÓN =====
document.addEventListener('DOMContentLoaded', inicializarAplicacion);
