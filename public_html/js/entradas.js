document.addEventListener('DOMContentLoaded', () => {
    // Obtener el número de butacas reservadas desde localStorage o usar 2 como valor por defecto
    const cantButacasReservadas = parseInt(localStorage.getItem('numeroButacasSeleccionadas')) || 2;
    let cantEntradasSeleccionadas = 0;

    // Referencias a elementos del DOM
    const numeroButacasSpan = document.getElementById('numero-butacas');
    const textoResumenEntradas = document.getElementById('texto-resumen-entradas');
    const btnContinuar = document.getElementById('btn-continuar');
    const controlesCantidad = document.querySelectorAll('.control-cantidad');
    const timerDisplay = document.getElementById('timer-display');

    // Inicializar contadores en la UI
    numeroButacasSpan.textContent = cantButacasReservadas;
    textoResumenEntradas.textContent = `Entradas seleccionadas: ${cantEntradasSeleccionadas} de ${cantButacasReservadas}`;

    // Función para actualizar el resumen y estado del botón continuar
    const actualizarResumen = () => {
        textoResumenEntradas.textContent = `Entradas seleccionadas: ${cantEntradasSeleccionadas} de ${cantButacasReservadas}`;

        if (cantEntradasSeleccionadas === cantButacasReservadas) {
            btnContinuar.disabled = false;
            btnContinuar.classList.add('enabled');
        } else {
            btnContinuar.disabled = true;
            btnContinuar.classList.remove('enabled');
        }

        // Habilitar/deshabilitar todos los botones de suma si se alcanza el límite
        const allSumarButtons = document.querySelectorAll('.btn-sumar');
        if (cantEntradasSeleccionadas >= cantButacasReservadas) {
            allSumarButtons.forEach(btn => {
                // Solo deshabilita si el contador de su item es 0
                const cantidadSpan = btn.previousElementSibling;
                if (parseInt(cantidadSpan.textContent) === 0) {
                    btn.disabled = true;
                }
            });
        } else {
            allSumarButtons.forEach(btn => btn.disabled = false);
        }
    };

    // Configurar eventos para cada control de cantidad
    controlesCantidad.forEach(control => {
        const btnRestar = control.querySelector('.btn-restar');
        const btnSumar = control.querySelector('.btn-sumar');
        const cantidadSpan = control.querySelector('.cantidad');

        let cantidad = 0;

        btnSumar.addEventListener('click', () => {
            if (cantEntradasSeleccionadas < cantButacasReservadas) {
                cantidad++;
                cantEntradasSeleccionadas++;
                cantidadSpan.textContent = cantidad;
                btnRestar.disabled = false;
                actualizarResumen();
            }
        });

        btnRestar.addEventListener('click', () => {
            if (cantidad > 0) {
                cantidad--;
                cantEntradasSeleccionadas--;
                cantidadSpan.textContent = cantidad;
                if (cantidad === 0) {
                    btnRestar.disabled = true;
                }
                actualizarResumen();
            }
        });
    });

    // Cargar datos de la reserva desde localStorage
    const reservaData = JSON.parse(localStorage.getItem('reservaButacas'));

    if (reservaData) {
        document.getElementById('movie-title').textContent = reservaData.titulo;
        document.getElementById('movie-details').textContent = `${reservaData.formato}, ${reservaData.tipo}`;
        document.getElementById('cinema-name').textContent = reservaData.cine;
        document.getElementById('showtime-date').textContent = reservaData.fecha;
        document.getElementById('showtime-time').textContent = reservaData.hora;
        document.getElementById('room-name').textContent = reservaData.sala;

        // Si hay poster en los datos
        if (reservaData.imagenUrl) {
            const posterDiv = document.getElementById('movie-poster');
            if (posterDiv) {
                posterDiv.style.backgroundImage = `url('${reservaData.imagenUrl}')`;
                posterDiv.style.backgroundSize = 'cover';
            }
        }
    } else {
        // Fallback si no hay datos (para pruebas directas)
        const movieData = {
            title: "Nada es lo que Parece 3",
            details: "2D, REGULAR, DOBLADA",
            cinema: "CP Tacna",
            date: "Hoy, 14 de Nov, 2025",
            time: "20:00",
            room: "SALA 3-D"
        };
        document.getElementById('movie-title').textContent = movieData.title;
        document.getElementById('movie-details').textContent = movieData.details;
        document.getElementById('cinema-name').textContent = movieData.cinema;
        document.getElementById('showtime-date').textContent = movieData.date;
        document.getElementById('showtime-time').textContent = movieData.time;
        document.getElementById('room-name').textContent = movieData.room;
    }

    // Simular el temporizador (decrementar cada segundo)
    let timerMinutes = 4;
    let timerSeconds = 18;

    const updateTimer = () => {
        if (timerSeconds === 0) {
            if (timerMinutes === 0) {
                // Tiempo agotado
                timerDisplay.textContent = "00:00";
                return;
            }
            timerMinutes--;
            timerSeconds = 59;
        } else {
            timerSeconds--;
        }

        const displayMinutes = timerMinutes.toString().padStart(2, '0');
        const displaySeconds = timerSeconds.toString().padStart(2, '0');
        timerDisplay.textContent = `${displayMinutes}:${displaySeconds}`;
    };

    // Actualizar el temporizador cada segundo
    setInterval(updateTimer, 1000);

    // Event listener para el botón continuar
    btnContinuar.addEventListener('click', () => {
        if (!btnContinuar.disabled) {
            // Guardar número de entradas seleccionadas en localStorage
            localStorage.setItem('numeroEntradasSeleccionadas', cantEntradasSeleccionadas);

            // Calcular y guardar total de entradas
            let totalEntradas = 0;
            controlesCantidad.forEach(control => {
                const cantidad = parseInt(control.querySelector('.cantidad').textContent);
                const precio = parseFloat(control.closest('.item-entrada').getAttribute('data-precio'));
                totalEntradas += cantidad * precio;
            });
            localStorage.setItem('totalEntradas', totalEntradas.toFixed(2));

            // Redirigir a dulcería
            window.location.href = 'dulceria.html';
        }
    });

    // Event listeners para los botones del header
    const btnUsuario = document.getElementById('btn-usuario');
    const btnCerrar = document.getElementById('btn-cerrar');

    if (btnUsuario) {
        btnUsuario.addEventListener('click', () => {
            // Mostrar menú de usuario o redirigir al login
            console.log('Botón usuario clickeado');
            // window.location.href = 'login.html';
        });
    }

    if (btnCerrar) {
        btnCerrar.addEventListener('click', () => {
            // Cerrar sesión o volver al inicio
            if (confirm('¿Deseas salir de la compra?')) {
                window.location.href = 'inicio.html';
            }
        });
    }

    // Inicializar estado del botón continuar
    actualizarResumen();
});
