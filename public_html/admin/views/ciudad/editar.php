<?php
$pageTitle = 'Editar Ciudad';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Obtener ID de la ciudad
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Obtener datos de la ciudad
require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/CiudadModel.php';

$model = new CiudadModel($conn);
$ciudad = $model->getById($id);

if (!$ciudad) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Ciudades</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Ciudad</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Ciudad</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-lg">
                <form id="formCiudad" onsubmit="return actualizarCiudad(event)">
                    <input type="hidden" id="id_ciudad" value="<?php echo $ciudad['id_ciudad']; ?>">
                    
                    <div class="mb-4">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre de la Ciudad <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nombre" 
                               name="nombre" 
                               required
                               value="<?php echo htmlspecialchars($ciudad['nombre']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: Lima">
                    </div>

                    <div class="mb-4">
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado
                        </label>
                        <select id="estado" 
                                name="estado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1" <?php echo $ciudad['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo $ciudad['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
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
            function actualizarCiudad(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_ciudad').value;
                const nombre = document.getElementById('nombre').value.trim();
                const estado = document.getElementById('estado').value;
                
                if (!nombre) {
                    showAlert('El nombre es requerido', 'error');
                    return false;
                }
                
                fetch(`${API_BASE}/ciudad_api.php`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: parseInt(id),
                        nombre: nombre,
                        estado: parseInt(estado)
                    })
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
