<?php
$pageTitle = 'Editar Metodo de Pago';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/MetodoModel.php';

$model = new MetodoModel($conn);
$metodo = $model->getById($id);

if (!$metodo) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Metodos de Pago</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Metodo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Metodo de Pago</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formMetodo" onsubmit="return actualizarMetodo(event)">
                    <input type="hidden" id="id_metodo" value="<?php echo $metodo['id_metodo']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre_metodo" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Metodo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre_metodo" 
                                   name="nombre_metodo" 
                                   required
                                   value="<?php echo htmlspecialchars($metodo['nombre_metodo']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Tarjeta de Credito">
                        </div>

                        <div class="md:col-span-2">
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                                Descripcion
                            </label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Ej: Pago con tarjeta de credito Visa/Mastercard"><?php echo htmlspecialchars($metodo['descripcion'] ?? ''); ?></textarea>
                        </div>

                        <div>
                            <label for="icono" class="block text-sm font-medium text-gray-700 mb-1">
                                Icono (clase CSS)
                            </label>
                            <input type="text" 
                                   id="icono" 
                                   name="icono" 
                                   value="<?php echo htmlspecialchars($metodo['icono'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: credit-card">
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>
                            <select id="estado" 
                                    name="estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" <?php echo $metodo['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo $metodo['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
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
            function actualizarMetodo(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_metodo').value;
                const nombre = document.getElementById('nombre_metodo').value.trim();
                const descripcion = document.getElementById('descripcion').value.trim();
                const icono = document.getElementById('icono').value.trim();
                const estado = document.getElementById('estado').value;
                
                if (!nombre) {
                    showAlert('El nombre del metodo es requerido', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    nombre_metodo: nombre,
                    estado: parseInt(estado)
                };
                
                if (descripcion) data.descripcion = descripcion;
                if (icono) data.icono = icono;
                
                fetch(`${API_BASE}/metodo_api.php`, {
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
