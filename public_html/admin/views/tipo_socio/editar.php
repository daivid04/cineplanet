<?php
$pageTitle = 'Editar Tipo de Socio';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/TipoSocioModel.php';

$model = new TipoSocioModel($conn);
$tipoSocio = $model->getById($id);

if (!$tipoSocio) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Tipos de Socio</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Tipo</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Tipo de Socio</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formTipoSocio" onsubmit="return actualizarTipoSocio(event)">
                    <input type="hidden" id="id_socio" value="<?php echo $tipoSocio['id_socio']; ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre del Tipo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   value="<?php echo htmlspecialchars($tipoSocio['nombre']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Silver, Gold, Platinum">
                        </div>

                        <div>
                            <label for="desc_dulces" class="block text-sm font-medium text-gray-700 mb-1">
                                Descuento en Dulces (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="desc_dulces" 
                                   name="desc_dulces" 
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="<?php echo $tipoSocio['desc_dulces']; ?>"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="desc_boleto" class="block text-sm font-medium text-gray-700 mb-1">
                                Descuento en Boletos (%) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="desc_boleto" 
                                   name="desc_boleto" 
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="<?php echo $tipoSocio['desc_boleto']; ?>"
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="puntos_por_sol" class="block text-sm font-medium text-gray-700 mb-1">
                                Puntos por Sol Gastado <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   id="puntos_por_sol" 
                                   name="puntos_por_sol" 
                                   step="0.01"
                                   min="0"
                                   value="<?php echo $tipoSocio['puntos_por_sol']; ?>"
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
                                <option value="1" <?php echo $tipoSocio['estado'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo $tipoSocio['estado'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
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
            function actualizarTipoSocio(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_socio').value;
                const nombre = document.getElementById('nombre').value.trim();
                const descDulces = parseFloat(document.getElementById('desc_dulces').value);
                const descBoleto = parseFloat(document.getElementById('desc_boleto').value);
                const puntosPorSol = parseFloat(document.getElementById('puntos_por_sol').value);
                const estado = document.getElementById('estado').value;
                
                if (!nombre) {
                    showAlert('El nombre del tipo de socio es requerido', 'error');
                    return false;
                }

                if (descDulces < 0 || descDulces > 100) {
                    showAlert('El descuento en dulces debe estar entre 0 y 100', 'error');
                    return false;
                }

                if (descBoleto < 0 || descBoleto > 100) {
                    showAlert('El descuento en boletos debe estar entre 0 y 100', 'error');
                    return false;
                }

                if (puntosPorSol < 0) {
                    showAlert('Los puntos por sol deben ser mayor o igual a 0', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    nombre: nombre,
                    desc_dulces: descDulces,
                    desc_boleto: descBoleto,
                    puntos_por_sol: puntosPorSol,
                    estado: parseInt(estado)
                };
                
                fetch(`${API_BASE}/tipo_socio_api.php`, {
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
