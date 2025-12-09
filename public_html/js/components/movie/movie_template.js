export function loadCartelera(movie, id = 0) {
  if(!movie){
    console.error("No se recibe la pelicula");
  }
  return `
    <div class="movie-card" id='${id > 0 ? `movie-${id}` : ''}'>
      <div class="movie-poster">
        <img src="${movie.url_imagen}" alt="">
      </div>
      <div class="movie-actions">
        <button class="btn-buy" data-id="${movie.id_pelicula}">
          <span class="icon icon-ticket"></span>
          Comprar</button>
        <button class="btn-details" data-id="${movie.id_pelicula}">
          <span class="icon icon-info"></span>
          Ver detalles</button>
      </div>
    </div>
  `;
}
