<?php
$pageTitle = 'Socios';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SocioAdminModel.php';

$model = new SocioAdminModel($conn);

// Filtros
$filtroTipo = $_GET['tipo'] ?? null;
$soloActivos = isset($_GET['activos']) && $_GET['activos'] === '1';
$busqueda = $_GET['busqueda'] ?? null;

$socios = $model->getAll($soloActivos, $filtroTipo, $busqueda);
$tiposSocio = $model->getTiposSocio(false); // Todos para el filtro
$estadisticas = $model->getEstadisticas();
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Socios</h1>
                    <p class="text-gray-600">Gestiona los socios registrados en el sistema</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nuevo Socio
                </a>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-id-card text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Socios</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['total'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-user-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Activos</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $estadisticas['activos'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <?php 
                $generos = $estadisticas['por_genero'] ?? [];
                $masculino = 0;
                $femenino = 0;
                foreach ($generos as $g) {
                    if ($g['genero'] === 'Masculino') $masculino = $g['cantidad'];
                    if ($g['genero'] === 'Femenino') $femenino = $g['cantidad'];
                }
                ?>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                            <i class="fas fa-mars text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Masculino</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $masculino; ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-pink-100 text-pink-600">
                            <i class="fas fa-venus text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Femenino</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $femenino; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Membresía</label>
                        <select id="filtroTipo" onchange="aplicarFiltros()" 
                                class="border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <?php foreach ($tiposSocio as $tipo): ?>
                                <option value="<?php echo $tipo['id_socio']; ?>" 
                                        <?php echo $filtroTipo == $tipo['id_socio'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select id="filtroActivos" onchange="aplicarFiltros()" 
                                class="border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos</option>
                            <option value="1" <?php echo $soloActivos ? 'selected' : ''; ?>>Solo activos</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                        <div class="relative">
                            <input type="text" id="busqueda" placeholder="Buscar por nombre, documento o correo..." 
                                   value="<?php echo htmlspecialchars($busqueda ?? ''); ?>"
                                   onkeyup="debounceSearch()"
                                   class="w-full border rounded-lg px-3 py-2 pl-10 focus:ring-blue-500 focus:border-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="self-end">
                        <button onclick="limpiarFiltros()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            <i class="fas fa-times mr-1"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Socios -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Socio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Membresía</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Género</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($socios)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-2"></i>
                                    <p>No se encontraron socios</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($socios as $socio): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $socio['id_usuario']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium text-sm">
                                                        <?php echo strtoupper(substr($socio['nombre'], 0, 1) . substr($socio['apellido'], 0, 1)); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($socio['correo']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <i class="fas fa-id-card mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($socio['documento']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $badgeColor = 'gray';
                                        $tipoNombre = strtolower($socio['tipo_socio_nombre'] ?? '');
                                        if (strpos($tipoNombre, 'premium') !== false || strpos($tipoNombre, 'oro') !== false) {
                                            $badgeColor = 'yellow';
                                        } elseif (strpos($tipoNombre, 'platino') !== false || strpos($tipoNombre, 'vip') !== false) {
                                            $badgeColor = 'purple';
                                        } elseif (strpos($tipoNombre, 'básico') !== false || strpos($tipoNombre, 'basico') !== false) {
                                            $badgeColor = 'blue';
                                        }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo $badgeColor; ?>-100 text-<?php echo $badgeColor; ?>-800">
                                            <i class="fas fa-crown mr-1"></i>
                                            <?php echo htmlspecialchars($socio['tipo_socio_nombre']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php if ($socio['genero'] === 'Masculino'): ?>
                                            <i class="fas fa-mars text-blue-500 mr-1"></i>
                                        <?php elseif ($socio['genero'] === 'Femenino'): ?>
                                            <i class="fas fa-venus text-pink-500 mr-1"></i>
                                        <?php else: ?>
                                            <i class="fas fa-genderless text-gray-500 mr-1"></i>
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($socio['genero']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button onclick="toggleEstadoSocio(<?php echo $socio['id_usuario']; ?>, <?php echo $socio['estado']; ?>)" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium cursor-pointer hover:opacity-80 transition <?php echo $socio['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <i class="fas <?php echo $socio['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                            <?php echo $socio['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </button>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="ver.php?id=<?php echo $socio['id_usuario']; ?>" 
                                           class="text-gray-600 hover:text-gray-900 mr-3" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="editar.php?id=<?php echo $socio['id_usuario']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="confirmDeleteSocio(<?php echo $socio['id_usuario']; ?>, '<?php echo addslashes($socio['nombre'] . ' ' . $socio['apellido']); ?>')" 
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

            <!-- Info de resultados -->
            <div class="mt-4 text-sm text-gray-500">
                Mostrando <?php echo count($socios); ?> socio(s)
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">Confirmar acción</h3>
                <p class="text-gray-500 mb-6" id="modalMessage">¿Estás seguro?</p>
            </div>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button id="btnConfirm" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '../../api/socio_admin_api.php';
        let searchTimeout;

        function aplicarFiltros() {
            const tipo = document.getElementById('filtroTipo').value;
            const activos = document.getElementById('filtroActivos').value;
            const busqueda = document.getElementById('busqueda').value;
            
            let url = 'index.php?';
            if (tipo) url += 'tipo=' + tipo + '&';
            if (activos) url += 'activos=' + activos + '&';
            if (busqueda) url += 'busqueda=' + encodeURIComponent(busqueda) + '&';
            
            window.location.href = url.slice(0, -1);
        }

        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(aplicarFiltros, 500);
        }

        function limpiarFiltros() {
            window.location.href = 'index.php';
        }

        function toggleEstadoSocio(id, estadoActual) {
            const nuevoEstado = estadoActual == 1 ? 'inactivo' : 'activo';
            
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado del socio a "${nuevoEstado}"?`,
                () => {
                    fetch(`${API_URL}?id=${id}&action=toggle`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cambiar el estado');
                    });
                }
            );
        }

        function confirmDeleteSocio(id, nombre) {
            // Primero verificar dependencias
            fetch(`${API_URL}?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.compras_count > 0) {
                        showModal(
                            'No se puede eliminar',
                            `El socio "${nombre}" tiene ${data.data.compras_count} compra(s) registrada(s). Considere desactivarlo en lugar de eliminarlo.`,
                            null,
                            true
                        );
                    } else {
                        showModal(
                            'Eliminar socio',
                            `¿Estás seguro de eliminar al socio "${nombre}"? Esta acción no se puede deshacer.`,
                            () => deleteSocio(id)
                        );
                    }
                });
        }

        function deleteSocio(id) {
            fetch(`${API_URL}?id=${id}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el socio');
            });
        }

        function showModal(title, message, onConfirm, infoOnly = false) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            
            const btnConfirm = document.getElementById('btnConfirm');
            
            if (infoOnly) {
                btnConfirm.style.display = 'none';
            } else {
                btnConfirm.style.display = 'inline-block';
                btnConfirm.onclick = () => {
                    closeModal();
                    if (onConfirm) onConfirm();
                };
            }
            
            document.getElementById('modalConfirm').classList.remove('hidden');
            document.getElementById('modalConfirm').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modalConfirm').classList.add('hidden');
            document.getElementById('modalConfirm').classList.remove('flex');
        }

        // Cerrar modal con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>

<?php include '../../partials/footer.php'; ?>
