<?php
$pageTitle = 'Editar Sede';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/CiudadModel.php';

$sedeModel = new SedeModel($conn);
$ciudadModel = new CiudadModel($conn);

$sede = $sedeModel->getById($id);
$ciudades = $ciudadModel->getAll(true);

if (!$sede) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Sedes</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Sede</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Sede</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formSede" onsubmit="return actualizarSede(event)">
                    <input type="hidden" id="id_sede" value="<?php echo $sede['id_sede']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de la Sede <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   value="<?php echo htmlspecialchars($sede['nombre']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Cineplanet Mall del Sur">
                        </div>

                        <div>
                            <label for="id_ciudad" class="block text-sm font-medium text-gray-700 mb-1">
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <select id="id_ciudad" 
                                    name="id_ciudad" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione una ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <option value="<?php echo $ciudad['id_ciudad']; ?>"
                                            <?php echo $ciudad['id_ciudad'] == $sede['id_ciudad'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ciudad['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>
                            <select id="estado" 
                                    name="estado"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1" <?php echo $sede['estado'] == 1 ? 'selected' : ''; ?>>Activa</option>
                                <option value="0" <?php echo $sede['estado'] == 0 ? 'selected' : ''; ?>>Inactiva</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">
                                Direccion
                            </label>
                            <input type="text" 
                                   id="direccion" 
                                   name="direccion" 
                                   value="<?php echo htmlspecialchars($sede['direccion'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Av. Los Heroes 123">
                        </div>

                        <div class="md:col-span-2">
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                Telefono
                            </label>
                            <input type="text" 
                                   id="telefono" 
                                   name="telefono" 
                                   maxlength="15"
                                   value="<?php echo htmlspecialchars($sede['telefono'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: 01-1234567">
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
            function actualizarSede(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_sede').value;
                const nombre = document.getElementById('nombre').value.trim();
                const idCiudad = document.getElementById('id_ciudad').value;
                const direccion = document.getElementById('direccion').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                const estado = document.getElementById('estado').value;
                
                if (!nombre) {
                    showAlert('El nombre de la sede es requerido', 'error');
                    return false;
                }

                if (!idCiudad) {
                    showAlert('Debe seleccionar una ciudad', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    nombre: nombre,
                    id_ciudad: parseInt(idCiudad),
                    estado: parseInt(estado)
                };
                
                if (direccion) data.direccion = direccion;
                if (telefono) data.telefono = telefono;
                
                fetch(`${API_BASE}/sede_api.php`, {
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
