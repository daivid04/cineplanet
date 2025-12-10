/**
 * ==========================================================================
 * CINES.JS - Lógica para la página de listado de cines
 * 
 * Funcionalidades:
 * - Renderizado dinámico de tarjetas de cines
 * - Filtrado por ciudad y formato
 * - Cambio entre vista Lista y Mapa (placeholder)
 * - Redirección a la página de selección de película/horario
 * 
 * Estructura:
 * 1. Datos estáticos de cines (simulados)
 * 2. Referencias al DOM
 * 3. Funciones de renderizado
 * 4. Funciones de filtrado
 * 5. Event Listeners
 * 6. Inicialización
 * ==========================================================================
 */

document.addEventListener('DOMContentLoaded', () => {

    // ========================================================================
    // 1. DATOS ESTÁTICOS DE CINES (Simulados para prototipo)
    // ========================================================================

    /**
     * Array de objetos que representa los cines disponibles.
     * En producción, estos datos vendrían de una API o base de datos.
     */
    const datosCines = [
        {
            id: 'cp-alcazar',
            nombre: 'CP Alcazar',
            direccion: 'Av. Santa Cruz 814-816',
            ciudad: 'Lima',
            formatos: ['2D', 'REGULAR', '3D'],
            imagen: '../assets/images/cines/cine-alcazar.jpg',
            servicios: ['estacionamiento', 'accesibilidad']
        },
        {
            id: 'cp-arequipa-mall-plaza',
            nombre: 'CP Arequipa Mall Plaza',
            direccion: 'Av. Ejercito 793 Cayma',
            ciudad: 'Arequipa',
            formatos: ['2D', '3D', 'REGULAR'],
            imagen: '../assets/images/cines/cine-arequipa-mall.jpg',
            servicios: []
        },
        {
            id: 'cp-arequipa-paseo-central',
            nombre: 'CP Arequipa Paseo Central',
            direccion: 'Av. Arturo Ibañez S/N,',
            ciudad: 'Arequipa',
            formatos: ['2D', 'REGULAR'],
            imagen: '../assets/images/cines/cine-paseo-central.jpg',
            servicios: []
        },
        {
            id: 'cp-arequipa-real-plaza',
            nombre: 'CP Arequipa Real Plaza',
            direccion: 'Av. Ejercito 1009 Cayma',
            ciudad: 'Arequipa',
            formatos: ['2D', 'REGULAR', '3D'],
            imagen: '../assets/images/cines/cine-real-plaza.jpg',
            servicios: []
        },
        {
            id: 'cp-brasil',
            nombre: 'CP Brasil',
            direccion: 'Av. Brasil 714 - 792 Piso 3',
            ciudad: 'Lima',
            formatos: ['2D', 'REGULAR', '3D'],
            imagen: '../assets/images/cines/cine-brasil.jpg',
            servicios: ['estacionamiento', 'accesibilidad']
        },
        {
            id: 'cp-cajamarca',
            nombre: 'CP Cajamarca',
            direccion: 'Av. Vía de Evitamiento Norte',
            ciudad: 'Cajamarca',
            formatos: ['2D', 'REGULAR', '3D'],
            imagen: '../assets/images/cines/cine-cajamarca.jpg',
            servicios: []
        },
        {
            id: 'cp-tacna',
            nombre: 'CP Tacna',
            direccion: 'Av. Bolognesi 999',
            ciudad: 'Tacna',
            formatos: ['2D', 'REGULAR', '3D'],
            imagen: '../assets/images/cines/cine-tacna.jpg',
            servicios: ['estacionamiento']
        },
        {
            id: 'cp-cusco',
            nombre: 'CP Cusco',
            direccion: 'Av. La Cultura 1520',
            ciudad: 'Cusco',
            formatos: ['2D', 'REGULAR'],
            imagen: '../assets/images/cines/cine-cusco.jpg',
            servicios: []
        },
        {
            id: 'cp-trujillo',
            nombre: 'CP Trujillo',
            direccion: 'Av. América Oeste 750',
            ciudad: 'Trujillo',
            formatos: ['2D', '3D', 'REGULAR'],
            imagen: '../assets/images/cines/cine-trujillo.jpg',
            servicios: ['accesibilidad']
        }
    ];

    /**
     * Lista de ciudades únicas extraídas de los datos de cines.
     * Se usa para poblar el dropdown de filtro por ciudad.
     */
    const ciudadesUnicas = [...new Set(datosCines.map(cine => cine.ciudad))].sort();

    // ========================================================================
    // 2. REFERENCIAS AL DOM
    // ========================================================================

    const gridCines = document.getElementById('grid-cines');
    const mensajeSinResultados = document.getElementById('mensaje-sin-resultados');
    const selectCiudad = document.getElementById('filtro-ciudad');
    const selectFormato = document.getElementById('filtro-formato');
    const btnLista = document.getElementById('btn-lista');
    const btnMapa = document.getElementById('btn-mapa');

    // ========================================================================
    // 3. FUNCIONES DE RENDERIZADO
    // ========================================================================

    /**
     * Genera el HTML de una tarjeta de cine.
     * @param {Object} cine - Objeto con los datos del cine.
     * @returns {string} - HTML de la tarjeta.
     */
    function crearTarjetaCineHTML(cine) {
        // Generar iconos de servicios si existen
        let serviciosHTML = '';
        if (cine.servicios.length > 0) {
            serviciosHTML = '<div class="servicios-cine">';
            cine.servicios.forEach(servicio => {
                if (servicio === 'estacionamiento') {
                    serviciosHTML += '<i class="fas fa-parking icono-servicio" title="Estacionamiento"></i>';
                } else if (servicio === 'accesibilidad') {
                    serviciosHTML += '<i class="fas fa-wheelchair icono-servicio accesibilidad" title="Accesibilidad"></i>';
                }
            });
            serviciosHTML += '</div>';
        }

        return `
            <article class="tarjeta-cine" data-cine-id="${cine.id}" onclick="seleccionarCine('${cine.id}')">
                <div class="imagen-cine-container">
                    <img src="${cine.imagen}" 
                         alt="${cine.nombre}" 
                         class="imagen-cine"
                         onerror="this.src='../assets/images/placeholder.svg'">
                </div>
                <div class="info-cine">
                    <h3 class="nombre-cine">${cine.nombre}</h3>
                    <p class="direccion-cine">${cine.direccion}</p>
                    <p class="formatos-cine">${cine.formatos.join(', ')}</p>
                    ${serviciosHTML}
                </div>
            </article>
        `;
    }

    /**
     * Renderiza las tarjetas de cines en el grid.
     * @param {Array} cines - Array de objetos de cines a renderizar.
     */
    function renderizarCines(cines) {
        if (cines.length === 0) {
            gridCines.style.display = 'none';
            mensajeSinResultados.style.display = 'block';
            return;
        }

        gridCines.style.display = 'grid';
        mensajeSinResultados.style.display = 'none';

        gridCines.innerHTML = cines.map(cine => crearTarjetaCineHTML(cine)).join('');
    }

    /**
     * Puebla el dropdown de ciudades con las opciones disponibles.
     */
    function poblarDropdownCiudades() {
        ciudadesUnicas.forEach(ciudad => {
            const option = document.createElement('option');
            option.value = ciudad;
            option.textContent = ciudad;
            selectCiudad.appendChild(option);
        });
    }

    // ========================================================================
    // 4. FUNCIONES DE FILTRADO
    // ========================================================================

    /**
     * Filtra los cines según los criterios seleccionados.
     * @returns {Array} - Array de cines filtrados.
     */
    function filtrarCines() {
        const ciudadSeleccionada = selectCiudad.value;
        const formatoSeleccionado = selectFormato.value;

        return datosCines.filter(cine => {
            // Filtrar por ciudad
            const coincideCiudad = !ciudadSeleccionada || cine.ciudad === ciudadSeleccionada;

            // Filtrar por formato
            const coincideFormato = !formatoSeleccionado || cine.formatos.includes(formatoSeleccionado);

            return coincideCiudad && coincideFormato;
        });
    }

    /**
     * Aplica los filtros y re-renderiza la lista de cines.
     */
    function aplicarFiltros() {
        const cinesFiltrados = filtrarCines();
        renderizarCines(cinesFiltrados);
    }

    // ========================================================================
    // 5. FUNCIONES GLOBALES (Para onclick en HTML)
    // ========================================================================

    /**
     * Maneja la selección de un cine y redirige a la página de películas.
     * @param {string} cineId - ID del cine seleccionado.
     */
    window.seleccionarCine = function (cineId) {
        const cineSeleccionado = datosCines.find(c => c.id === cineId);

        if (cineSeleccionado) {
            // Guardar el cine seleccionado en localStorage para usarlo en otras páginas
            localStorage.setItem('cineSeleccionado', JSON.stringify({
                id: cineSeleccionado.id,
                nombre: cineSeleccionado.nombre,
                ciudad: cineSeleccionado.ciudad
            }));

            // Redirigir a la página de películas con el cine preseleccionado
            window.location.href = `peliculas.html?cine=${cineId}`;
        }
    };

    // ========================================================================
    // 6. EVENT LISTENERS
    // ========================================================================

    // Listener para el filtro de ciudad
    selectCiudad.addEventListener('change', aplicarFiltros);

    // Listener para el filtro de formato
    selectFormato.addEventListener('change', aplicarFiltros);

    // Listeners para los botones de vista
    btnLista.addEventListener('click', () => {
        btnLista.classList.add('activo');
        btnMapa.classList.remove('activo');
        gridCines.style.display = 'grid';
        // Aquí se podría ocultar un componente de mapa si existiera
    });

    btnMapa.addEventListener('click', () => {
        btnMapa.classList.add('activo');
        btnLista.classList.remove('activo');
        // Placeholder: En una implementación real, aquí se mostraría un mapa
        alert('La vista de mapa estará disponible próximamente.');
        // Revertir al estado de lista
        btnLista.classList.add('activo');
        btnMapa.classList.remove('activo');
    });

    // ========================================================================
    // 7. INICIALIZACIÓN
    // ========================================================================

    /**
     * Función de inicialización que se ejecuta al cargar la página.
     */
    function inicializarPagina() {
        console.log('Inicializando página de Cines...');

        // Poblar dropdown de ciudades
        poblarDropdownCiudades();

        // Renderizar todos los cines inicialmente
        renderizarCines(datosCines);

        console.log('Página de Cines lista.');
    }

    // Ejecutar inicialización
    inicializarPagina();

});
