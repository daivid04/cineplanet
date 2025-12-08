<?php
$pageTitle = 'Salas';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';

$salaModel = new SalaModel($conn);
$sedeModel = new SedeModel($conn);

$salas = $salaModel->getAll();
$sedes = $sedeModel->getAll(true); // Solo sedes activas para el filtro
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Salas de Cine</h1>
                    <p class="text-gray-600">Gestiona las salas de cada sede</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Sala
                </a>
            </div>

            <!-- Filtro por Sede -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Filtrar por sede:</label>
                    <select id="filtroSede" onchange="filtrarPorSede()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las sedes</option>
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>">
                                <?php echo htmlspecialchars($sede['nombre']); ?> (<?php echo htmlspecialchars($sede['ciudad_nombre']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span id="contadorFiltro" class="text-sm text-gray-500"></span>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sala</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaSalas" class="bg-white divide-y divide-gray-200">
                        <?php if (empty($salas)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay salas registradas
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($salas as $sala): ?>
                        <tr class="hover:bg-gray-50 fila-sala" data-sede="<?php echo $sala['id_sede']; ?>">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $sala['id_sala']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-door-open text-blue-600"></i>
                                    </div>
                                    <span class="font-semibold text-lg">Sala <?php echo $sala['num_sala']; ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-building text-xs mr-1"></i>
                                    <?php echo htmlspecialchars($sala['sede_nombre']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-map-marker-alt text-xs mr-1"></i>
                                    <?php echo htmlspecialchars($sala['ciudad_nombre']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($sala['estado'] == 1): ?>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Activa
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    Inactiva
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="toggleEstado(<?php echo $sala['id_sala']; ?>, <?php echo $sala['estado']; ?>, 'sala_api.php')"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $sala['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                
                                <a href="editar.php?id=<?php echo $sala['id_sala']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="confirmDelete(<?php echo $sala['id_sala']; ?>, 'Sala <?php echo $sala['num_sala']; ?> - <?php echo htmlspecialchars($sala['sede_nombre']); ?>', 'sala_api.php')"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-gray-500">
                Total: <span id="totalSalas"><?php echo count($salas); ?></span> salas
            </div>
        </main>

        <script>
            function filtrarPorSede() {
                const sedeId = document.getElementById('filtroSede').value;
                const filas = document.querySelectorAll('.fila-sala');
                let visibles = 0;

                filas.forEach(fila => {
                    if (!sedeId || fila.dataset.sede === sedeId) {
                        fila.style.display = '';
                        visibles++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                document.getElementById('totalSalas').textContent = visibles;
                
                const contador = document.getElementById('contadorFiltro');
                if (sedeId) {
                    contador.textContent = `(${visibles} salas en esta sede)`;
                } else {
                    contador.textContent = '';
                }
            }
        </script>

<?php include '../../partials/footer.php'; ?>
