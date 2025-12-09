import { loadCartelera } from "./movie_template.js";
import { fetchFromApi } from "./../../data-manager.js";


export function setupMovies(){
  renderMovies("cartelera");
  attachFilterEvents();
}


/**
 * Renderiza las películas en la galería
 */
async function renderMovies (opcion) {
  switch (opcion){
    case 'cartelera':
      const billBoard = document.getElementById("galeria-peliculas");
      const movies = await fetchFromApi("pelicula","billBoard", 5);
      let contentMovie = '';
      if(!Array.isArray(movies)) return;
      let i = 0;
      movies.forEach(movie => {
        i++;
        contentMovie += loadCartelera(movie,i);
      });
      contentMovie += `
        <div id='movie-6'><span>Ver más peliculas</span></div>
      `;
      billBoard.innerHTML = contentMovie;
      break;
    case 'pelicula':
      break;
    default:
      console.log("Opcion invalida para renderizado");
      break;

  }
}


export function attachFilterEvents(){
  document.addEventListener("click", (e) =>{
    const button = e.target.closest('button');
    if(!button) return;

    // Obtener el ID de la película desde el atributo data-id
    const movieId = button.dataset.id;
    sessionStorage.setItem('movieId', movieId);
    if(button.classList.contains('btn-buy')){
      window.location.href = `public_html/views/seleccion.html?id=${movieId}`;
    }
    if(button.classList.contains('btn-details')){
      window.location.href = `public_html/views/seleccion.html?id=${movieId}`;
    }
  });
}