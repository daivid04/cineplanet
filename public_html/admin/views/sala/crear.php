<?php
$pageTitle = 'Nueva Sala';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';

$sedeModel = new SedeModel($conn);
$salaModel = new SalaModel($conn);

$sedes = $sedeModel->getAll(true); // Solo sedes activas

// Pre-seleccionar sede si viene por parametro
$sedePreseleccionada = isset($_GET['sede']) ? intval($_GET['sede']) : 0;
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Salas</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nueva Sala</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nueva Sala</h1>
            </div>

            <?php if (empty($sedes)): ?>
            <!-- Alerta si no hay sedes -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-2xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">No hay sedes disponibles</h3>
                        <p class="text-sm text-yellow-700">Debes crear al menos una sede activa antes de poder agregar salas.</p>
                        <a href="../sede/crear.php" class="text-blue-600 hover:underline text-sm mt-1 inline-block">
                            <i class="fas fa-plus mr-1"></i> Crear nueva sede
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formSala" onsubmit="return guardarSala(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-1">
                                Sede <span class="text-red-500">*</span>
                            </label>
                            <select id="id_sede" 
                                    name="id_sede" 
                                    required
                                    onchange="actualizarInfoSede()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione una sede</option>
                                <?php foreach ($sedes as $sede): ?>
                                    <option value="<?php echo $sede['id_sede']; ?>"
                                            data-ciudad="<?php echo htmlspecialchars($sede['ciudad_nombre']); ?>"
                                            <?php echo $sede['id_sede'] == $sedePreseleccionada ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sede['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p id="infoCiudad" class="mt-1 text-xs text-gray-500"></p>
                        </div>

                        <div>
                            <label for="num_sala" class="block text-sm font-medium text-gray-700 mb-1">
                                Numero de Sala <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="num_sala" 
                                   name="num_sala" 
                                   required
                                   min="1"
                                   max="99"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: 1, 2, 3...">
                            <p class="mt-1 text-xs text-gray-500">Numero del 1 al 99</p>
                        </div>
                    </div>

                    <!-- Salas existentes en la sede -->
                    <div id="salasExistentes" class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4 hidden">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-door-open text-gray-500 mr-1"></i>
                            Salas existentes en esta sede:
                        </h3>
                        <div id="listaSalasExistentes" class="flex flex-wrap gap-2">
                            <!-- Se llena con JavaScript -->
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle"></i> Informacion
                        </h3>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p>• Cada sala debe tener un numero unico dentro de la misma sede</p>
                            <p>• Puede haber Sala 1 en diferentes sedes</p>
                            <p>• Una vez creada la sala, podras configurar los asientos</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" 
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
            <?php endif; ?>
        </main>

        <script>
            // Cargar info de la sede seleccionada
            function actualizarInfoSede() {
                const select = document.getElementById('id_sede');
                const option = select.options[select.selectedIndex];
                const ciudad = option.dataset.ciudad || '';
                
                document.getElementById('infoCiudad').textContent = ciudad ? `Ciudad: ${ciudad}` : '';
                
                if (select.value) {
                    cargarSalasExistentes(select.value);
                } else {
                    document.getElementById('salasExistentes').classList.add('hidden');
                }
            }

            // Cargar salas existentes de la sede
            async function cargarSalasExistentes(sedeId) {
                try {
                    const response = await fetch(`../../../api/sala_api.php?sede=${sedeId}`);
                    const data = await response.json();
                    
                    const container = document.getElementById('salasExistentes');
                    const lista = document.getElementById('listaSalasExistentes');
                    
                    if (data.success && data.data.length > 0) {
                        lista.innerHTML = data.data.map(sala => `
                            <span class="px-3 py-1 rounded-full text-sm font-medium 
                                ${sala.estado == 1 ? 'bg-blue-100 text-blue-800' : 'bg-gray-200 text-gray-600'}">
                                Sala ${sala.num_sala}
                                ${sala.estado != 1 ? '<i class="fas fa-eye-slash text-xs ml-1" title="Inactiva"></i>' : ''}
                            </span>
                        `).join('');
                        container.classList.remove('hidden');
                    } else {
                        lista.innerHTML = '<span class="text-sm text-gray-500">No hay salas en esta sede</span>';
                        container.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error al cargar salas:', error);
                }
            }

            // Guardar sala
            function guardarSala(event) {
                event.preventDefault();
                
                const numSala = document.getElementById('num_sala').value;
                const idSede = document.getElementById('id_sede').value;
                
                if (!idSede) {
                    showAlert('Debe seleccionar una sede', 'error');
                    return false;
                }

                if (!numSala || numSala < 1) {
                    showAlert('El numero de sala es requerido y debe ser mayor a 0', 'error');
                    return false;
                }
                
                const data = { 
                    num_sala: parseInt(numSala),
                    id_sede: parseInt(idSede)
                };
                
                fetch('../../../api/sala_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    showAlert('Error al guardar: ' + error.message, 'error');
                });
                
                return false;
            }

            // Inicializar si hay sede preseleccionada
            document.addEventListener('DOMContentLoaded', function() {
                const sedeSelect = document.getElementById('id_sede');
                if (sedeSelect && sedeSelect.value) {
                    actualizarInfoSede();
                }
            });
        </script>

<?php include '../../partials/footer.php'; ?>
