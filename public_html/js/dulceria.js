document.addEventListener('DOMContentLoaded', () => {
    // ========================================
    // VARIABLES GLOBALES
    // ========================================
    const cantButacasReservadas = parseInt(localStorage.getItem('numeroButacasSeleccionadas')) || 2;
    const cantEntradasSeleccionadas = parseInt(localStorage.getItem('numeroEntradasSeleccionadas')) || 2;
    let ordenDulceria = [];
    let totalOrden = 0;
    // Base total from tickets (S/30.00 as per image)
    let totalEntradas = parseFloat(localStorage.getItem('totalEntradas')) || 30.00;

    // Referencias a elementos del DOM
    const galeriaProductos = document.getElementById('galeria-productos');
    const pestanasDulceria = document.querySelectorAll('.pestana-dulceria');
    const timerDisplay = document.getElementById('timer-display');
    const precioTotalSpan = document.getElementById('precio-total');
    const listaOrden = document.getElementById('lista-orden');
    const mensajeOrdenVacia = document.getElementById('mensaje-orden-vacia');
    const btnContinuar = document.getElementById('btn-continuar-dulceria');

    // Inicializar información en el panel resumen
    const elButacas = document.getElementById('numero-butacas');
    const elEntradas = document.getElementById('numero-entradas');
    if (elButacas) elButacas.textContent = cantButacasReservadas;
    if (elEntradas) elEntradas.textContent = cantEntradasSeleccionadas;

    // Cargar datos de la reserva desde localStorage
    const reservaData = JSON.parse(localStorage.getItem('reservaButacas'));

    if (reservaData) {
        const elTitulo = document.getElementById('movie-title');
        const elDetalles = document.getElementById('movie-details');
        const elCine = document.getElementById('cinema-name');
        const elFecha = document.getElementById('showtime-date');
        const elHora = document.getElementById('showtime-time');
        const elSala = document.getElementById('room-name');
        const elPoster = document.getElementById('poster-img');

        if (elTitulo) elTitulo.textContent = reservaData.titulo;
        if (elDetalles) elDetalles.textContent = `${reservaData.formato}, ${reservaData.tipo || 'REGULAR'}`;
        if (elCine) elCine.textContent = reservaData.cine;
        if (elFecha) elFecha.textContent = reservaData.fecha;
        if (elHora) elHora.textContent = reservaData.hora;
        if (elSala) elSala.textContent = reservaData.sala;
        if (elPoster && reservaData.imagenUrl) elPoster.src = reservaData.imagenUrl;
    }

    // ========================================
    // DATOS ESTÁTICOS DE PRODUCTOS (MATCHING IMAGE)
    // ========================================
    const productosDulceria = {
        'promos-dulceras': [
            {
                idProducto: 1,
                nombreProducto: 'COMBO 2 + CHOCOLATE HH',
                descripcionProducto: 'Canchita Gigante + 2 Bebidas (32oz) + 5 MINI CHOCOLATITOS HH *Sabor de bebida sujeto a stock / canchita sin refill',
                precioProducto: 46.00,
                imagenProducto: '../assets/images/placeholder.svg', // Placeholder
                categoria: 'promos-dulceras'
            },
            {
                idProducto: 2,
                nombreProducto: 'COMBO 2 + 2 DUOMÁX',
                descripcionProducto: '1 Canchita Gigante + 2 Bebidas (32oz) + 2 Duomáx (44g). *Sabor bebida sujeto a stock / canchita sin refill',
                precioProducto: 51.00,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-dulceras'
            },
            {
                idProducto: 3,
                nombreProducto: 'COMBO 1 + B.MOOD 40',
                descripcionProducto: '1 Canchita Grande + 1 Bebida (32oz) + 1 Biscolata Mood (40g). *Sabor bebida sujeto a stock / canchita sin refill',
                precioProducto: 28.50,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-dulceras'
            },
            {
                idProducto: 4,
                nombreProducto: 'COMBO DOS + 2M&M',
                descripcionProducto: '1 Canchita Gigante + 2 Bebidas (32oz) + 2 M&M\'s (45g). *Sabor bebida y M&M\'s sujeto a stock / canchita sin refill',
                precioProducto: 55.00,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-dulceras'
            },
            {
                idProducto: 5,
                nombreProducto: 'COMBO UNO + M&M',
                descripcionProducto: '1 Canchita Grande + 1 Bebida (32oz) + 1 M&M\'s (45g). *Sabor bebida y M&M\'s sujeto a stock / canchita sin refill',
                precioProducto: 31.50,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-dulceras'
            },
            {
                idProducto: 6,
                nombreProducto: 'COMBO 2 + 2 KIT KAT',
                descripcionProducto: '1 Canchita Gigante + 2 Bebidas (32oz) + 2 Kit Kat. *Sabor bebida sujeto a stock / canchita sin refill',
                precioProducto: 54.00,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-dulceras'
            }
        ],
        'promos-pelicula': [
            {
                idProducto: 7,
                nombreProducto: 'COMBO PELÍCULA ESPECIAL',
                descripcionProducto: '1 Canchita Gigante + 2 Bebidas + 1 Nachos con queso',
                precioProducto: 65.00,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'promos-pelicula'
            }
        ],
        'combos-uno-dos': [
            {
                idProducto: 8,
                nombreProducto: 'COMBO PERSONAL',
                descripcionProducto: '1 Canchita Mediana + 1 Bebida (22oz)',
                precioProducto: 25.00,
                imagenProducto: '../assets/images/placeholder.svg',
                categoria: 'combos-uno-dos'
            }
        ],
        'combos-compartir': [],
        'canchitas': [],
        'dulces': [],
        'complementos': []
    };

    // ========================================
    // DATOS DE LA PELÍCULA (del localStorage o estáticos)
    // ========================================
    const datoPelicula = {
        tituloPelicula: localStorage.getItem('tituloPelicula') || 'Nada es lo que Parece 3',
        detallesPelicula: localStorage.getItem('detallesPelicula') || '2D, REGULAR, DOBLADA',
        nombreCine: localStorage.getItem('nombreCine') || 'CP Tacna',
        fechaFuncion: localStorage.getItem('fechaFuncion') || 'Hoy, 26 de Nov, 2025',
        horaFuncion: localStorage.getItem('horaFuncion') || '15:30',
        nombreSala: localStorage.getItem('nombreSala') || 'SALA 5-D',
        imagenPelicula: localStorage.getItem('imagenPelicula') || '../assets/images/nada-es-lo-que-parece-3.jpg'
    };

    // Actualizar información de la película en el panel resumen
    const elTitle = document.getElementById('movie-title');
    if (elTitle) elTitle.textContent = datoPelicula.tituloPelicula;

    const elDetails = document.getElementById('movie-details');
    if (elDetails) elDetails.textContent = datoPelicula.detallesPelicula;

    const elCinema = document.getElementById('cinema-name');
    if (elCinema) elCinema.textContent = datoPelicula.nombreCine;

    const elDate = document.getElementById('showtime-date');
    if (elDate) elDate.textContent = datoPelicula.fechaFuncion;

    const elTime = document.getElementById('showtime-time');
    if (elTime) elTime.textContent = datoPelicula.horaFuncion;

    const elRoom = document.getElementById('room-name');
    if (elRoom) elRoom.textContent = datoPelicula.nombreSala;

    // ========================================
    // FUNCIONES PRINCIPALES
    // ========================================

    // Función para renderizar productos según categoría
    function renderizarProductos(categoria) {
        galeriaProductos.innerHTML = '';
        const productosCategoria = productosDulceria[categoria] || [];

        if (productosCategoria.length === 0) {
            galeriaProductos.innerHTML = '<p style="text-align: center; color: #999; padding: 40px; width: 100%;">No hay productos disponibles en esta categoría.</p>';
            return;
        }

        productosCategoria.forEach(producto => {
            const cardProducto = crearCardProducto(producto);
            galeriaProductos.appendChild(cardProducto);
        });
    }

    // Función para crear card de producto
    function crearCardProducto(producto) {
        const card = document.createElement('div');
        card.className = 'tarjeta-producto'; // Matches CSS
        card.innerHTML = `
            <div class="imagen-producto">
                <img src="${producto.imagenProducto}" alt="${producto.nombreProducto}" onerror="this.src='../assets/images/placeholder.svg'">
            </div>
            <div class="info-producto">
                <h4>${producto.nombreProducto}</h4>
                <p class="descripcion">${producto.descripcionProducto}</p>
                <p class="precio">Precio desde: <strong>S/${producto.precioProducto.toFixed(2)}</strong></p>
                <button class="btn-agregar" data-id="${producto.idProducto}">
                    <i class="fas fa-shopping-cart"></i> Agregar
                </button>
            </div>
        `;

        // Event listener para agregar producto
        const btnAgregar = card.querySelector('.btn-agregar');
        btnAgregar.addEventListener('click', () => agregarProductoOrden(producto));

        return card;
    }

    // Función para agregar producto a la orden
    function agregarProductoOrden(producto) {
        ordenDulceria.push(producto);
        totalOrden += producto.precioProducto;
        actualizarTotal();
        actualizarListaOrden();

        // Feedback visual
        mostrarNotificacion(`${producto.nombreProducto} agregado`);
    }

    // Función para actualizar la lista visual de la orden
    function actualizarListaOrden() {
        if (ordenDulceria.length === 0) {
            listaOrden.style.display = 'none';
            mensajeOrdenVacia.style.display = 'block';
            return;
        }

        listaOrden.style.display = 'block';
        mensajeOrdenVacia.style.display = 'none';
        listaOrden.innerHTML = '';

        // Agrupar productos por ID
        const conteoProductos = {};
        ordenDulceria.forEach(prod => {
            if (!conteoProductos[prod.idProducto]) {
                conteoProductos[prod.idProducto] = { ...prod, cantidad: 0 };
            }
            conteoProductos[prod.idProducto].cantidad++;
        });

        Object.values(conteoProductos).forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'item-orden';
            itemElement.innerHTML = `
                <div class="detalle-item">
                    <span class="cantidad-item">${item.cantidad} x</span>
                    <span class="nombre-item">${item.nombreProducto}</span>
                </div>
                <div class="precio-acciones" style="display: flex; align-items: center;">
                    <span class="precio-item">S/${(item.precioProducto * item.cantidad).toFixed(2)}</span>
                    <button class="btn-eliminar-item" data-id="${item.idProducto}">
                        <i class="far fa-trash-alt"></i>
                    </button>
                </div>
            `;

            // Event listener para eliminar
            itemElement.querySelector('.btn-eliminar-item').addEventListener('click', () => {
                eliminarProductoOrden(item.idProducto);
            });

            listaOrden.appendChild(itemElement);
        });
    }

    // Función para eliminar producto de la orden (uno por uno)
    function eliminarProductoOrden(idProducto) {
        const index = ordenDulceria.findIndex(p => p.idProducto == idProducto);
        if (index > -1) {
            const productoEliminado = ordenDulceria[index];
            totalOrden -= productoEliminado.precioProducto;
            ordenDulceria.splice(index, 1);
            actualizarTotal();
            actualizarListaOrden();
        }
    }

    // Función para actualizar el total
    function actualizarTotal() {
        const totalGeneral = totalEntradas + totalOrden;
        if (precioTotalSpan) precioTotalSpan.textContent = `S/${totalGeneral.toFixed(2)}`;
    }

    // Función para mostrar notificación
    function mostrarNotificacion(mensaje) {
        const notificacion = document.createElement('div');
        notificacion.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 12px 24px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 10000;
            font-size: 0.9rem;
            animation: fadeIn 0.3s;
        `;
        notificacion.textContent = mensaje;
        document.body.appendChild(notificacion);

        setTimeout(() => {
            notificacion.style.opacity = '0';
            notificacion.style.transition = 'opacity 0.3s';
            setTimeout(() => notificacion.remove(), 300);
        }, 2000);
    }

    // ========================================
    // EVENT LISTENERS
    // ========================================

    // Event listener para cambiar categoría
    pestanasDulceria.forEach(pestana => {
        pestana.addEventListener('click', () => {
            pestanasDulceria.forEach(p => p.classList.remove('activa'));
            pestana.classList.add('activa');
            const categoria = pestana.getAttribute('data-categoria');
            renderizarProductos(categoria);
        });
    });

    // Event listener para botón cerrar
    const btnCerrar = document.getElementById('btn-cerrar');
    if (btnCerrar) {
        btnCerrar.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que deseas cancelar tu compra?')) {
                localStorage.clear();
                window.location.href = '../index.html';
            }
        });
    }

    // Event listener para botón Continuar
    if (btnContinuar) {
        btnContinuar.addEventListener('click', () => {
            // Guardar orden en localStorage
            localStorage.setItem('ordenDulceria', JSON.stringify(ordenDulceria));
            localStorage.setItem('totalOrdenDulceria', totalOrden);

            // Redirigir a la página de pago
            window.location.href = 'pago.html';
        });
    }

    // ========================================
    // TEMPORIZADOR
    // ========================================
    let tiempoMinutos = 4;
    let tiempoSegundos = 37; // Matching image roughly

    const actualizarTemporizador = () => {
        if (tiempoSegundos === 0) {
            if (tiempoMinutos === 0) {
                alert('Se agotó el tiempo para completar tu compra.');
                localStorage.clear();
                window.location.href = '../index.html';
                return;
            }
            tiempoMinutos--;
            tiempoSegundos = 59;
        } else {
            tiempoSegundos--;
        }

        const minutosDisplay = String(tiempoMinutos).padStart(2, '0');
        const segundosDisplay = String(tiempoSegundos).padStart(2, '0');
        if (timerDisplay) timerDisplay.textContent = `${minutosDisplay}:${segundosDisplay}`;
    };

    setInterval(actualizarTemporizador, 1000);

    // ========================================
    // INICIALIZACIÓN
    // ========================================
    renderizarProductos('promos-dulceras');
    actualizarTotal();
});
