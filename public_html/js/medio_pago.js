document.addEventListener('DOMContentLoaded', () => {
    // ========================================
    // INITIALIZATION & DATA LOADING
    // ========================================

    // Load movie data from localStorage
    const reservaData = JSON.parse(localStorage.getItem('reservaButacas'));

    let datoPelicula = {};
    let butacas = [];

    if (reservaData) {
        datoPelicula = {
            titulo: reservaData.titulo,
            detalles: `${reservaData.formato}, ${reservaData.tipo || 'REGULAR'}`,
            cine: reservaData.cine,
            fecha: reservaData.fecha,
            hora: reservaData.hora,
            sala: reservaData.sala,
            poster: reservaData.imagenUrl || '../assets/images/poster-placeholder.jpg'
        };
        butacas = reservaData.butacas || [];
    } else {
        datoPelicula = {
            titulo: localStorage.getItem('tituloPelicula') || 'Nada es lo que Parece 3',
            detalles: localStorage.getItem('detallesPelicula') || '2D, REGULAR, DOBLADA',
            cine: localStorage.getItem('nombreCine') || 'CP Tacna',
            fecha: localStorage.getItem('fechaFuncion') || 'Hoy, 26 de Nov, 2025',
            hora: localStorage.getItem('horaFuncion') || '15:30',
            sala: localStorage.getItem('nombreSala') || 'SALA 5-D',
            poster: localStorage.getItem('imagenPelicula') || '../assets/images/poster-placeholder.jpg'
        };
        butacas = JSON.parse(localStorage.getItem('butacasSeleccionadas')) || ['K10', 'K9'];
    }

    // Load cart data
    const cantButacas = parseInt(localStorage.getItem('numeroButacasSeleccionadas')) || butacas.length;
    const entradas = JSON.parse(localStorage.getItem('entradasSeleccionadas')) || [{ tipo: 'General 2D OL', cantidad: 2, precio: 15.00 }];
    const totalEntradas = parseFloat(localStorage.getItem('totalEntradas')) || 30.00;
    const ordenDulceria = JSON.parse(localStorage.getItem('ordenDulceria')) || [];
    const totalDulceria = parseFloat(localStorage.getItem('totalOrdenDulceria')) || 0.00;
    const totalGeneral = totalEntradas + totalDulceria;

    // Render Sidebar Info
    renderSidebar(datoPelicula, cantButacas, entradas, ordenDulceria, totalGeneral);

    // Timer Logic
    initTimer();

    // ========================================
    // UI INTERACTION LOGIC
    // ========================================

    // Payment Method Selection (Accordion)
    window.seleccionarMetodo = function (metodo) {
        // Remove active class from all options
        document.querySelectorAll('.opcion-pago').forEach(el => el.classList.remove('activa'));

        // Add active class to selected option
        const selected = document.getElementById(`opcion-${metodo}`);
        if (selected) {
            selected.classList.add('activa');
        }
    };

    // Wallet Selection
    window.seleccionarBilletera = function (element, billetera) {
        // Remove selected class from all wallet buttons
        document.querySelectorAll('.btn-billetera').forEach(el => el.classList.remove('seleccionada'));

        // Add selected class to clicked button
        element.classList.add('seleccionada');

        // Store selected wallet in a data attribute or variable if needed
        element.closest('.contenido-pago').dataset.selectedWallet = billetera;
    };

    // Process Payment
    window.procesarPago = function (metodo) {
        // Basic Validation
        const nombre = document.getElementById('input-nombre').value;
        const email = document.getElementById('input-email').value;
        const terminos = document.getElementById('check-terminos').checked;

        if (!nombre || !email) {
            alert('Por favor, complete sus datos personales.');
            return;
        }

        if (!terminos) {
            alert('Debe aceptar los Términos y Condiciones.');
            return;
        }

        if (metodo === 'billetera') {
            const walletContainer = document.getElementById('opcion-billetera').querySelector('.contenido-pago');
            if (!walletContainer.dataset.selectedWallet) {
                alert('Por favor, seleccione una billetera electrónica.');
                return;
            }
        }

        // Simulate Processing
        const btn = event.target;
        const originalText = btn.textContent;
        btn.textContent = 'Procesando...';
        btn.disabled = true;

        setTimeout(() => {
            // Populate and Show Modal
            popularTicketPreview(datoPelicula, butacas, totalGeneral, nombre);
            document.getElementById('modal-exito').style.display = 'flex';

            btn.textContent = originalText;
            btn.disabled = false;
        }, 2000);
    };

    // ========================================
    // MODAL & PDF LOGIC
    // ========================================

    window.cerrarModal = function () {
        document.getElementById('modal-exito').style.display = 'none';
    };

    window.finalizarCompra = function () {
        localStorage.clear();
        window.location.href = '../index.html';
    };

    window.descargarPDF = function () {
        const element = document.getElementById('ticket-template');

        // Crear un contenedor temporal fuera de la vista para evitar cortes por scroll
        const container = document.createElement('div');
        container.style.position = 'absolute';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.style.width = '600px'; // Ancho fijo para asegurar formato A4 correcto
        document.body.appendChild(container);

        // Clonar el ticket y resetear estilos conflictivos
        const clone = element.cloneNode(true);
        clone.style.display = 'block';
        clone.style.width = '100%';
        clone.style.height = 'auto';
        clone.style.overflow = 'visible';
        clone.style.boxShadow = 'none';
        clone.style.border = 'none';

        container.appendChild(clone);

        const opt = {
            margin: 10,
            filename: 'ticket-cineplanet.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false, scrollY: 0 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Show loading state on button
        const btn = document.querySelector('.btn-descargar');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        btn.disabled = true;

        html2pdf().set(opt).from(clone).save().then(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            document.body.removeChild(container);
        }).catch(err => {
            console.error(err);
            btn.innerHTML = originalText;
            btn.disabled = false;
            if (document.body.contains(container)) {
                document.body.removeChild(container);
            }
        });
    };

    function popularTicketPreview(movieData, seats, total, userName) {
        // Populate PDF Template
        document.getElementById('pdf-ticket-id').textContent = 'WPMT' + Math.floor(Math.random() * 10000);
        document.getElementById('pdf-movie-title').textContent = movieData.titulo;
        document.getElementById('pdf-user-name').textContent = userName;
        document.getElementById('pdf-cinema').textContent = movieData.cine;
        document.getElementById('pdf-date').textContent = movieData.fecha;
        document.getElementById('pdf-time').textContent = movieData.hora;
        document.getElementById('pdf-room').textContent = movieData.sala;
        document.getElementById('pdf-seats').textContent = seats.join(', ');
        document.getElementById('pdf-total').textContent = `S/${total.toFixed(2)}`;

        // Populate Items List in PDF
        const itemsContainer = document.getElementById('pdf-items-list');
        itemsContainer.innerHTML = '';

        // Add Tickets
        const ticketRow = document.createElement('div');
        ticketRow.className = 'detail-row';
        ticketRow.innerHTML = `
            <span>Entradas (${seats.length})</span>
            <span>Cant: ${seats.length}</span>
            <span>S/${totalEntradas.toFixed(2)}</span>
        `;
        itemsContainer.appendChild(ticketRow);

        // Add Candy Items
        if (ordenDulceria.length > 0) {
            // Group items logic (simplified)
            const conteo = {};
            ordenDulceria.forEach(p => {
                conteo[p.nombreProducto] = (conteo[p.nombreProducto] || 0) + 1;
            });

            for (const [nombre, cant] of Object.entries(conteo)) {
                const row = document.createElement('div');
                row.className = 'detail-row';
                row.innerHTML = `
                    <span>${nombre}</span>
                    <span>Cant: ${cant}</span>
                    <span>-</span>
                `;
                itemsContainer.appendChild(row);
            }
        }
    }

    // ========================================
    // HELPER FUNCTIONS
    // ========================================

    function renderSidebar(data, seatsCount, tickets, candyOrder, total) {
        const elTitle = document.getElementById('movie-title');
        if (elTitle) elTitle.textContent = data.titulo;

        const elDetails = document.getElementById('movie-details');
        if (elDetails) elDetails.textContent = data.detalles;

        const elCinema = document.getElementById('cinema-name');
        if (elCinema) elCinema.textContent = data.cine;

        const elDate = document.getElementById('showtime-date');
        if (elDate) elDate.textContent = data.fecha;

        const elTime = document.getElementById('showtime-time');
        if (elTime) elTime.textContent = data.hora;

        const elRoom = document.getElementById('room-name');
        if (elRoom) elRoom.textContent = data.sala;

        const elPoster = document.getElementById('poster-img');
        if (elPoster) elPoster.src = data.poster;

        const elNumButacas = document.getElementById('numero-butacas');
        if (elNumButacas) elNumButacas.textContent = seatsCount;

        const cantEntradas = tickets.reduce((acc, curr) => acc + curr.cantidad, 0);
        const elNumEntradas = document.getElementById('numero-entradas');
        if (elNumEntradas) elNumEntradas.textContent = cantEntradas;

        const elNumDulceria = document.getElementById('numero-dulceria');
        if (elNumDulceria) elNumDulceria.textContent = candyOrder.length;

        const elTotalLateral = document.getElementById('precio-total-lateral');
        if (elTotalLateral) elTotalLateral.textContent = `S/${total.toFixed(2)}`;
    }

    function initTimer() {
        let tiempoMinutos = 4;
        let tiempoSegundos = 37;
        const timerDisplay = document.getElementById('timer-display');

        if (timerDisplay) {
            setInterval(() => {
                if (tiempoSegundos === 0) {
                    if (tiempoMinutos === 0) {
                        alert('Tiempo agotado');
                        localStorage.clear();
                        window.location.href = '../../index.html';
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
    }

    // ========================================
    // HEADER BUTTONS
    // ========================================

    // Botón cerrar (X) del header - redirige al inicio
    const btnCerrar = document.getElementById('btn-cerrar');
    if (btnCerrar) {
        btnCerrar.addEventListener('click', () => {
            if (confirm('¿Deseas salir de la compra? Perderás tu selección.')) {
                localStorage.clear();
                window.location.href = '../../index.html';
            }
        });
    }

    // Botón usuario del header
    const btnUsuario = document.getElementById('btn-usuario');
    if (btnUsuario) {
        btnUsuario.addEventListener('click', () => {
            window.location.href = 'login.html';
        });
    }
});
