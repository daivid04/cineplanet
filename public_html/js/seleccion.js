// ===== DATOS DE PELÍCULAS (Simulados para este prototipo) =====
const peliculasData = [
    {
        id: 1,
        nombre: "Tron Ares",
        genero: "Ciencia Ficción",
        clasificacion: "+14",
        duracion: "2h",
        imagenUrl: "../assets/images/tron.jpg",
        sinopsis: "Ares, un programa altamente sofisticado, es enviado al mundo real en una misión peligrosa."
    },
    {
        id: 2,
        nombre: "Amores Perros 25 Aniversario",
        genero: "Thriller",
        clasificacion: "+18",
        duracion: "2h 34min",
        imagenUrl: "https://images.unsplash.com/photo-1594909122845-11baa439b7bf?w=400&h=600&fit=crop",
        sinopsis: "Un terrible accidente automovilístico enlaza tres historias de personas de diferentes clases sociales."
    }
    // ... agregar más si es necesario
];

// ===== ELEMENTOS DEL DOM =====
const tituloPelicula = document.getElementById('titulo-pelicula');
const generoPelicula = document.getElementById('genero-pelicula');
const duracionPelicula = document.getElementById('duracion-pelicula');
const clasificacionPelicula = document.getElementById('clasificacion-pelicula');
const posterPelicula = document.getElementById('poster-pelicula');
const botonComprar = document.getElementById('boton-comprar');

// Filtros
const selectCiudad = document.getElementById('filtro-ciudad');
const selectCine = document.getElementById('filtro-cine');
const selectFecha = document.getElementById('filtro-fecha');
const listaCines = document.getElementById('lista-cines');

// ===== DATOS DE CINES Y HORARIOS (Simulados) =====
const datosCines = {
    'tacna': [
        {
            id: 'cp-tacna',
            nombre: 'CP Tacna',
            grupos: [
                {
                    formato: '2D',
                    tipo: 'REGULAR, DOBLADA',
                    horarios: ['18:00', '20:00', '22:25']
                },
                {
                    formato: '3D',
                    tipo: 'REGULAR, DOBLADA',
                    horarios: ['19:30', '21:45']
                }
            ]
        }
    ],
    'lima': [
        {
            id: 'cp-alcazar',
            nombre: 'CP Alcazar',
            grupos: [
                {
                    formato: '2D',
                    tipo: 'REGULAR, SUBTITULADA',
                    horarios: ['16:30', '19:00', '21:30']
                },
                {
                    formato: 'PRIME',
                    tipo: 'PRIME, DOBLADA',
                    horarios: ['18:00', '21:00']
                }
            ]
        },
        {
            id: 'cp-salaverry',
            nombre: 'CP Salaverry',
            grupos: [
                {
                    formato: '2D',
                    tipo: 'REGULAR, DOBLADA',
                    horarios: ['15:00', '17:30', '20:00']
                }
            ]
        }
    ],
    'arequipa': [
        {
            id: 'cp-arequipa',
            nombre: 'CP Arequipa',
            grupos: [
                {
                    formato: '2D',
                    tipo: 'REGULAR, DOBLADA',
                    horarios: ['14:00', '16:30', '19:00']
                }
            ]
        }
    ]
};

// ===== FUNCIONES =====

function cargarDatosPelicula() {
    const urlParams = new URLSearchParams(window.location.search);
    const peliculaId = parseInt(urlParams.get('id')) || 1;

    const pelicula = peliculasData.find(p => p.id === peliculaId) || peliculasData[0];

    if (pelicula) {
        tituloPelicula.textContent = pelicula.nombre;
        generoPelicula.textContent = pelicula.genero;
        duracionPelicula.textContent = pelicula.duracion;
        clasificacionPelicula.textContent = pelicula.clasificacion;
        posterPelicula.src = pelicula.imagenUrl;
        posterPelicula.alt = pelicula.nombre;
    }
}

