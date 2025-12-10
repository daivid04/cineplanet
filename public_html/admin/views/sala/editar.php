<?php
$pageTitle = 'Editar Sala';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SalaModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';

$salaModel = new SalaModel($conn);
$sedeModel = new SedeModel($conn);

$sala = $salaModel->getById($id);
$sedes = $sedeModel->getAll(true); // Solo sedes activas

if (!$sala) {
    header('Location: index.php');
    exit;
}

// Contar asientos y funciones de esta sala para mostrar info
$sqlAsientos = "SELECT COUNT(*) as total FROM asiento WHERE id_sala = :id";
$stmtAsientos = $conn->prepare($sqlAsientos);
$stmtAsientos->execute([':id' => $id]);
$totalAsientos = $stmtAsientos->fetch(PDO::FETCH_ASSOC)['total'];

$sqlFunciones = "SELECT COUNT(*) as total FROM funcion WHERE id_sala = :id";
$stmtFunciones = $conn->prepare($sqlFunciones);
$stmtFunciones->execute([':id' => $id]);
$totalFunciones = $stmtFunciones->fetch(PDO::FETCH_ASSOC)['total'];
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Salas</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Sala</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Editar Sala <?php echo $sala['num_sala']; ?></h1>
                <p class="text-gray-600"><?php echo htmlspecialchars($sala['sede_nombre']); ?> - <?php echo htmlspecialchars($sala['ciudad_nombre']); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="formSala" onsubmit="return actualizarSala(event)">
                            <input type="hidden" id="id_sala" value="<?php echo $sala['id_sala']; ?>">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="id_sede" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sede <span class="text-red-500">*</span>
                                    </label>
                                    <select id="id_sede" 
                                            name="id_sede" 
                                            required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione una sede</option>
                                        <?php foreach ($sedes as $sede): ?>
                                            <option value="<?php echo $sede['id_sede']; ?>"
                                                    data-ciudad="<?php echo htmlspecialchars($sede['ciudad_nombre']); ?>"
                                                    <?php echo $sede['id_sede'] == $sala['id_sede'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sede['nombre']); ?> (<?php echo htmlspecialchars($sede['ciudad_nombre']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="num_sala" class="block text-sm font-medium text-gray-700 mb-1">
                                        Numero de Sala <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="num_sala" 
                                           name="num_sala" 
                                           required
                                           min="1"
                                           max="99"
                                           value="<?php echo $sala['num_sala']; ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado
                                    </label>
                                    <select id="estado" 
                                            name="estado"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="1" <?php echo $sala['estado'] == 1 ? 'selected' : ''; ?>>Activa</option>
                                        <option value="0" <?php echo $sala['estado'] == 0 ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                </div>
                            </div>

                            <?php if ($totalFunciones > 0): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Advertencia:</strong> Esta sala tiene <?php echo $totalFunciones; ?> funcion(es) asociada(s). 
                                    Si la desactivas, las funciones futuras podrian verse afectadas.
                                </p>
                            </div>
                            <?php endif; ?>

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
                </div>

                <!-- Panel de Informacion -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Informacion de la Sala
                        </h3>

                        <div class="space-y-4">
                            <!-- Asientos -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-chair text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Asientos</p>
                                        <p class="text-xs text-gray-500">Configurados</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-gray-800"><?php echo $totalAsientos; ?></span>
                            </div>

                            <!-- Funciones -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-film text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Funciones</p>
                                        <p class="text-xs text-gray-500">Totales</p>
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-gray-800"><?php echo $totalFunciones; ?></span>
                            </div>

                            <!-- Estado -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 <?php echo $sala['estado'] == 1 ? 'bg-green-100' : 'bg-red-100'; ?> rounded-lg flex items-center justify-center">
                                        <i class="fas fa-<?php echo $sala['estado'] == 1 ? 'check' : 'times'; ?> <?php echo $sala['estado'] == 1 ? 'text-green-600' : 'text-red-600'; ?>"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Estado Actual</p>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $sala['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $sala['estado'] == 1 ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </div>
                        </div>

                        <?php if ($totalAsientos == 0): ?>
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <i class="fas fa-lightbulb mr-1"></i>
                                Esta sala no tiene asientos configurados. 
                                <!-- Puedes agregarlos desde el modulo de asientos. -->
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function actualizarSala(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_sala').value;
                const numSala = document.getElementById('num_sala').value;
                const idSede = document.getElementById('id_sede').value;
                const estado = document.getElementById('estado').value;
                
                if (!idSede) {
                    showAlert('Debe seleccionar una sede', 'error');
                    return false;
                }

                if (!numSala || numSala < 1) {
                    showAlert('El numero de sala es requerido y debe ser mayor a 0', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    num_sala: parseInt(numSala),
                    id_sede: parseInt(idSede),
                    estado: parseInt(estado)
                };
                
                fetch('../../../api/sala_api.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1500);
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    showAlert('Error al actualizar: ' + error.message, 'error');
                });
                
                return false;
            }
        </script>

<?php include '../../partials/footer.php'; ?>
