<?php
$pageTitle = 'Configurar Asientos';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/AsientoModel.php';

$salaModel = new SalaModel($conn);
$sedeModel = new SedeModel($conn);
$asientoModel = new AsientoModel($conn);

$sedes = $sedeModel->getAll(true);
$salas = $salaModel->getAll(true);

// Pre-seleccionar sala si viene por parámetro
$salaPreseleccionada = isset($_GET['sala']) ? intval($_GET['sala']) : 0;
$salaInfo = null;
$asientosExistentes = 0;

if ($salaPreseleccionada > 0) {
    $salaInfo = $salaModel->getById($salaPreseleccionada);
    if ($salaInfo) {
        $asientosExistentes = $asientoModel->countBySala($salaPreseleccionada);
    }
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Asientos</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Configurar Sala</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Configurar Asientos de Sala</h1>
                <p class="text-gray-600">Genera automáticamente la distribución de asientos</p>
            </div>

            <?php if (empty($salas)): ?>
            <!-- Alerta si no hay salas -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 max-w-3xl">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    <div>
                        <h3 class="font-semibold text-yellow-800">No hay salas disponibles</h3>
                        <p class="text-sm text-yellow-700">Debes crear al menos una sala activa antes de configurar asientos.</p>
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
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Configuración</h2>
                        
                        <form id="formAsientos" onsubmit="return generarAsientos(event)">
                            <!-- Seleccionar Sede y Sala -->
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
                                            onchange="verificarAsientosExistentes()"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione una sala</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Configuración de filas y columnas -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="filas" class="block text-sm font-medium text-gray-700 mb-1">
                                        Número de Filas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="filas" 
                                           name="filas" 
                                           required
                                           min="1"
                                           max="26"
                                           value="10"
                                           onchange="actualizarVistaPrevia()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: 10">
                                    <p class="mt-1 text-xs text-gray-500">De 1 a 26 filas (A-Z)</p>
                                </div>

                                <div>
                                    <label for="columnas" class="block text-sm font-medium text-gray-700 mb-1">
                                        Número de Columnas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="columnas" 
                                           name="columnas" 
                                           required
                                           min="1"
                                           max="50"
                                           value="12"
                                           onchange="actualizarVistaPrevia()"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: 12">
                                    <p class="mt-1 text-xs text-gray-500">De 1 a 50 columnas</p>
                                </div>
                            </div>

                            <!-- Alerta de asientos existentes -->
                            <div id="alertaExistentes" class="hidden bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-exclamation-triangle text-red-600 mt-1"></i>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-red-800 mb-1">Advertencia</h3>
                                        <p class="text-sm text-red-700">Esta sala ya tiene <span id="cantidadExistentes" class="font-bold"></span> asientos configurados.</p>
                                        <p class="text-sm text-red-700 mt-1">Al generar nuevos asientos, se eliminarán todos los asientos existentes.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Información -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                                <h3 class="text-sm font-semibold text-blue-900 mb-2">
                                    <i class="fas fa-info-circle"></i> Información
                                </h3>
                                <div class="text-xs text-blue-800 space-y-1">
                                    <p>• Las filas se identifican con letras (A, B, C... Z)</p>
                                    <p>• Las columnas se numeran del 1 hasta el número especificado</p>
                                    <p>• Ejemplo: Fila B, Columna 5 = Asiento B5</p>
                                    <p>• Puedes activar/desactivar asientos individuales después de generarlos</p>
                                </div>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" 
                                        id="btnGenerar"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                    <i class="fas fa-cog"></i>
                                    Generar Asientos
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
                        
                        <!-- Pantalla -->
                        <div class="flex flex-col items-center mb-4">
                            <div class="w-full h-8 bg-gradient-to-b from-gray-700 to-gray-900 rounded-t-2xl flex items-center justify-center mb-1">
                                <span class="text-white text-xs font-bold uppercase tracking-wider">PANTALLA</span>
                            </div>
                            <div class="w-full h-1 bg-gray-300 rounded-b-sm"></div>
                        </div>

                        <!-- Grid Preview -->
                        <div id="vistaPrevia" class="bg-gray-50 rounded-lg p-4 overflow-auto max-h-96">
                            <div class="text-center text-gray-400 py-8">
                                <i class="fas fa-couch text-4xl mb-2"></i>
                                <p class="text-sm">Configura filas y columnas</p>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="bg-gray-50 p-2 rounded">
                                    <div class="text-gray-600 text-xs">Filas</div>
                                    <div class="font-bold text-gray-800"><span id="previewFilas">0</span></div>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <div class="text-gray-600 text-xs">Columnas</div>
                                    <div class="font-bold text-gray-800"><span id="previewColumnas">0</span></div>
                                </div>
                                <div class="bg-blue-50 p-2 rounded col-span-2">
                                    <div class="text-blue-600 text-xs">Total Asientos</div>
                                    <div class="font-bold text-blue-800 text-lg"><span id="previewTotal">0</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>

        <script>
            const API_URL = '../../../api/asiento_api.php';
            const SALA_API_URL = '../../../api/sala_api.php';
            
            let salasData = <?php echo json_encode($salas); ?>;
            let asientosExistentesPorSala = {};

            // Pre-cargar sala si viene por parámetro
            <?php if ($salaPreseleccionada > 0 && $salaInfo): ?>
            document.addEventListener('DOMContentLoaded', () => {
                const sede = <?php echo $salaInfo['id_sede']; ?>;
                document.getElementById('id_sede').value = sede;
                cargarSalasPorSede();
                setTimeout(() => {
                    document.getElementById('id_sala').value = <?php echo $salaPreseleccionada; ?>;
                    verificarAsientosExistentes();
                }, 100);
            });
            <?php endif; ?>

            // Cargar salas por sede
            function cargarSalasPorSede() {
                const sedeId = document.getElementById('id_sede').value;
                const selectSala = document.getElementById('id_sala');
                
                selectSala.innerHTML = '<option value="">Seleccione una sala</option>';
                
                const salasFiltradas = sedeId 
                    ? salasData.filter(s => s.id_sede == sedeId)
                    : salasData;
                
                salasFiltradas.forEach(sala => {
                    const option = document.createElement('option');
                    option.value = sala.id_sala;
                    option.textContent = `Sala ${sala.num_sala}`;
                    selectSala.appendChild(option);
                });

                document.getElementById('alertaExistentes').classList.add('hidden');
            }

            // Verificar asientos existentes
            async function verificarAsientosExistentes() {
                const salaId = document.getElementById('id_sala').value;
                
                if (!salaId) {
                    document.getElementById('alertaExistentes').classList.add('hidden');
                    return;
                }

                try {
                    const response = await fetch(`${API_URL}?sala=${salaId}`);
                    const result = await response.json();

                    if (result.success && result.data.length > 0) {
                        asientosExistentesPorSala[salaId] = result.data.length;
                        document.getElementById('cantidadExistentes').textContent = result.data.length;
                        document.getElementById('alertaExistentes').classList.remove('hidden');
                        document.getElementById('btnGenerar').innerHTML = '<i class="fas fa-sync-alt"></i> Regenerar Asientos';
                    } else {
                        document.getElementById('alertaExistentes').classList.add('hidden');
                        document.getElementById('btnGenerar').innerHTML = '<i class="fas fa-cog"></i> Generar Asientos';
                    }
                } catch (error) {
                    console.error('Error:', error);
                }

                actualizarVistaPrevia();
            }

            // Actualizar vista previa
            function actualizarVistaPrevia() {
                const filas = parseInt(document.getElementById('filas').value) || 0;
                const columnas = parseInt(document.getElementById('columnas').value) || 0;
                
                document.getElementById('previewFilas').textContent = filas;
                document.getElementById('previewColumnas').textContent = columnas;
                document.getElementById('previewTotal').textContent = filas * columnas;

                // Generar preview visual simplificado
                const container = document.getElementById('vistaPrevia');
                
                if (filas === 0 || columnas === 0) {
                    container.innerHTML = `
                        <div class="text-center text-gray-400 py-8">
                            <i class="fas fa-couch text-4xl mb-2"></i>
                            <p class="text-sm">Configura filas y columnas</p>
                        </div>
                    `;
                    return;
                }

                let html = '<div class="space-y-1">';
                
                // Limitar preview a máximo 10 filas para no sobrecargar
                const filasPreview = Math.min(filas, 10);
                const mostrarMas = filas > 10;
                
                for (let i = 0; i < filasPreview; i++) {
                    const letra = String.fromCharCode(65 + i); // A, B, C...
                    html += `<div class="flex gap-1 items-center justify-center">`;
                    html += `<span class="text-xs font-bold text-gray-500 w-4">${letra}</span>`;
                    
                    // Limitar preview de columnas
                    const columnasPreview = Math.min(columnas, 12);
                    for (let j = 0; j < columnasPreview; j++) {
                        html += `<div class="w-4 h-4 bg-green-500 rounded"></div>`;
                    }
                    
                    if (columnas > 12) {
                        html += `<span class="text-xs text-gray-400">...</span>`;
                    }
                    
                    html += `</div>`;
                }
                
                if (mostrarMas) {
                    html += `<div class="text-center text-xs text-gray-400 mt-2">... ${filas - 10} filas más</div>`;
                }
                
                html += '</div>';
                container.innerHTML = html;
            }

            // Generar asientos
            async function generarAsientos(event) {
                event.preventDefault();
                
                const salaId = document.getElementById('id_sala').value;
                const filas = parseInt(document.getElementById('filas').value);
                const columnas = parseInt(document.getElementById('columnas').value);

                // Confirmación si hay asientos existentes
                if (asientosExistentesPorSala[salaId] > 0) {
                    if (!confirm(`Esta sala tiene ${asientosExistentesPorSala[salaId]} asientos existentes.\n\n¿Estás seguro de regenerar? Se eliminarán todos los asientos actuales.`)) {
                        return false;
                    }
                }

                const btnGenerar = document.getElementById('btnGenerar');
                const textoOriginal = btnGenerar.innerHTML;
                btnGenerar.disabled = true;
                btnGenerar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

                try {
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            generar: true,
                            id_sala: parseInt(salaId),
                            filas: filas,
                            columnas: columnas
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        mostrarAlerta('Asientos generados exitosamente', 'success');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        mostrarAlerta(result.message, 'error');
                        btnGenerar.disabled = false;
                        btnGenerar.innerHTML = textoOriginal;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al generar asientos', 'error');
                    btnGenerar.disabled = false;
                    btnGenerar.innerHTML = textoOriginal;
                }

                return false;
            }

            // Mostrar alertas
            function mostrarAlerta(mensaje, tipo) {
                const color = tipo === 'success' ? 'green' : tipo === 'error' ? 'red' : 'yellow';
                const alerta = document.createElement('div');
                alerta.className = `fixed top-4 right-4 bg-${color}-100 border border-${color}-400 text-${color}-700 px-4 py-3 rounded-lg shadow-lg z-50`;
                alerta.innerHTML = `<span class="block sm:inline">${mensaje}</span>`;
                document.body.appendChild(alerta);
                setTimeout(() => alerta.remove(), 3000);
            }

            // Inicializar vista previa
            actualizarVistaPrevia();
        </script>

<?php include '../../partials/footer.php'; ?>
