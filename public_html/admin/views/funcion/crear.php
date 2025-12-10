<?php
$pageTitle = 'Nueva Función';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/PeliculaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';

$peliculaModel = new PeliculaModel($conn);
$sedeModel = new SedeModel($conn);
$salaModel = new SalaModel($conn);

$peliculas = $peliculaModel->getAll(true);
$sedes = $sedeModel->getAll(true);
$salas = $salaModel->getAll(true);

// Pre-seleccionar si viene por parámetro
$peliculaPreseleccionada = isset($_GET['pelicula']) ? intval($_GET['pelicula']) : 0;
$salaPreseleccionada = isset($_GET['sala']) ? intval($_GET['sala']) : 0;
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Funciones</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nueva Función</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nueva Función</h1>
                <p class="text-gray-600">Programa una nueva función de cine</p>
            </div>

            <?php if (empty($peliculas)): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-3xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">No hay películas disponibles</h3>
                        <p class="text-sm text-yellow-700">Debes crear al menos una película activa antes de programar funciones.</p>
                        <a href="../pelicula/crear.php" class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                            <i class="fas fa-plus mr-1"></i> Crear nueva película
                        </a>
                    </div>
                </div>
            </div>
            <?php elseif (empty($salas)): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-3xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">No hay salas disponibles</h3>
                        <p class="text-sm text-yellow-700">Debes crear al menos una sala activa antes de programar funciones.</p>
                        <a href="../sala/crear.php" class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                            <i class="fas fa-plus mr-1"></i> Crear nueva sala
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>

            <!-- Grid Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="formFuncion" onsubmit="return guardarFuncion(event)">
                            <!-- Película -->
                            <div class="mb-4">
                                <label for="id_pelicula" class="block text-sm font-medium text-gray-700 mb-1">
                                    Película <span class="text-red-500">*</span>
                                </label>
                                <select id="id_pelicula" 
                                        name="id_pelicula" 
                                        required
                                        onchange="actualizarPeliculaInfo()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccione una película</option>
                                    <?php foreach ($peliculas as $pelicula): ?>
                                        <option value="<?php echo $pelicula['id_pelicula']; ?>"
                                                data-duracion="<?php echo $pelicula['duracion']; ?>"
                                                data-imagen="<?php echo htmlspecialchars($pelicula['url_imagen']); ?>"
                                                <?php echo $pelicula['id_pelicula'] == $peliculaPreseleccionada ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pelicula['nombre']); ?> (<?php echo $pelicula['duracion']; ?> min)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Sede y Sala -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sede <span class="text-red-500">*</span>
                                    </label>
                                    <select id="id_sede" 
                                            required
                                            onchange="cargarSalasPorSede()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione una sede</option>
                                        <?php foreach ($sedes as $sede): ?>
                                            <option value="<?php echo $sede['id_sede']; ?>">
                                                <?php echo htmlspecialchars($sede['nombre']); ?> - <?php echo htmlspecialchars($sede['ciudad_nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="id_sala" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sala <span class="text-red-500">*</span>
                                    </label>
                                    <select id="id_sala" 
                                            name="id_sala" 
                                            required
                                            onchange="cargarFuncionesExistentes()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione primero una sede</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">
                                        Fecha <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" 
                                           id="fecha" 
                                           name="fecha" 
                                           required
                                           min="<?php echo date('Y-m-d'); ?>"
                                           onchange="cargarFuncionesExistentes()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="hora" class="block text-sm font-medium text-gray-700 mb-1">
                                        Hora <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" 
                                           id="hora" 
                                           name="hora" 
                                           required
                                           onchange="verificarConflicto()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Funciones existentes en la sala/fecha -->
                            <div id="funcionesExistentes" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-day text-gray-500 mr-1"></i>
                                    Funciones programadas ese día:
                                </h3>
                                <div id="listaFuncionesExistentes" class="space-y-2">
                                    <!-- Se llena con JavaScript -->
                                </div>
                            </div>

                            <!-- Alerta de conflicto -->
                            <div id="alertaConflicto" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                                    <div>
                                        <h3 class="font-semibold text-red-800">Conflicto de Horario</h3>
                                        <p id="mensajeConflicto" class="text-sm text-red-700"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h3 class="text-sm font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-info-circle"></i> Información
                                </h3>
                                <div class="text-xs text-blue-800 space-y-1">
                                    <p>• La función ocupará la sala durante toda la duración de la película</p>
                                    <p>• No se pueden programar funciones que se superpongan en horario</p>
                                    <p>• Se recomienda dejar al menos 15-30 minutos entre funciones para limpieza</p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" 
                                        id="btnGuardar"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    Guardar
                                </button>
                                <a href="index.php" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Vista Previa -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Vista Previa</h2>
                        
                        <!-- Poster película -->
                        <div id="previewPoster" class="mb-4">
                            <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center">
                                <i class="fas fa-film text-4xl text-gray-300"></i>
                            </div>
                        </div>

                        <!-- Info función -->
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Película</p>
                                <p id="previewPelicula" class="font-semibold text-gray-800">-</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Fecha</p>
                                    <p id="previewFecha" class="font-medium text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Horario</p>
                                    <p id="previewHorario" class="font-medium text-gray-800">-</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Sede</p>
                                    <p id="previewSede" class="font-medium text-gray-800">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Sala</p>
                                    <p id="previewSala" class="font-medium text-gray-800">-</p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Duración</p>
                                <p id="previewDuracion" class="font-medium text-gray-800">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>

        <script>
            const API_URL = '../../../api/funcion_api.php';
            const SALA_API_URL = '../../../api/sala_api.php';
            
            let salasData = <?php echo json_encode($salas); ?>;
            let peliculasData = <?php echo json_encode($peliculas); ?>;
            let funcionesEnSala = [];
            let hayConflicto = false;

            // Actualizar info de película seleccionada
            function actualizarPeliculaInfo() {
                const select = document.getElementById('id_pelicula');
                const option = select.options[select.selectedIndex];
                
                if (select.value) {
                    const duracion = option.dataset.duracion;
                    const imagen = option.dataset.imagen;
                    
                    document.getElementById('previewPelicula').textContent = option.text.split(' (')[0];
                    document.getElementById('previewDuracion').textContent = formatDuration(duracion);
                    
                    document.getElementById('previewPoster').innerHTML = `
                        <img src="${imagen}" alt="Poster" class="w-full h-48 object-cover rounded-lg"
                             onerror="this.parentElement.innerHTML='<div class=\\'bg-gray-100 rounded-lg h-48 flex items-center justify-center\\'><i class=\\'fas fa-film text-4xl text-gray-300\\'></i></div>'">
                    `;
                } else {
                    document.getElementById('previewPelicula').textContent = '-';
                    document.getElementById('previewDuracion').textContent = '-';
                    document.getElementById('previewPoster').innerHTML = `
                        <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center">
                            <i class="fas fa-film text-4xl text-gray-300"></i>
                        </div>
                    `;
                }
                
                verificarConflicto();
            }

            // Cargar salas por sede
            function cargarSalasPorSede() {
                const sedeId = document.getElementById('id_sede').value;
                const selectSala = document.getElementById('id_sala');
                
                selectSala.innerHTML = '<option value="">Seleccione una sala</option>';
                
                if (sedeId) {
                    const salasFiltradas = salasData.filter(s => s.id_sede == sedeId);
                    
                    salasFiltradas.forEach(sala => {
                        const option = document.createElement('option');
                        option.value = sala.id_sala;
                        option.textContent = `Sala ${sala.num_sala}`;
                        selectSala.appendChild(option);
                    });

                    const sede = <?php echo json_encode($sedes); ?>.find(s => s.id_sede == sedeId);
                    document.getElementById('previewSede').textContent = sede ? sede.nombre : '-';
                } else {
                    document.getElementById('previewSede').textContent = '-';
                }
                
                document.getElementById('previewSala').textContent = '-';
                document.getElementById('funcionesExistentes').classList.add('hidden');
            }

            // Cargar funciones existentes en la sala/fecha
            async function cargarFuncionesExistentes() {
                const salaId = document.getElementById('id_sala').value;
                const fecha = document.getElementById('fecha').value;
                
                if (salaId) {
                    const sala = salasData.find(s => s.id_sala == salaId);
                    document.getElementById('previewSala').textContent = sala ? `Sala ${sala.num_sala}` : '-';
                }
                
                if (fecha) {
                    const fechaObj = new Date(fecha + 'T00:00:00');
                    document.getElementById('previewFecha').textContent = fechaObj.toLocaleDateString('es-ES', {
                        weekday: 'long', day: 'numeric', month: 'short'
                    });
                }

                if (!salaId || !fecha) {
                    document.getElementById('funcionesExistentes').classList.add('hidden');
                    funcionesEnSala = [];
                    return;
                }

                try {
                    // Usar el API de funciones con filtros
                    const response = await fetch(`${API_URL}?accion=con_filtros&fecha=${fecha}`);
                    const result = await response.json();

                    if (result.success) {
                        funcionesEnSala = result.data.filter(f => f.id_sala == salaId && f.estado == 1);
                        
                        if (funcionesEnSala.length > 0) {
                            mostrarFuncionesExistentes();
                        } else {
                            document.getElementById('funcionesExistentes').classList.add('hidden');
                        }
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
                
                verificarConflicto();
            }

            // Mostrar funciones existentes
            function mostrarFuncionesExistentes() {
                const container = document.getElementById('listaFuncionesExistentes');
                container.innerHTML = funcionesEnSala.map(f => {
                    const horaInicio = f.hora.substring(0, 5);
                    const horaFin = calcularHoraFin(f.hora, f.pelicula_duracion);
                    return `
                        <div class="flex items-center gap-3 bg-white p-2 rounded border">
                            <div class="w-16 text-center">
                                <span class="font-bold text-blue-600">${horaInicio}</span>
                                <span class="text-xs text-gray-400 block">a ${horaFin}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium">${f.pelicula_nombre}</p>
                                <p class="text-xs text-gray-500">${f.pelicula_duracion} min</p>
                            </div>
                        </div>
                    `;
                }).join('');
                
                document.getElementById('funcionesExistentes').classList.remove('hidden');
            }

            // Verificar conflicto de horarios
            function verificarConflicto() {
                const hora = document.getElementById('hora').value;
                const idPelicula = document.getElementById('id_pelicula').value;
                
                if (!hora || !idPelicula || funcionesEnSala.length === 0) {
                    document.getElementById('alertaConflicto').classList.add('hidden');
                    hayConflicto = false;
                    actualizarPreviewHorario();
                    return;
                }

                const pelicula = peliculasData.find(p => p.id_pelicula == idPelicula);
                const duracionNueva = parseInt(pelicula.duracion);
                
                const horaInicioNueva = timeToMinutes(hora);
                const horaFinNueva = horaInicioNueva + duracionNueva;

                hayConflicto = false;
                let conflictoMsg = '';

                for (const funcion of funcionesEnSala) {
                    const horaInicioExistente = timeToMinutes(funcion.hora);
                    const horaFinExistente = horaInicioExistente + parseInt(funcion.pelicula_duracion);

                    if (horaInicioNueva < horaFinExistente && horaFinNueva > horaInicioExistente) {
                        hayConflicto = true;
                        conflictoMsg = `Se superpone con "${funcion.pelicula_nombre}" (${funcion.hora.substring(0,5)} - ${calcularHoraFin(funcion.hora, funcion.pelicula_duracion)})`;
                        break;
                    }
                }

                if (hayConflicto) {
                    document.getElementById('mensajeConflicto').textContent = conflictoMsg;
                    document.getElementById('alertaConflicto').classList.remove('hidden');
                } else {
                    document.getElementById('alertaConflicto').classList.add('hidden');
                }

                actualizarPreviewHorario();
            }

            // Actualizar preview de horario
            function actualizarPreviewHorario() {
                const hora = document.getElementById('hora').value;
                const idPelicula = document.getElementById('id_pelicula').value;
                
                if (hora && idPelicula) {
                    const pelicula = peliculasData.find(p => p.id_pelicula == idPelicula);
                    const horaFin = calcularHoraFin(hora + ':00', pelicula.duracion);
                    document.getElementById('previewHorario').textContent = `${hora} - ${horaFin}`;
                } else if (hora) {
                    document.getElementById('previewHorario').textContent = hora;
                } else {
                    document.getElementById('previewHorario').textContent = '-';
                }
            }

            // Helpers
            function timeToMinutes(timeStr) {
                const [hours, minutes] = timeStr.split(':').map(Number);
                return hours * 60 + minutes;
            }

            function calcularHoraFin(hora, duracion) {
                const [h, m] = hora.split(':').map(Number);
                const totalMinutos = h * 60 + m + parseInt(duracion);
                const horaFin = Math.floor(totalMinutos / 60);
                const minFin = totalMinutos % 60;
                return `${String(horaFin).padStart(2, '0')}:${String(minFin).padStart(2, '0')}`;
            }

            function formatDuration(mins) {
                const h = Math.floor(mins / 60);
                const m = mins % 60;
                if (h > 0 && m > 0) return `${h}h ${m}min`;
                if (h > 0) return `${h}h`;
                return `${m}min`;
            }

            // Guardar función
            async function guardarFuncion(event) {
                event.preventDefault();

                if (hayConflicto) {
                    mostrarAlerta('Hay un conflicto de horarios. Elige otra hora.', 'error');
                    return false;
                }

                const data = {
                    id_pelicula: parseInt(document.getElementById('id_pelicula').value),
                    id_sala: parseInt(document.getElementById('id_sala').value),
                    fecha: document.getElementById('fecha').value,
                    hora: document.getElementById('hora').value
                };

                const btnGuardar = document.getElementById('btnGuardar');
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        mostrarAlerta('Función creada exitosamente', 'success');
                        setTimeout(() => window.location.href = 'index.php', 1500);
                    } else {
                        mostrarAlerta(result.message, 'error');
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al guardar', 'error');
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar';
                }

                return false;
            }

            function mostrarAlerta(mensaje, tipo) {
                const color = tipo === 'success' ? 'green' : 'red';
                const alerta = document.createElement('div');
                alerta.className = `fixed top-4 right-4 bg-${color}-100 border border-${color}-400 text-${color}-700 px-4 py-3 rounded-lg shadow-lg z-50`;
                alerta.innerHTML = `<span>${mensaje}</span>`;
                document.body.appendChild(alerta);
                setTimeout(() => alerta.remove(), 3000);
            }

            // Inicializar
            document.addEventListener('DOMContentLoaded', () => {
                actualizarPeliculaInfo();
            });
        </script>

<?php include '../../partials/footer.php'; ?>
