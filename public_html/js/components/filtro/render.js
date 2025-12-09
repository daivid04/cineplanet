
/**
 * Renderiza las opciones de fecha
 */
export function renderDate() {
    const filterDay = document.getElementById('dropdown-date');
    if (!filterDay) return;
    
    const today = dayNumber();
    const tomorrow = dayNumber(1);
    
    let contentDrop = `<option class="opction" value="">Elige un día</option>`;
    contentDrop += `<option class="opction" value="${today}">${today}</option>`;
    contentDrop += `<option class="opction" value="${tomorrow}">${tomorrow}</option>`;
    
    filterDay.innerHTML = contentDrop;
}

/**
 * Renderiza las opciones de ciudades
 * @param {Array} cities - Lista de ciudades
 */
export function renderCities(cities) {
    const filterCity = document.getElementById('dropdown-city');
    if (!filterCity) return;
  
    let contentDrop = '<option class="opction" value="">Elige una ciudad</option>';
    
    if (Array.isArray(cities)) {
      contentDrop += cities.map(city => 
        `<option class="opction" value="${city.id_ciudad}" data-name="${city.nombre}">${city.nombre}</option>`
      ).join('');
    }
    
    filterCity.innerHTML = contentDrop;
}

/**
 * Renderiza las opciones de sedes
 * @param {Array} cinemas - Lista de sedes
 */
export function renderCinemas(cinemas) {
    const filterCinema = document.getElementById('dropdown-cinema');
    if (!filterCinema) return;
  
    let contentDrop = `<option class="opction" value="">Elige una sede</option>`;
    
    if (Array.isArray(cinemas)) {
      contentDrop += cinemas.map(cinema => 
        `<option class="opction" value="${cinema.id_sede}" data-city-id="${cinema.id_ciudad}">${cinema.nombre}</option>`
      ).join('');
    }
    
    filterCinema.innerHTML = contentDrop;
}

/**
 * Renderiza las opciones de películas
 * @param {Array} movies - Lista de películas
 */
export function renderMovies(movies) {
    const filterMovie = document.getElementById('dropdown-movie');
    if (!filterMovie) return;
  
    let contentDrop = `<option class="opction" value="">Elige una película</option>`;
    
    if (Array.isArray(movies)) {
      contentDrop += movies.map(movie => 
        `<option class="opction" value="${movie.id_pelicula}">${movie.nombre}</option>`
      ).join('');
    }
    
    filterMovie.innerHTML = contentDrop;
}

/**
 * Configura la descripción de los filtros
 */
export function renderDescription() {
    const selects = document.querySelectorAll(".dropdown-item");
    
    selects.forEach((select) => {
      const container = select.closest('.filtro-item').querySelector('.filtro-descripcion');
      
      if (container) {
        if (!container.dataset.original) {
          container.dataset.original = container.textContent;
        }
  
        // Remover listeners anteriores para evitar duplicados si se llama múltiples veces
        // Nota: Esto es un poco truculento sin referencias a las funciones, 
        // pero como renderDescription se suele llamar una vez o al reiniciar, 
        // aseguramos que el evento se adjunte correctamente.
        // Una mejor aproximación es usar un atributo para marcar que ya tiene evento.
        if (select.dataset.hasDescriptionEvent) return;

        select.addEventListener('change', (e) => {
          const valor = e.target.value;
          const texto = e.target.options[e.target.selectedIndex].text;
          
          if (valor && valor !== "") {
            container.textContent = texto;
            container.classList.add('active');
          } else {
            container.textContent = container.dataset.original;
            container.classList.remove('active');
          }
        });

        select.dataset.hasDescriptionEvent = "true";
      }
    });
}

// Helper function for date (assuming it was global or needs to be here)
function dayNumber(addDays = 0) {
    const date = new Date();
    date.setDate(date.getDate() + addDays);
    const options = { day: 'numeric', month: 'long' };
    return date.toLocaleDateString('es-ES', options);
}