function renderizarHorarios() {
    const ciudad = selectCiudad.value;
    const cines = datosCines[ciudad] || [];

    // Actualizar filtro de cines
    actualizarFiltroCines(cines);

    // Filtrar por cine seleccionado si aplica
    const cineSeleccionado = selectCine.value;
    const cinesFiltrados = cineSeleccionado ? cines.filter(c => c.id === cineSeleccionado) : cines;

    listaCines.innerHTML = '';

    if (cinesFiltrados.length === 0) {
        listaCines.innerHTML = '<p style="text-align:center; padding: 20px;">No hay funciones disponibles para esta selección.</p>';
        return;
    }

    cinesFiltrados.forEach(cine => {
        const cineItem = document.createElement('div');
        cineItem.className = 'cine-item'; // Por defecto cerrado

        let gruposHtml = '';
        cine.grupos.forEach(grupo => {
            let botonesHtml = '';
            grupo.horarios.forEach(hora => {
                botonesHtml += `
                    <button class="btn-horario" onclick="seleccionarHorario('${hora}', '${cine.nombre}', '${grupo.formato}', '${grupo.tipo}')">
                        ${hora} <i class="fas fa-couch"></i>
                    </button>
                `;
            });

            gruposHtml += `
                <div class="grupo-formato">
                    <div class="etiqueta-formato-horario">
                        <span class="badge-2d">${grupo.formato}</span>
                        <span class="texto-formato">${grupo.tipo}</span>
                    </div>
                    <div class="botones-horario">
                        ${botonesHtml}
                    </div>
                </div>
            `;
        });

        cineItem.innerHTML = `
            <div class="cine-header" onclick="toggleAcordeon(this)">
                <h3 class="nombre-cine">${cine.nombre}</h3>
                <button class="btn-expandir">+</button>
            </div>
            <div class="cine-horarios">
                ${gruposHtml}
            </div>
        `;
        listaCines.appendChild(cineItem);
    });
}

function actualizarFiltroCines(cines) {
    // Guardar selección actual si existe
    const seleccionActual = selectCine.value;

    selectCine.innerHTML = '<option value="">Todos los cines</option>';
    cines.forEach(cine => {
        const option = document.createElement('option');
        option.value = cine.id;
        option.textContent = cine.nombre;
        selectCine.appendChild(option);
    });

    // Restaurar selección si es válida para la nueva lista
    if (cines.some(c => c.id === seleccionActual)) {
        selectCine.value = seleccionActual;
    }
}

// Función para el acordeón
window.toggleAcordeon = function (header) {
    const item = header.parentElement;
    const isActive = item.classList.contains('activo');

    // Cerrar todos (opcional, si queremos comportamiento de acordeón estricto)
    // document.querySelectorAll('.cine-item').forEach(el => el.classList.remove('activo'));

    if (!isActive) {
        item.classList.add('activo');
    } else {
        item.classList.remove('activo');
    }
};

// Función global para ser llamada desde el HTML
window.seleccionarHorario = function (hora, cineNombre, formato, tipo) {
    const urlParams = new URLSearchParams(window.location.search);
    const peliculaId = urlParams.get('id') || '1';

    // Guardar datos en localStorage o pasar por URL
    const params = new URLSearchParams({
        peliculaId: peliculaId,
        cine: cineNombre,
        hora: hora,
        fecha: selectFecha.options[selectFecha.selectedIndex].text,
        formato: formato,
        tipo: tipo
    });

    window.location.href = `butacas.html?${params.toString()}`;
};

function inicializarEventos() {
    selectCiudad.addEventListener('change', () => {
        selectCine.value = ""; // Resetear cine al cambiar ciudad
        renderizarHorarios();
    });
    selectCine.addEventListener('change', renderizarHorarios);
    selectFecha.addEventListener('change', renderizarHorarios);

    if (botonComprar) {
        botonComprar.addEventListener('click', () => {
            document.querySelector('.seccion-horarios').scrollIntoView({ behavior: 'smooth' });
        });
    }
}

function inicializarAplicacion() {
    console.log('Inicializando página de selección...');
    cargarDatosPelicula();
    renderizarHorarios();
    inicializarEventos();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
    inicializarAplicacion();
}
