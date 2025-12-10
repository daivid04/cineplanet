import { fetchFromApi } from "../../data-manager.js";

export async function renderButaca() {
  const title = document.getElementById('titulo-pelicula');
  const poster = document.getElementById('poster-pelicula');
  const formato = document.getElementById('detalle-formato');
  const cine = document.getElementById('detalle-cine');
  const fecha = document.getElementById('detalle-fecha');
  const hora = document.getElementById('detalle-hora');
  const sala = document.getElementById('detalle-sala');

  const idFuncion = sessionStorage.getItem('id_funcion');

  try {
    const response = await fetchFromApi('funcion', 'id', idFuncion);

    if (!response || !response.data) {
      console.error('No se recibieron datos de la función');
      return null;
    }

    const data = response.data;

    if (title) title.innerHTML = data.pelicula_nombre || 'Película';

    if (poster) {
      poster.src = data.pelicula_imagen || '../assets/images/poster-placeholder.jpg';
    }

    if (formato) formato.innerHTML = data.formatos || '2D';
    if (cine) cine.innerHTML = data.sede_nombre || 'Cine';
    if (fecha) fecha.innerHTML = data.fecha || '';
    if (hora) hora.innerHTML = data.hora || '';
    if (sala) sala.innerHTML = `SALA ${data.numero_sala || '1'}`;

    // Retornar datos para uso externo
    return data;
  } catch (error) {
    console.error('Error al cargar datos de la función:', error);
    return null;
  }
}