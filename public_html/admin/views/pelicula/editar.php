<?php
$pageTitle = 'Editar Pelicula';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/PeliculaModel.php';
require_once __DIR__ . '/../../../../src/models/IdiomaModel.php';
require_once __DIR__ . '/../../../../src/models/FormatoModel.php';

$peliculaModel = new PeliculaModel($conn);
$idiomaModel = new IdiomaModel($conn);
$formatoModel = new FormatoModel($conn);

$pelicula = $peliculaModel->getById($id);

if (!$pelicula) {
    header('Location: index.php');
    exit;
}

$idiomas = $idiomaModel->getAll(true);
$formatos = $formatoModel->getAll(true);

// Contar funciones de esta pelicula
$sqlFunciones = "SELECT COUNT(*) as total FROM funcion WHERE id_pelicula = :id";
$stmtFunciones = $conn->prepare($sqlFunciones);
$stmtFunciones->execute([':id' => $id]);
$totalFunciones = $stmtFunciones->fetch(PDO::FETCH_ASSOC)['total'];

// Funciones futuras
$sqlFuncionesFuturas = "SELECT COUNT(*) as total FROM funcion WHERE id_pelicula = :id AND fecha >= CURDATE() AND estado = 1";
$stmtFuncionesFuturas = $conn->prepare($sqlFuncionesFuturas);
$stmtFuncionesFuturas->execute([':id' => $id]);
$funcionesFuturas = $stmtFuncionesFuturas->fetch(PDO::FETCH_ASSOC)['total'];
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Peliculas</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Editar Pelicula</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($pelicula['nombre']); ?></h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="formPelicula" onsubmit="return actualizarPelicula(event)">
                            <input type="hidden" id="id_pelicula" value="<?php echo $pelicula['id_pelicula']; ?>">
                            
                            <!-- Nombre -->
                            <div class="mb-4">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre de la Pelicula <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="nombre" 
                                       name="nombre" 
                                       required
                                       maxlength="80"
                                       value="<?php echo htmlspecialchars($pelicula['nombre']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <!-- Duracion -->
                                <div>
                                    <label for="duracion" class="block text-sm font-medium text-gray-700 mb-1">
                                        Duracion (minutos) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           id="duracion" 
                                           name="duracion" 
                                           required
                                           min="1"
                                           max="600"
                                           value="<?php echo $pelicula['duracion']; ?>"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="mt-1 text-xs text-gray-500">
                                        = <?php echo PeliculaModel::formatDuration($pelicula['duracion']); ?>
                                    </p>
                                </div>

                                <!-- Estado -->
                                <div>
                                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                                        Estado
                                    </label>
                                    <select id="estado" 
                                            name="estado"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="1" <?php echo $pelicula['estado'] == 1 ? 'selected' : ''; ?>>Activa</option>
                                        <option value="0" <?php echo $pelicula['estado'] == 0 ? 'selected' : ''; ?>>Inactiva</option>
                                    </select>
                                </div>
                            </div>

                            <!-- URL Imagen -->
                            <div class="mb-4">
                                <label for="url_imagen" class="block text-sm font-medium text-gray-700 mb-1">
                                    URL de Imagen <span class="text-red-500">*</span>
                                </label>
                                <input type="url" 
                                       id="url_imagen" 
                                       name="url_imagen" 
                                       required
                                       value="<?php echo htmlspecialchars($pelicula['url_imagen']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <!-- Sinopsis -->
                            <div class="mb-4">
                                <label for="sinopsis" class="block text-sm font-medium text-gray-700 mb-1">
                                    Sinopsis <span class="text-red-500">*</span>
                                </label>
                                <textarea id="sinopsis" 
                                          name="sinopsis" 
                                          required
                                          rows="4"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($pelicula['sinopsis']); ?></textarea>
                            </div>

                            <!-- Idiomas -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Idiomas Disponibles
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($idiomas as $idioma): ?>
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors">
                                        <input type="checkbox" 
                                               name="idiomas[]" 
                                               value="<?php echo $idioma['id_idioma']; ?>"
                                               <?php echo in_array($idioma['id_idioma'], $pelicula['idiomas_ids'] ?? []) ? 'checked' : ''; ?>
                                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm"><?php echo htmlspecialchars($idioma['idioma']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Formatos -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Formatos Disponibles
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($formatos as $formato): ?>
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-purple-50 transition-colors">
                                        <input type="checkbox" 
                                               name="formatos[]" 
                                               value="<?php echo $formato['id_formato']; ?>"
                                               <?php echo in_array($formato['id_formato'], $pelicula['formatos_ids'] ?? []) ? 'checked' : ''; ?>
                                               class="mr-2 rounded text-purple-600 focus:ring-purple-500">
                                        <span class="text-sm"><?php echo htmlspecialchars($formato['nombre']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <?php if ($funcionesFuturas > 0): ?>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    <strong>Advertencia:</strong> Esta pelicula tiene <?php echo $funcionesFuturas; ?> funcion(es) programada(s). 
                                    Si la desactivas, las funciones podrian verse afectadas.
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

                <!-- Panel lateral -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Poster -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="h-80 bg-gray-200">
                            <img src="<?php echo htmlspecialchars($pelicula['url_imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($pelicula['nombre']); ?>"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://via.placeholder.com/300x450?text=Sin+Imagen'">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Informacion
                        </h3>

                        <div class="space-y-4">
                            <!-- Estado -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700">Estado</span>
                                <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $pelicula['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $pelicula['estado'] == 1 ? 'Activa' : 'Inactiva'; ?>
                                </span>
                            </div>

                            <!-- Duracion -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700">Duracion</span>
                                <span class="font-semibold text-gray-800">
                                    <?php echo PeliculaModel::formatDuration($pelicula['duracion']); ?>
                                </span>
                            </div>

                            <!-- Funciones -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700">Funciones Totales</span>
                                <span class="font-semibold text-gray-800"><?php echo $totalFunciones; ?></span>
                            </div>

                            <!-- Funciones Futuras -->
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700">Funciones Programadas</span>
                                <span class="font-semibold <?php echo $funcionesFuturas > 0 ? 'text-green-600' : 'text-gray-400'; ?>">
                                    <?php echo $funcionesFuturas; ?>
                                </span>
                            </div>

                            <!-- Idiomas actuales -->
                            <?php if (!empty($pelicula['idiomas'])): ?>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700 block mb-2">Idiomas</span>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($pelicula['idiomas'] as $idioma): ?>
                                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                        <?php echo htmlspecialchars($idioma['idioma']); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Formatos actuales -->
                            <?php if (!empty($pelicula['formatos'])): ?>
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-700 block mb-2">Formatos</span>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($pelicula['formatos'] as $formato): ?>
                                    <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">
                                        <?php echo htmlspecialchars($formato['nombre']); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            function actualizarPelicula(event) {
                event.preventDefault();
                
                const id = document.getElementById('id_pelicula').value;
                const nombre = document.getElementById('nombre').value.trim();
                const duracion = document.getElementById('duracion').value;
                const url_imagen = document.getElementById('url_imagen').value.trim();
                const sinopsis = document.getElementById('sinopsis').value.trim();
                const estado = document.getElementById('estado').value;
                
                // Obtener idiomas seleccionados
                const idiomasChecked = document.querySelectorAll('input[name="idiomas[]"]:checked');
                const idiomas = Array.from(idiomasChecked).map(cb => parseInt(cb.value));
                
                // Obtener formatos seleccionados
                const formatosChecked = document.querySelectorAll('input[name="formatos[]"]:checked');
                const formatos = Array.from(formatosChecked).map(cb => parseInt(cb.value));
                
                // Validaciones
                if (!nombre) {
                    showAlert('El nombre de la pelicula es requerido', 'error');
                    return false;
                }

                if (!duracion || duracion < 1) {
                    showAlert('La duracion es requerida y debe ser mayor a 0', 'error');
                    return false;
                }

                if (!url_imagen) {
                    showAlert('La URL de imagen es requerida', 'error');
                    return false;
                }

                if (!sinopsis) {
                    showAlert('La sinopsis es requerida', 'error');
                    return false;
                }
                
                const data = { 
                    id: parseInt(id),
                    nombre: nombre,
                    duracion: parseInt(duracion),
                    url_imagen: url_imagen,
                    sinopsis: sinopsis,
                    estado: parseInt(estado),
                    idiomas: idiomas,
                    formatos: formatos
                };
                
                fetch('../../../api/pelicula_api.php', {
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
