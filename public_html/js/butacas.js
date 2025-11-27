// ===== DATOS DE PELÍCULAS (Simulados) =====
const peliculasData = [
    {
        id: 1,
        nombre: "Tron Ares",
        imagenUrl: "../assets/images/tron.jpg"
    },
    {
        id: 2,
        nombre: "Amores Perros 25 Aniversario",
        imagenUrl: "https://images.unsplash.com/photo-1594909122845-11baa439b7bf?w=400&h=600&fit=crop"
    }
];

// ===== ELEMENTOS DEL DOM =====
const btnAtras = document.getElementById('btn-atras');
const btnCerrar = document.getElementById('btn-cerrar');
const btnUsuario = document.getElementById('btn-usuario');
const btnContinuar = document.getElementById('btn-continuar');
const mapaAsientos = document.getElementById('mapa-asientos');
const listaButacas = document.getElementById('lista-butacas');
const tiempoRestante = document.getElementById('tiempo-restante');

// Elementos de información
const tituloPelicula = document.getElementById('titulo-pelicula');
const posterPelicula = document.getElementById('poster-pelicula');
const detalleFormato = document.getElementById('detalle-formato');
const detalleCine = document.getElementById('detalle-cine');
const detalleFecha = document.getElementById('detalle-fecha');
const detalleHora = document.getElementById('detalle-hora');
const detalleSala = document.getElementById('detalle-sala');

// ===== ESTADO DE LA APLICACIÓN =====
let butacasSeleccionadas = [];
let tiempoSesion = 5 * 60; // 5 minutos en segundos
let intervaloTemporizador = null;
let datosReservaGlobal = {}; // Para guardar todo

// ===== CONFIGURACIÓN DE LA SALA =====
// Estructura: A-L filas, con pasillos y asientos para discapacitados
const configuracionSala = [
    { fila: 'A', asientos: [1, 2, null, 1, 2, 3, 4, 5, null, 'disc', null, 'disc', null, 1, 2, 3] },
    { fila: 'B', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, null, null, null, 1, 2, 3, 4] },
    { fila: 'C', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'D', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'E', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'F', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'G', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'H', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16] },
    { fila: 'I', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, null, 1, 2, 3] },
    { fila: 'J', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, null, 1, 2, 3] },
    { fila: 'K', asientos: [1, 2, null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, null, 1, 2, 3] },
    { fila: 'L', asientos: [1, 2, 3, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23] }
];

// Asientos ocupados (simulados) - basado en la imagen
const asientosOcupados = [
    'H12', 'H13', // Fila H asientos 12 y 13 en rojo
    'J11', 'J12'  // Fila J asientos 11 y 12 en azul oscuro (ya seleccionados por otro usuario)
];

// ===== FUNCIONES PRINCIPALES =====

/**
 * Carga los parámetros de la URL
 */
function cargarParametrosURL() {
    const params = new URLSearchParams(window.location.search);
    const peliculaId = parseInt(params.get('peliculaId')) || 1;
    const pelicula = peliculasData.find(p => p.id === peliculaId) || peliculasData[0];

    datosReservaGlobal = {
        peliculaId: peliculaId,
        titulo: pelicula.nombre,
        imagenUrl: pelicula.imagenUrl,
        cine: params.get('cine') || 'CP Tacna',
        hora: params.get('hora') || '20:00',
        formato: params.get('formato') || '2D',
        fecha: params.get('fecha') || 'Mañana, 14 de Nov. 2025',
        sala: 'SALA 3-D',
        tipo: params.get('tipo') || 'REGULAR, DOBLADA'
    };

    // Actualizar la interfaz
    tituloPelicula.textContent = datosReservaGlobal.titulo;
    if (posterPelicula) posterPelicula.src = datosReservaGlobal.imagenUrl;

    detalleFormato.textContent = `${datosReservaGlobal.formato}, ${datosReservaGlobal.tipo}`;
    detalleCine.textContent = datosReservaGlobal.cine;
    detalleFecha.innerHTML = `
        <svg class="icono-calendario" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/>
        </svg>
        ${datosReservaGlobal.fecha}
    `;
    detalleHora.innerHTML = `
        <svg class="icono-reloj" viewBox="0 0 24 24" fill="currentColor">
            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
        </svg>
        ${datosReservaGlobal.hora}
    `;
    detalleSala.innerHTML = `
        <svg class="icono-sala" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
        </svg>
        ${datosReservaGlobal.sala}
    `;
}

/**
 * Genera el mapa de asientos
 */
