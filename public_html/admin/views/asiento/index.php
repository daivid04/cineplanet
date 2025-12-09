<?php
$pageTitle = 'Asientos';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/AsientoModel.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';

$asientoModel = new AsientoModel($conn);
$salaModel = new SalaModel($conn);
$sedeModel = new SedeModel($conn);

$sedes = $sedeModel->getAll(true);
$salas = $salaModel->getAll(true);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Asientos</h1>
                    <p class="text-gray-600">Gestiona la configuración de asientos por sala</p>
                </div>
                <a href="configurar.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-cog"></i>
                    Configurar Sala
                </a>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Filtro Sede -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Sede:</label>
                        <select id="filtroSede" onchange="cargarSalasPorSede()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las sedes</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?php echo $sede['id_sede']; ?>">
                                    <?php echo htmlspecialchars($sede['nombre']); ?> - <?php echo htmlspecialchars($sede['ciudad_nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro Sala -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Sala:</label>
                        <select id="filtroSala" onchange="cargarAsientos()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione una sala</option>
                        </select>
                    </div>

                    <!-- Botón visualizar -->
                    <div class="flex items-end">
                        <button onclick="cargarAsientos()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2">
                            <i class="fas fa-eye"></i>
                            Visualizar Mapa
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info de sala seleccionada -->
            <div id="infoSala" class="hidden bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-3 rounded-lg">
                        <div class="text-xs text-gray-600 mb-1">Sala</div>
                        <div class="text-xl font-bold text-blue-600">Sala <span id="numSala"></span></div>
                    </div>
                    <div class="bg-green-50 p-3 rounded-lg">
                        <div class="text-xs text-gray-600 mb-1">Total Asientos</div>
                        <div class="text-xl font-bold text-green-600"><span id="totalAsientos">0</span></div>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-lg">
                        <div class="text-xs text-gray-600 mb-1">Activos</div>
                        <div class="text-xl font-bold text-purple-600"><span id="asientosActivos">0</span></div>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg">
                        <div class="text-xs text-gray-600 mb-1">Inactivos</div>
                        <div class="text-xl font-bold text-red-600"><span id="asientosInactivos">0</span></div>
                    </div>
                </div>
            </div>

            <!-- Mapa de Asientos -->
            <div id="contenedorMapa" class="hidden">
                <!-- Pantalla -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex flex-col items-center">
                        <div class="w-full max-w-4xl h-12 bg-gradient-to-b from-gray-700 to-gray-900 rounded-t-3xl flex items-center justify-center mb-2">
                            <span class="text-white text-sm font-bold uppercase tracking-wider">PANTALLA</span>
                        </div>
                        <div class="w-full max-w-4xl h-2 bg-gray-300 rounded-b-sm"></div>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-center">
                        <div id="mapaAsientos" class="inline-block">
                            <!-- Se llenará dinámicamente -->
                        </div>
                    </div>

                    <!-- Leyenda -->
                    <div class="mt-6 flex justify-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chair text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700">Disponible</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chair text-white text-xs"></i>
                            </div>
                            <span class="text-gray-700">Inactivo</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje vacío -->
            <div id="mensajeVacio" class="bg-white rounded-lg shadow p-12 text-center">
                <i class="fas fa-couch text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Selecciona una sala</h3>
                <p class="text-gray-500">Elige una sede y sala para visualizar el mapa de asientos</p>
            </div>
        </main>

        <script>
            const API_URL = '../../../api/asiento_api.php';
            const SALA_API_URL = '../../../api/sala_api.php';
            
            let salasData = <?php echo json_encode($salas); ?>;

            // Cargar salas por sede
            function cargarSalasPorSede() {
                const sedeId = document.getElementById('filtroSede').value;
                const selectSala = document.getElementById('filtroSala');
                
                selectSala.innerHTML = '<option value="">Seleccione una sala</option>';
                
                const salasFiltradas = sedeId 
                    ? salasData.filter(s => s.id_sede == sedeId)
                    : salasData;
                
                salasFiltradas.forEach(sala => {
                    const option = document.createElement('option');
                    option.value = sala.id_sala;
                    option.textContent = `Sala ${sala.num_sala} - ${sala.sede_nombre}`;
                    selectSala.appendChild(option);
                });

                // Limpiar visualización
                ocultarMapa();
            }

            // Cargar asientos de la sala
            async function cargarAsientos() {
                const salaId = document.getElementById('filtroSala').value;
                
                if (!salaId) {
                    mostrarAlerta('Seleccione una sala', 'warning');
                    ocultarMapa();
                    return;
                }

                try {
                    const response = await fetch(`${API_URL}?sala=${salaId}&mapa=1`);
                    const result = await response.json();

                    console.log('Respuesta API:', result); // Debug

                    if (result.success) {
                        mostrarMapa(result.data.mapa, result.data.filas, salaId);
                    } else {
                        mostrarAlerta(result.message, 'error');
                        ocultarMapa();
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al cargar asientos', 'error');
                }
            }

            // Mostrar mapa de asientos
            function mostrarMapa(mapa, filas, salaId) {
                const sala = salasData.find(s => s.id_sala == salaId);
                
                // Actualizar info
                document.getElementById('numSala').textContent = sala.num_sala;
                
                let totalAsientos = 0;
                let activos = 0;
                let inactivos = 0;

                // Generar mapa HTML
                let html = '<div class="grid gap-2">';
                
                // Iterar por cada fila
                filas.forEach(fila => {
                    html += '<div class="flex gap-2 justify-center items-center">';
                    
                    // Letra de la fila
                    html += `<div class="w-8 h-8 flex items-center justify-center font-bold text-gray-600">${fila}</div>`;
                    
                    // Obtener asientos de esta fila
                    const asientosFila = mapa[fila];
                    
                    // Iterar por columnas
                    const columnas = Object.keys(asientosFila).sort((a, b) => parseInt(a) - parseInt(b));
                    
                    columnas.forEach(col => {
                        const asiento = asientosFila[col];
                        totalAsientos++;
                        
                        if (asiento.estado == 1) {
                            activos++;
                            html += `
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center cursor-pointer hover:bg-green-600 transition" 
                                     title="Asiento ${asiento.codigo} - Disponible"
                                     onclick="toggleAsiento(${asiento.id}, ${asiento.estado})">
                                    <i class="fas fa-chair text-white text-xs"></i>
                                </div>
                            `;
                        } else {
                            inactivos++;
                            html += `
                                <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center cursor-pointer hover:bg-red-600 transition" 
                                     title="Asiento ${asiento.codigo} - Inactivo"
                                     onclick="toggleAsiento(${asiento.id}, ${asiento.estado})">
                                    <i class="fas fa-chair text-white text-xs"></i>
                                </div>
                            `;
                        }
                    });
                    
                    html += '</div>';
                });
                
                html += '</div>';

                // Actualizar estadísticas
                document.getElementById('totalAsientos').textContent = totalAsientos;
                document.getElementById('asientosActivos').textContent = activos;
                document.getElementById('asientosInactivos').textContent = inactivos;

                // Mostrar elementos
                document.getElementById('mapaAsientos').innerHTML = html;
                document.getElementById('infoSala').classList.remove('hidden');
                document.getElementById('contenedorMapa').classList.remove('hidden');
                document.getElementById('mensajeVacio').classList.add('hidden');
            }

            // Ocultar mapa
            function ocultarMapa() {
                document.getElementById('infoSala').classList.add('hidden');
                document.getElementById('contenedorMapa').classList.add('hidden');
                document.getElementById('mensajeVacio').classList.remove('hidden');
            }

            // Toggle estado de asiento
            async function toggleAsiento(id, estadoActual) {
                if (!confirm('¿Cambiar estado del asiento?')) return;

                try {
                    const nuevoEstado = estadoActual == 1 ? 0 : 1;
                    const response = await fetch(API_URL, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, estado: nuevoEstado })
                    });

                    const result = await response.json();

                    if (result.success) {
                        mostrarAlerta('Estado actualizado', 'success');
                        cargarAsientos(); // Recargar mapa
                    } else {
                        mostrarAlerta(result.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    mostrarAlerta('Error al actualizar estado', 'error');
                }
            }

            // Mostrar alertas
            function mostrarAlerta(mensaje, tipo) {
                const color = tipo === 'success' ? 'green' : tipo === 'error' ? 'red' : 'yellow';
                const alerta = document.createElement('div');
                alerta.className = `fixed top-4 right-4 bg-${color}-100 border border-${color}-400 text-${color}-700 px-4 py-3 rounded-lg shadow-lg z-50`;
                alerta.innerHTML = `
                    <span class="block sm:inline">${mensaje}</span>
                `;
                document.body.appendChild(alerta);
                setTimeout(() => alerta.remove(), 3000);
            }
        </script>

<?php include '../../partials/footer.php'; ?>
