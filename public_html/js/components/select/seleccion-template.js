import { fetchFromApi } from "../../data-manager.js";

export async function loadMovieSelect (){
  let content = '';
  const title = document.getElementById('titulo-pelicula');
  const genre = document.getElementById('genero-pelicula');
  const duration = document.getElementById('duracion-pelicula');
  const clasi = document.getElementById('clasificacion-pelicula');
  const description = document.getElementById('sinopsis-pelicula');
  const language = document.getElementById('opcion-idioma');
  const formato = document.getElementById('formatos-disponibles');
  const img = document.getElementById('poster-pelicula');
  

  const idMovie = sessionStorage.getItem('movieId');
  const response = await fetchFromApi('pelicula','id',idMovie);
  const data = response?.data || response;
  
  if (!data || !data.nombre) {
    console.error('No se pudo cargar la película con id:', idMovie);
    return;
  }
  
  title.innerHTML = data.nombre;
  duration.innerHTML = formatMinutes(data.duracion);
  description.innerHTML  = data.sinopsis;
  language.innerHTML = data.idiomas_texto || data.idiomas || '';
  formato.innerHTML = data.formatos_texto || data.formatos || '';
  img.src = data.url_imagen;
  img.alt = data.nombre;
  genre.innerHTML = data.generos || '';
  const btnBuy = document.getElementById('boton-comprar');
  btnBuy.dataset.id = idMovie;
} 



/**
 * Convierte minutos a formato "Xhr Ymin"
 * @param {number} minutes - total de minutos (ej: 180)
 * @returns {string}
 */
function formatMinutes(minutes) {
  const hrs = Math.floor(minutes / 60);
  const mins = minutes % 60;

  return `${hrs}hr ${mins}min`;
}