function generarMapaAsientos() {
    mapaAsientos.innerHTML = '';

    configuracionSala.forEach(filaConfig => {
        const filaDiv = document.createElement('div');
        filaDiv.className = 'fila-asientos';


        // Letra de la fila (izquierda)
        const letraIzq = document.createElement('div');
        letraIzq.className = 'letra-fila';
        letraIzq.textContent = filaConfig.fila;
        filaDiv.appendChild(letraIzq);

        // Asientos
        let numeroAsiento = 1;
        filaConfig.asientos.forEach((asiento, index) => {
            if (asiento === null) {
                // Espacio de pasillo
                const pasillo = document.createElement('div');
                pasillo.className = 'espacio-pasillo';
                filaDiv.appendChild(pasillo);
            } else if (asiento === 'disc') {
                // Asiento para discapacitados
                const asientoDiv = document.createElement('div');
                asientoDiv.className = 'asiento discapacitado disponible';
                asientoDiv.dataset.fila = filaConfig.fila;
                asientoDiv.dataset.numero = numeroAsiento;
                asientoDiv.dataset.id = `${filaConfig.fila}${numeroAsiento}`;
                asientoDiv.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h2.2l1.8-2h4l1.8 2H18v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4z"/>
                    </svg>
                `;

                asientoDiv.addEventListener('click', () => toggleAsiento(asientoDiv));
                filaDiv.appendChild(asientoDiv);
                numeroAsiento++;
            } else {
                // Asiento normal
                const asientoDiv = document.createElement('div');
                const idAsiento = `${filaConfig.fila}${numeroAsiento}`;

                // Verificar si está ocupado
                const estaOcupado = asientosOcupados.includes(idAsiento);

                asientoDiv.className = `asiento ${estaOcupado ? 'ocupada' : 'disponible'}`;
                asientoDiv.dataset.fila = filaConfig.fila;
                asientoDiv.dataset.numero = numeroAsiento;
                asientoDiv.dataset.id = idAsiento;

                if (!estaOcupado) {
                    asientoDiv.addEventListener('click', () => toggleAsiento(asientoDiv));
                }

                filaDiv.appendChild(asientoDiv);
                numeroAsiento++;
            }
        });

        // Letra de la fila (derecha)
        const letraDer = document.createElement('div');
        letraDer.className = 'letra-fila';
        letraDer.textContent = filaConfig.fila;
        filaDiv.appendChild(letraDer);

        mapaAsientos.appendChild(filaDiv);
    });
}

/**
 * Alterna la selección de un asiento
 */
function toggleAsiento(asientoDiv) {
    if (asientoDiv.classList.contains('ocupada')) {
        return;
    }

    const idAsiento = asientoDiv.dataset.id;

    if (asientoDiv.classList.contains('seleccionada')) {
        // Deseleccionar
        asientoDiv.classList.remove('seleccionada');
        butacasSeleccionadas = butacasSeleccionadas.filter(id => id !== idAsiento);
    } else {
        // Seleccionar
        asientoDiv.classList.add('seleccionada');
        butacasSeleccionadas.push(idAsiento);
    }

    actualizarListaButacas();
}

/**
 * Actualiza la lista de butacas seleccionadas
 */
function actualizarListaButacas() {
    if (butacasSeleccionadas.length === 0) {
        listaButacas.textContent = '-';
        btnContinuar.disabled = true;
    } else {
        // Ordenar butacas alfabéticamente
        const butacasOrdenadas = [...butacasSeleccionadas].sort((a, b) => {
            const filaA = a.charAt(0);
            const filaB = b.charAt(0);
            if (filaA !== filaB) {
                return filaA.localeCompare(filaB);
            }
            const numA = parseInt(a.substring(1));
            const numB = parseInt(b.substring(1));
            return numA - numB;
        });

        listaButacas.textContent = butacasOrdenadas.join(', ');
        btnContinuar.disabled = false;
    }
}

/**
 * Inicia el temporizador de sesión
 */
function iniciarTemporizador() {
    intervaloTemporizador = setInterval(() => {
        tiempoSesion--;

        const minutos = Math.floor(tiempoSesion / 60);
        const segundos = tiempoSesion % 60;

        tiempoRestante.textContent = `${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')}`;

        // Cambiar color cuando quede poco tiempo
        if (tiempoSesion <= 60) {
            tiempoRestante.style.color = 'var(--color-rojo)';
        }

        // Cuando se acabe el tiempo
        if (tiempoSesion <= 0) {
            clearInterval(intervaloTemporizador);
            alert('Se ha agotado el tiempo de selección. Serás redirigido a la página anterior.');
            window.location.href = 'seleccion.html';
        }
    }, 1000);
}

/**
 * Maneja el clic en el botón Atrás
 */
function irAtras() {
    if (butacasSeleccionadas.length > 0) {
        const confirmar = confirm('¿Estás seguro de que deseas salir? Perderás tu selección de butacas.');
        if (!confirmar) return;
    }
    window.location.href = 'seleccion.html';
}

/**
 * Maneja el clic en el botón Cerrar
 */
function cerrarVentana() {
    if (butacasSeleccionadas.length > 0) {
        const confirmar = confirm('¿Estás seguro de que deseas salir? Perderás tu selección de butacas.');
        if (!confirmar) return;
    }
    // Limpiar datos y redirigir a inicio
    localStorage.clear();
    window.location.href = '../../index.html';
}

/**
 * Maneja el clic en el botón Continuar
 */
function continuar() {
    if (butacasSeleccionadas.length === 0) {
        return;
    }

    // Guardar selección en localStorage
    const datosReserva = {
        butacas: butacasSeleccionadas,
        cantidad: butacasSeleccionadas.length,
        fecha: new Date().toISOString(),
        ...datosReservaGlobal // Incluir datos de la película
    };

    localStorage.setItem('reservaButacas', JSON.stringify(datosReserva));
    localStorage.setItem('numeroButacasSeleccionadas', butacasSeleccionadas.length.toString());

    // Detener temporizador
    clearInterval(intervaloTemporizador);

    // Redirigir a la página de entradas
    window.location.href = 'entradas.html';
}

/**
 * Inicializa los eventos
 */
function inicializarEventos() {
    btnAtras.addEventListener('click', irAtras);
    btnCerrar.addEventListener('click', cerrarVentana);
    btnUsuario.addEventListener('click', () => {
        // Redirigir al perfil de usuario o login
        window.location.href = 'login.html';
    });
    btnContinuar.addEventListener('click', continuar);
}

/**
 * Inicializa la aplicación
 */
function inicializarAplicacion() {
    console.log('Inicializando página de selección de butacas...');

    // Cargar información de la reserva
    cargarParametrosURL();

    // Generar el mapa de asientos
    generarMapaAsientos();

    // Iniciar temporizador
    iniciarTemporizador();

    // Inicializar eventos
    inicializarEventos();

    console.log('Página de butacas lista');
}

// ===== INICIALIZACIÓN =====
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarAplicacion);
} else {
    inicializarAplicacion();
}
