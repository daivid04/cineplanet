<?php
$pageTitle = 'Editar Función';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/FuncionModel.php';
require_once __DIR__ . '/../../../../src/models/PeliculaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';

$funcionModel = new FuncionModel($conn);
$peliculaModel = new PeliculaModel($conn);
$sedeModel = new SedeModel($conn);
$salaModel = new SalaModel($conn);

// Obtener ID de la función
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$funcion = $funcionModel->getById($id);

if (!$funcion) {
    header('Location: index.php');
    exit;
}

$peliculas = $peliculaModel->getAll(true);
$sedes = $sedeModel->getAll(true);
$salas = $salaModel->getAll(true);

// Verificar si la función ya pasó
$esFutura = strtotime($funcion['fecha'] . ' ' . $funcion['hora']) >= time();
$boletos = $funcion['boletos_vendidos'];

// Calcular hora fin
$horaFin = FuncionModel::calcularHoraFin($funcion['hora'], $funcion['pelicula_duracion']);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Funciones</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Función</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Función</h1>
            </div>

            <!-- Alertas de restricciones -->
            <?php if (!$esFutura): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-3xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-history text-yellow-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">Función Pasada</h3>
                        <p class="text-sm text-yellow-700">Esta función ya se realizó. Solo puedes ver la información.</p>
                    </div>
                </div>
            </div>
            <?php elseif ($boletos > 0): ?>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6 max-w-3xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-ticket-alt text-orange-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-orange-800">Función con Boletos Vendidos</h3>
                        <p class="text-sm text-orange-700">
                            Esta función tiene <?php echo $boletos; ?> boleto(s) vendido(s). 
                            No puedes cambiar la fecha, hora ni sala.
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Grid Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="formFuncion" onsubmit="return actualizarFuncion(event)">
                            <!-- Película -->
                            <div class="mb-4">
                                <label for="id_pelicula" class="block text-sm font-medium text-gray-700 mb-1">
                                    Película <span class="text-red-500">*</span>
                                </label>
                                <select id="id_pelicula" 
                                        name="id_pelicula" 
                                        required
                                        <?php echo ($boletos > 0) ? 'disabled' : ''; ?>
                                        onchange="actualizarPeliculaInfo()"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 <?php echo ($boletos > 0) ? 'bg-gray-100' : ''; ?>">
                                    <?php foreach ($peliculas as $pelicula): ?>
                                        <option value="<?php echo $pelicula['id_pelicula']; ?>"
                                                data-duracion="<?php echo $pelicula['duracion']; ?>"
                                                data-imagen="<?php echo htmlspecialchars($pelicula['url_imagen']); ?>"
                                                <?php echo $pelicula['id_pelicula'] == $funcion['id_pelicula'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pelicula['nombre']); ?> (<?php echo $pelicula['duracion']; ?> min)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($boletos > 0): ?>
                                <p class="text-xs text-orange-600 mt-1">
                                    <i class="fas fa-lock mr-1"></i>No editable por boletos vendidos
                                </p>
                                <?php endif; ?>
                            </div>

                            <!-- Sede y Sala -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sede
                                    </label>
                                    <select id="id_sede" 
                                            disabled
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">
                                        <?php foreach ($sedes as $sede): ?>
                                            <option value="<?php echo $sede['id_sede']; ?>"
                                                    <?php echo $sede['id_sede'] == $funcion['id_sede'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sede['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>La sede no se puede cambiar
                                    </p>
                                </div>

                                <div>
                                    <label for="id_sala" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sala
                                    </label>
                                    <select id="id_sala" 
                                            name="id_sala"
                                            <?php echo ($boletos > 0 || !$esFutura) ? 'disabled' : ''; ?>
                                            onchange="cargarFuncionesExistentes()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg <?php echo ($boletos > 0 || !$esFutura) ? 'bg-gray-100' : ''; ?>">
                                        <?php 
                                        $salasDeSede = array_filter($salas, fn($s) => $s['id_sede'] == $funcion['id_sede']);
                                        foreach ($salasDeSede as $sala): 
                                        ?>
                                            <option value="<?php echo $sala['id_sala']; ?>"
                                                    <?php echo $sala['id_sala'] == $funcion['id_sala'] ? 'selected' : ''; ?>>
                                                Sala <?php echo $sala['num_sala']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if ($boletos > 0): ?>
                                    <p class="text-xs text-orange-600 mt-1">
                                        <i class="fas fa-lock mr-1"></i>No editable por boletos vendidos
                                    </p>
                                    <?php endif; ?>
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
                                           value="<?php echo $funcion['fecha']; ?>"
                                           <?php echo ($boletos > 0 || !$esFutura) ? 'disabled' : ''; ?>
                                           onchange="cargarFuncionesExistentes()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg <?php echo ($boletos > 0 || !$esFutura) ? 'bg-gray-100' : ''; ?>">
                                    <?php if ($boletos > 0): ?>
                                    <p class="text-xs text-orange-600 mt-1">
                                        <i class="fas fa-lock mr-1"></i>No editable por boletos vendidos
                                    </p>
                                    <?php endif; ?>
                                </div>

                                <div>
                                    <label for="hora" class="block text-sm font-medium text-gray-700 mb-1">
                                        Hora <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" 
                                           id="hora" 
                                           name="hora" 
                                           required
                                           value="<?php echo substr($funcion['hora'], 0, 5); ?>"
                                           <?php echo ($boletos > 0 || !$esFutura) ? 'disabled' : ''; ?>
                                           onchange="verificarConflicto()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg <?php echo ($boletos > 0 || !$esFutura) ? 'bg-gray-100' : ''; ?>">
                                    <?php if ($boletos > 0): ?>
                                    <p class="text-xs text-orange-600 mt-1">
                                        <i class="fas fa-lock mr-1"></i>No editable por boletos vendidos
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Funciones existentes en la sala/fecha -->
                            <div id="funcionesExistentes" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-day text-gray-500 mr-1"></i>
                                    Otras funciones ese día:
                                </h3>
                                <div id="listaFuncionesExistentes" class="space-y-2">
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

                            <?php if ($esFutura && $boletos == 0): ?>
                            <div class="flex gap-3">
                                <button type="submit" 
                                        id="btnGuardar"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    Guardar Cambios
                                </button>
                                <a href="index.php" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>
                            </div>
                            <?php else: ?>
                            <div class="flex gap-3">
                                <a href="index.php" 
                                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    Volver
                                </a>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                        <!-- Poster -->
                        <div class="mb-4">
                            <img src="<?php echo htmlspecialchars($funcion['pelicula_imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($funcion['pelicula_nombre']); ?>"
                                 class="w-full h-48 object-cover rounded-lg"
                                 onerror="this.src='https://via.placeholder.com/300x200?text=Sin+Imagen'">
                        </div>

                        <!-- Info -->
                        <div class="space-y-3 mb-4">
                            <div>
                                <p class="text-xs text-gray-500">Película</p>
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($funcion['pelicula_nombre']); ?></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Fecha</p>
                                    <p class="font-medium text-gray-800"><?php echo date('d/m/Y', strtotime($funcion['fecha'])); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Horario</p>
                                    <p class="font-medium text-gray-800"><?php echo substr($funcion['hora'], 0, 5); ?> - <?php echo $horaFin; ?></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <p class="text-xs text-gray-500">Sede</p>
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($funcion['sede_nombre']); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Sala</p>
                                    <p class="font-medium text-gray-800">Sala <?php echo $funcion['numero_sala']; ?></p>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Duración</p>
                                <p class="font-medium text-gray-800"><?php echo FuncionModel::formatDuration($funcion['pelicula_duracion']); ?></p>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="border-t pt-4 space-y-3">
                            <h3 class="font-semibold text-gray-700">Estadísticas</h3>
                            
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-blue-600">Boletos Vendidos</span>
                                    <span class="text-xl font-bold text-blue-800"><?php echo $boletos; ?></span>
                                </div>
                            </div>

                            <div class="<?php echo $esFutura ? 'bg-green-50' : 'bg-gray-50'; ?> p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm <?php echo $esFutura ? 'text-green-600' : 'text-gray-600'; ?>">Estado</span>
                                    <span class="text-sm font-bold <?php echo $esFutura ? 'text-green-800' : 'text-gray-800'; ?>">
                                        <?php echo $esFutura ? ($funcion['estado'] ? 'Activa' : 'Inactiva') : 'Pasada'; ?>
                                    </span>
                                </div>
                            </div>

                            <div class="bg-purple-50 p-3 rounded-lg">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-purple-600">ID Función</span>
                                    <span class="text-xl font-bold text-purple-800">#<?php echo $funcion['id_funcion']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            const API_URL = '../../../api/funcion_api.php';
            const funcionId = <?php echo $id; ?>;
            const boletosVendidos = <?php echo $boletos; ?>;
            const esFutura = <?php echo $esFutura ? 'true' : 'false'; ?>;
            
            let peliculasData = <?php echo json_encode($peliculas); ?>;
            let salasData = <?php echo json_encode(array_values($salasDeSede)); ?>;
            let funcionesEnSala = [];
            let hayConflicto = false;

            // Cargar funciones existentes
            async function cargarFuncionesExistentes() {
                const salaId = document.getElementById('id_sala').value;
                const fecha = document.getElementById('fecha').value;

                if (!salaId || !fecha) {
                    document.getElementById('funcionesExistentes').classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(`${API_URL}?accion=con_filtros&fecha=${fecha}`);
                    const result = await response.json();

                    if (result.success) {
                        // Excluir la función actual
                        funcionesEnSala = result.data.filter(f => f.id_sala == salaId && f.estado == 1 && f.id_funcion != funcionId);
                        
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
                            </div>
                        </div>
                    `;
                }).join('');
                
                document.getElementById('funcionesExistentes').classList.remove('hidden');
            }

            function verificarConflicto() {
                const hora = document.getElementById('hora').value;
                const idPelicula = document.getElementById('id_pelicula').value;
                
                if (!hora || !idPelicula || funcionesEnSala.length === 0) {
                    document.getElementById('alertaConflicto').classList.add('hidden');
                    hayConflicto = false;
                    return;
                }

                const pelicula = peliculasData.find(p => p.id_pelicula == idPelicula);
                const duracionNueva = parseInt(pelicula.duracion);
                
                const horaInicioNueva = timeToMinutes(hora);
                const horaFinNueva = horaInicioNueva + duracionNueva;

                hayConflicto = false;

                for (const funcion of funcionesEnSala) {
                    const horaInicioExistente = timeToMinutes(funcion.hora);
                    const horaFinExistente = horaInicioExistente + parseInt(funcion.pelicula_duracion);

                    if (horaInicioNueva < horaFinExistente && horaFinNueva > horaInicioExistente) {
                        hayConflicto = true;
                        document.getElementById('mensajeConflicto').textContent = 
                            `Se superpone con "${funcion.pelicula_nombre}" (${funcion.hora.substring(0,5)} - ${calcularHoraFin(funcion.hora, funcion.pelicula_duracion)})`;
                        break;
                    }
                }

                document.getElementById('alertaConflicto').classList.toggle('hidden', !hayConflicto);
            }

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

            async function actualizarFuncion(event) {
                event.preventDefault();

                if (hayConflicto) {
                    mostrarAlerta('Hay un conflicto de horarios', 'error');
                    return false;
                }

                const data = {};
                
                // Solo enviar campos editables
                if (boletosVendidos === 0) {
                    data.id_pelicula = parseInt(document.getElementById('id_pelicula').value);
                    data.id_sala = parseInt(document.getElementById('id_sala').value);
                    data.fecha = document.getElementById('fecha').value;
                    data.hora = document.getElementById('hora').value;
                }

                const btnGuardar = document.getElementById('btnGuardar');
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

                try {
                    const response = await fetch(`${API_URL}?id=${funcionId}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        mostrarAlerta('Función actualizada exitosamente', 'success');
                        setTimeout(() => window.location.href = 'index.php', 1500);
                    } else {
                        mostrarAlerta(result.message, 'error');
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al guardar', 'error');
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
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

            // Cargar funciones existentes al inicio
            <?php if ($esFutura && $boletos == 0): ?>
            document.addEventListener('DOMContentLoaded', cargarFuncionesExistentes);
            <?php endif; ?>
        </script>

<?php include '../../partials/footer.php'; ?>
