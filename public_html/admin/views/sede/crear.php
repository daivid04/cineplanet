<?php
$pageTitle = 'Nueva Sede';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/CiudadModel.php';

$ciudadModel = new CiudadModel($conn);
$ciudades = $ciudadModel->getAll(true); // Solo ciudades activas
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Sedes</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nueva Sede</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nueva Sede</h1>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formSede" onsubmit="return guardarSede(event)">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre de la Sede <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Cineplanet Mall del Sur, Cineplanet Real Plaza">
                        </div>

                        <div class="md:col-span-2">
                            <label for="id_ciudad" class="block text-sm font-medium text-gray-700 mb-1">
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <select id="id_ciudad" 
                                    name="id_ciudad" 
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Seleccione una ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <option value="<?php echo $ciudad['id_ciudad']; ?>">
                                        <?php echo htmlspecialchars($ciudad['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1">
                                Direccion
                            </label>
                            <input type="text" 
                                   id="direccion" 
                                   name="direccion" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: Av. Los Heroes 123, Mall del Sur">
                        </div>

                        <div class="md:col-span-2">
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                Telefono
                            </label>
                            <input type="text" 
                                   id="telefono" 
                                   name="telefono" 
                                   maxlength="15"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ej: 01-1234567 o 987654321">
                            <p class="mt-1 text-xs text-gray-500">Maximo 15 caracteres</p>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2">
                            <i class="fas fa-info-circle"></i> Informacion
                        </h3>
                        <div class="text-xs text-blue-800 space-y-1">
                            <p>• El nombre de la sede debe ser unico</p>
                            <p>• La direccion y telefono son opcionales pero recomendados</p>
                            <p>• Una vez creada la sede, podras agregar salas y configurar stock de productos</p>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar
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
            function guardarSede(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre').value.trim();
                const idCiudad = document.getElementById('id_ciudad').value;
                const direccion = document.getElementById('direccion').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                
                if (!nombre) {
                    showAlert('El nombre de la sede es requerido', 'error');
                    return false;
                }

                if (!idCiudad) {
                    showAlert('Debe seleccionar una ciudad', 'error');
                    return false;
                }
                
                const data = { 
                    nombre: nombre,
                    id_ciudad: parseInt(idCiudad)
                };
                
                if (direccion) data.direccion = direccion;
                if (telefono) data.telefono = telefono;
                
                fetch(`${API_BASE}/sede_api.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => window.location.href = 'index.php', 1000);
                    } else {
                        showAlert(data.message || 'Error al guardar', 'error');
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
