<?php
$pageTitle = 'Editar Combo';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/ComboModel.php';

$model = new ComboModel($conn);
$combo = $model->getById($id);

if (!$combo) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Combos</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Combo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Combo</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formCombo" onsubmit="return actualizarCombo(event)">
                    <input type="hidden" id="id_combo" value="<?php echo $combo['id_combo']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Combo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   value="<?php echo htmlspecialchars($combo['nombre']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Combo Familiar">
                        </div>

                        <div>
                            <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                                Precio del Combo (S/) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="precio" 
                                   name="precio" 
                                   step="0.01"
                                   min="0.01"
                                   value="<?php echo $combo['precio']; ?>"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>
                            <select id="estado" 
                                    name="estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" <?php echo $combo['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo $combo['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Actualizar
                        </button>
                        <a href="index.php" 
                           class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>

        <script>
            function actualizarCombo(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_combo').value;
                const nombre = document.getElementById('nombre').value.trim();
                const precio = parseFloat(document.getElementById('precio').value);
                const estado = document.getElementById('estado').value;
                
                if (!nombre) {
                    showAlert('El nombre del combo es requerido', 'error');
                    return false;
                }

                if (isNaN(precio) || precio <= 0) {
                    showAlert('El precio debe ser mayor a 0', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    nombre: nombre,
                    precio: precio,
                    estado: parseInt(estado)
                };
                
                fetch(`${API_BASE}/combo_api.php`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.href = 'index.php', 1000);
                    } else {
                        showAlert(data.message || 'Error al actualizar', 'error');
                    }
                })
                .catch(error => {
                    showAlert('Error de conexion', 'error');
                    console.error(error);
                });
                
                return false;
            }
        </script>

<?php include '../../partials/footer.php'; ?>
