<?php
$pageTitle = 'Editar Idioma';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/IdiomaModel.php';

$model = new IdiomaModel($conn);
$idioma = $model->getById($id);

if (!$idioma) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Idiomas</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Idioma</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Idioma</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-lg">
                <form id="formIdioma" onsubmit="return actualizarIdioma(event)">
                    <input type="hidden" id="id_idioma" value="<?php echo $idioma['id_idioma']; ?>">
                    
                    <div class="mb-4">
                        <label for="idioma" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre del Idioma <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="idioma" 
                               name="idioma" 
                               required
                               value="<?php echo htmlspecialchars($idioma['idioma']); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Ej: Espanol (Doblada)">
                    </div>

                    <div class="mb-4">
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                            Estado
                        </label>
                        <select id="estado" 
                                name="estado"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="1" <?php echo $idioma['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                            <option value="0" <?php echo $idioma['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
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
            function actualizarIdioma(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_idioma').value;
                const idiomaVal = document.getElementById('idioma').value.trim();
                const estado = document.getElementById('estado').value;
                
                if (!idiomaVal) {
                    showAlert('El nombre del idioma es requerido', 'error');
                    return false;
                }
                
                fetch(`${API_BASE}/idioma_api.php`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        id: parseInt(id),
                        idioma: idiomaVal,
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
