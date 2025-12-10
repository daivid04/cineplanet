import { fetchFromApi } from "../../data-manager.js";
import { renderDate, renderCities, renderCinemas, renderMovies, renderDescription } from "./render.js";

// Estado global de los datos
let allCities = [];
let allCinemas = [];
let allMovies = [];

/**
 * Inicializa los filtros del sistema
 */
async function setupFilter() {
  await loadData();
  
  // Renderizado inicial
  renderDate();
  renderCities(allCities);
  renderCinemas(allCinemas);
  renderMovies(allMovies);
  
  // Configurar lógica de filtrado y descripciones
  attachFilterEvents();
  renderDescription();
}

/**
 * Carga todos los datos necesarios para los filtros
 */
async function loadData() {
  try {
    const [citiesRes, cinemasRes, moviesRes] = await Promise.all([
      fetchFromApi('ciudad'),
      fetchFromApi('sede'),
      fetchFromApi('pelicula')
    ]);

    allCities = citiesRes?.data || citiesRes || [];
    allCinemas = cinemasRes?.data || cinemasRes || [];
    allMovies = moviesRes?.data || moviesRes || [];
    
  } catch (error) {
    console.error("Error cargando datos de filtros:", error);
  }
}

/**
 * Agrega eventos de lógica de negocio a los filtros
 */
function attachFilterEvents() {
  const citySelect = document.getElementById('dropdown-city');
  const cinemaSelect = document.getElementById('dropdown-cinema');
  const movieSelect = document.getElementById('dropdown-movie');

  // 1. Al cambiar Ciudad -> Filtrar Sedes
  if (citySelect) {
    citySelect.addEventListener('change', (e) => {
      const selectedCityId = e.target.value;
      
      // Filtrar sedes que pertenecen a la ciudad seleccionada
      let filteredCinemas = allCinemas;
      if (selectedCityId) {
        filteredCinemas = allCinemas.filter(c => c.id_ciudad == selectedCityId);
      }
      
      renderCinemas(filteredCinemas);
      
      // Resetear selección de sede si la actual no está en la lista filtrada
      if (cinemaSelect) {
        const currentCinemaValue = cinemaSelect.value;
        const exists = filteredCinemas.find(c => c.id_sede == currentCinemaValue);
        if (!exists) {
          cinemaSelect.value = "";
          cinemaSelect.dispatchEvent(new Event('change'));
        }
      }
    });
  }

  // 2. Al cambiar Sede -> Seleccionar Ciudad automática
  if (cinemaSelect) {
    cinemaSelect.addEventListener('change', (e) => {
      const selectedCinemaId = e.target.value;
      
      if (selectedCinemaId) {
        const selectedCinema = allCinemas.find(c => c.id_sede == selectedCinemaId);
        
        if (selectedCinema && citySelect) {
           if (citySelect.value != selectedCinema.id_ciudad) {
             citySelect.value = selectedCinema.id_ciudad;
             citySelect.dispatchEvent(new Event('change'));
             
             cinemaSelect.value = selectedCinemaId;
             cinemaSelect.dispatchEvent(new Event('change'));
           }
        }
      }
    });
  }

  // 3. Botón Filtrar - Obtener valores seleccionados
  const filterButton = document.querySelector('.boton-filtrar');
  if (filterButton) {
    filterButton.addEventListener('click', () => {
      // Obtener el ID de la película seleccionada
      const selectedMovieId = movieSelect ? movieSelect.value : '';
      
      // Obtener otros valores si los necesitas
      const selectedCityId = citySelect ? citySelect.value : '';
      const selectedCinemaId = cinemaSelect ? cinemaSelect.value : '';
      const selectedDate = document.getElementById('dropdown-date') ? document.getElementById('dropdown-date').value : '';

      if (selectedMovieId) {
        // Aquí puedes llamar a tu función de filtrado o redirección
        let url = `public_html/views/seleccion.html?`;
        if(selectedCinemaId){
          url += `cine=${selectedCinemaId}&`;
        }
        if(selectedCityId){
          url += `ciudad=${selectedCityId}&`;
        }
        if(selectedMovieId){
          url += `id=${selectedMovieId}&`;
        }
        if(selectedDate){
          url += `fecha=${selectedDate}&`;
        }
        url = removeLastChar(url);
        window.location.href = url;
      } else {
        alert("Por favor selecciona una película");
      }
    });
  }
}



export { setupFilter };

function removeLastChar(str) {
  if (!str) return str;      // string vacío o null
  return str.slice(0, -1);
}