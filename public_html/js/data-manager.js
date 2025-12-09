/**
 * URL base del archivo principal de la API
 * @constant {string}
 */
const API_URL = new URL('../api/baul.php', import.meta.url).href;

/**
 * Realiza una petición GET a la API y devuelve la respuesta en formato JSON.
 *
 * La URL se construye dinámicamente usando el endpoint, el tipo de parámetro
 * y su contenido.
 *
 * Ejemplos de URLs generadas:
 * - ../api/baul.php?endpoint=pelicula&all
 * - ../api/baul.php?endpoint=pelicula&id=5
 * - ../api/baul.php?endpoint=pelicula&name=avatar
 * - ../api/funcion_api.php?accion=con_filtros&id_pelicula=2 (cuando type es objeto)
 *
 * @async
 * @function fetchFromApi
 *
 * @param {string} endpoint - Nombre del recurso o controlador (ej: "pelicula", "funcion")
 * @param {string|Object} [type="all"] - Tipo de parámetro de búsqueda (id, name, billBoard, etc.)
 *                                        O un objeto con múltiples parámetros para APIs específicas
 * @param {(string|number|null)} [content=null] - Valor del parámetro a enviar
 *
 * @returns {Promise<Object>} Retorna un objeto JSON con la respuesta de la API
 *
 * @throws {Error} Lanza un error si la respuesta HTTP no es exitosa
 */
export async function fetchFromApi(endpoint, type = "all", content = null) {
  try {
    let url;

    // Si type es un objeto, usar API específica con múltiples parámetros
    if (typeof type === 'object' && type !== null) {
      const baseApiUrl = new URL(`../api/${endpoint}_api.php`, import.meta.url).href;
      const params = new URLSearchParams(type);
      url = `${baseApiUrl}?${params.toString()}`;
    } else {
      // Comportamiento original: usar baul.php
      url = `${API_URL}?endpoint=${endpoint}&${type}`;
      if (content !== null) {
        url += `=${content}`;
      }
    }

    const response = await fetch(url);

    // 1. Obtenemos el texto crudo primero
    const textoRespuesta = await response.text();
    

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status} - ${textoRespuesta}`);
    }

    // 3. Intentamos convertirlo a JSON manualmente
    try {
        return JSON.parse(textoRespuesta);
    } catch (e) {
        throw new Error("El servidor no devolvió un JSON válido. Revisa la consola.");
    }

  } catch (error) {
    console.error("Error al obtener datos", error);
  }
}
