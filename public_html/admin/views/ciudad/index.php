<?php
$pageTitle = 'Ciudades';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Incluir conexion y model para obtener datos
require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/CiudadModel.php';

$model = new CiudadModel($conn);
$ciudades = $model->getAll();
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Ciudades</h1>
                    <p class="text-gray-600">Gestiona las ciudades del sistema</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Ciudad
                </a>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($ciudades)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No hay ciudades registradas
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($ciudades as $ciudad): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $ciudad['id_ciudad']; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($ciudad['nombre']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($ciudad['estado'] == 1): ?>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Activo
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    Inactivo
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <!-- Toggle Estado -->
                                <button onclick="toggleEstado(<?php echo $ciudad['id_ciudad']; ?>, <?php echo $ciudad['estado']; ?>, 'ciudad_api.php')"
                                        class="text-yellow-600 hover:text-yellow-900 mr-3" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $ciudad['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                
                                <!-- Editar -->
                                <a href="editar.php?id=<?php echo $ciudad['id_ciudad']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <!-- Eliminar -->
                                <button onclick="confirmDelete(<?php echo $ciudad['id_ciudad']; ?>, '<?php echo htmlspecialchars($ciudad['nombre']); ?>', 'ciudad_api.php')"
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

            <!-- Info -->
            <div class="mt-4 text-sm text-gray-500">
                Total: <?php echo count($ciudades); ?> ciudades
            </div>
        </main>

<?php include '../../partials/footer.php'; ?>
