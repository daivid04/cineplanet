document.addEventListener('DOMContentLoaded', () => {
    // Retrieve data
    const datoPelicula = {
        titulo: localStorage.getItem('tituloPelicula') || 'Nada es lo que Parece 3',
        detalles: localStorage.getItem('detallesPelicula') || '2D, REGULAR, DOBLADA',
        cine: localStorage.getItem('nombreCine') || 'CP Tacna',
        fecha: localStorage.getItem('fechaFuncion') || 'Hoy, 26 de Nov, 2025',
        hora: localStorage.getItem('horaFuncion') || '15:30',
        sala: localStorage.getItem('nombreSala') || 'SALA 5-D',
        poster: localStorage.getItem('imagenPelicula') || '../assets/images/poster-placeholder.jpg'
    };

    // Fallback data for demo purposes if localStorage is empty
    const butacas = JSON.parse(localStorage.getItem('butacasSeleccionadas')) || ['K10', 'K9'];
    const cantButacas = parseInt(localStorage.getItem('numeroButacasSeleccionadas')) || butacas.length;

    const entradas = JSON.parse(localStorage.getItem('entradasSeleccionadas')) || [{ tipo: 'General 2D OL', cantidad: 2, precio: 15.00 }];
    const totalEntradas = parseFloat(localStorage.getItem('totalEntradas')) || 30.00;

    const ordenDulceria = JSON.parse(localStorage.getItem('ordenDulceria')) || [];
    const totalDulceria = parseFloat(localStorage.getItem('totalOrdenDulceria')) || 0.00;

    const totalGeneral = totalEntradas + totalDulceria;

    // Render Sidebar Info
    const elTitle = document.getElementById('movie-title');
    if (elTitle) elTitle.textContent = datoPelicula.titulo;

    const elDetails = document.getElementById('movie-details');
    if (elDetails) elDetails.textContent = datoPelicula.detalles;

    const elCinema = document.getElementById('cinema-name');
    if (elCinema) elCinema.textContent = datoPelicula.cine;

    const elDate = document.getElementById('showtime-date');
    if (elDate) elDate.textContent = datoPelicula.fecha;

    const elTime = document.getElementById('showtime-time');
    if (elTime) elTime.textContent = datoPelicula.hora;

    const elRoom = document.getElementById('room-name');
    if (elRoom) elRoom.textContent = datoPelicula.sala;

    const elPoster = document.getElementById('poster-img');
    if (elPoster) elPoster.src = datoPelicula.poster;

    const elNumButacas = document.getElementById('numero-butacas');
    if (elNumButacas) elNumButacas.textContent = cantButacas;

    // Calculate total tickets count
    const cantEntradas = entradas.reduce((acc, curr) => acc + curr.cantidad, 0);
    const elNumEntradas = document.getElementById('numero-entradas');
    if (elNumEntradas) elNumEntradas.textContent = cantEntradas;

    const elNumDulceria = document.getElementById('numero-dulceria');
    if (elNumDulceria) elNumDulceria.textContent = ordenDulceria.length;

    const elTotalLateral = document.getElementById('precio-total-lateral');
    if (elTotalLateral) elTotalLateral.textContent = `S/${totalGeneral.toFixed(2)}`;

    // Render Main Summary Card

    // Butacas
    const elResumenButacas = document.getElementById('resumen-butacas');
    if (elResumenButacas) elResumenButacas.textContent = butacas.join(', ');

    const elCantButacas = document.getElementById('cant-butacas');
    if (elCantButacas) elCantButacas.textContent = cantButacas;

    // Entradas
    if (entradas.length > 0) {
        const entrada = entradas[0]; // Simplified
        const elTipoEntrada = document.getElementById('tipo-entrada');
        if (elTipoEntrada) elTipoEntrada.textContent = entrada.tipo || 'General 2D OL';

        const elCantEntradas = document.getElementById('cant-entradas');
        if (elCantEntradas) elCantEntradas.textContent = entrada.cantidad || cantEntradas;

        const elPrecioEntradas = document.getElementById('precio-entradas');
        if (elPrecioEntradas) elPrecioEntradas.textContent = `S/${totalEntradas.toFixed(2)}`;

        const elSubtotalEntradas = document.getElementById('subtotal-entradas');
        if (elSubtotalEntradas) elSubtotalEntradas.textContent = `S/${totalEntradas.toFixed(2)}`;
    }

    // Dulcería
    const seccionDulceria = document.getElementById('seccion-dulceria-resumen');
    if (seccionDulceria) {
        if (ordenDulceria.length > 0) {
            // Group items
            const conteoProductos = {};
            ordenDulceria.forEach(prod => {
                if (!conteoProductos[prod.idProducto]) {
                    conteoProductos[prod.idProducto] = { ...prod, cantidad: 0 };
                }
                conteoProductos[prod.idProducto].cantidad++;
            });

            Object.values(conteoProductos).forEach(item => {
                const subtotalItem = item.precioProducto * item.cantidad;
                const div = document.createElement('div');
                div.className = 'fila-resumen';
                div.innerHTML = `
                    <div class="detalle-producto">
                        <span>${item.nombreProducto}</span>
                        <small>${item.categoria === 'promos-dulceras' ? '(Salado)' : ''}</small>
                    </div>
                    <span class="cantidad-resumen">Cant. ${item.cantidad}</span>
                    <span class="precio-resumen">S/${subtotalItem.toFixed(2)}</span>
                `;
                seccionDulceria.appendChild(div);
            });

            // Subtotal Dulceria row
            const divSubtotal = document.createElement('div');
            divSubtotal.className = 'subtotal-resumen';
            divSubtotal.innerHTML = `<span>Sub-Total <span>S/${totalDulceria.toFixed(2)}</span></span>`;
            seccionDulceria.appendChild(divSubtotal);
        } else {
            seccionDulceria.innerHTML += '<p style="font-size: 0.9rem; color: #777;">No seleccionaste productos.</p>';
        }
    }

    // Total Final
    const elTotalFinal = document.getElementById('precio-total-final');
    if (elTotalFinal) elTotalFinal.textContent = `S/${totalGeneral.toFixed(2)}`;

    // Timer Logic
    let tiempoMinutos = 4;
    let tiempoSegundos = 37;
    const timerDisplay = document.getElementById('timer-display');

    if (timerDisplay) {
        setInterval(() => {
            if (tiempoSegundos === 0) {
                if (tiempoMinutos === 0) {
                    alert('Tiempo agotado');
                    window.location.href = '../index.html';
                    return;
                }
                tiempoMinutos--;
                tiempoSegundos = 59;
            } else {
                tiempoSegundos--;
            }
            timerDisplay.textContent = `${String(tiempoMinutos).padStart(2, '0')}:${String(tiempoSegundos).padStart(2, '0')}`;
        }, 1000);
    }

    // Buttons
    const btnContinuar = document.querySelector('.btn-continuar-pago');
    if (btnContinuar) {
        btnContinuar.addEventListener('click', () => {
            // Redirigir a la sección de medio de pago
            window.location.href = 'medio_pago.html';
        });
    }

    const btnCerrar = document.querySelector('.btn-cerrar-resumen');
    if (btnCerrar) {
        btnCerrar.addEventListener('click', () => {
            window.location.href = 'dulceria.html';
        });
    }
});
