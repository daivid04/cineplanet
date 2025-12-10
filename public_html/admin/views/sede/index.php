<?php
$pageTitle = 'Sedes';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';

$model = new SedeModel($conn);
$sedes = $model->getAll();
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Sedes Cineplanet</h1>
                    <p class="text-gray-600">Gestiona las sedes y locales del cine</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Sede
                </a>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ciudad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Direccion</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefono</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($sedes)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No hay sedes registradas
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($sedes as $sede): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $sede['id_sede']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-building text-blue-600"></i>
                                    <span class="font-medium"><?php echo htmlspecialchars($sede['nombre']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-map-marker-alt text-xs mr-1"></i>
                                    <?php echo htmlspecialchars($sede['ciudad_nombre']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php echo htmlspecialchars($sede['direccion'] ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <?php if ($sede['telefono']): ?>
                                    <i class="fas fa-phone text-xs text-gray-400 mr-1"></i>
                                    <?php echo htmlspecialchars($sede['telefono']); ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($sede['estado'] == 1): ?>
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
                                <button onclick="toggleEstado(<?php echo $sede['id_sede']; ?>, <?php echo $sede['estado']; ?>, 'sede_api.php')"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $sede['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                
                                <a href="editar.php?id=<?php echo $sede['id_sede']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="confirmDelete(<?php echo $sede['id_sede']; ?>, '<?php echo htmlspecialchars($sede['nombre']); ?>', 'sede_api.php')"
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
                Total: <?php echo count($sedes); ?> sedes
            </div>
        </main>

<?php include '../../partials/footer.php'; ?>
