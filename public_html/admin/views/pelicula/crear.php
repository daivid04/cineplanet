<?php
$pageTitle = 'Nueva Pelicula';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/IdiomaModel.php';
require_once __DIR__ . '/../../../../src/models/FormatoModel.php';

$idiomaModel = new IdiomaModel($conn);
$formatoModel = new FormatoModel($conn);

$idiomas = $idiomaModel->getAll(true); // Solo activos
$formatos = $formatoModel->getAll(true); // Solo activos
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8 overflow-auto">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-gray-600 mb-2">
                    <a href="index.php" class="hover:text-blue-600">Peliculas</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Nueva Pelicula</span>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">Nueva Pelicula</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <form id="formPelicula" onsubmit="return guardarPelicula(event)">
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
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Ej: Avengers: Endgame">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
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
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Ej: 120"
                                           onchange="actualizarDuracion()">
                                    <p id="duracionFormateada" class="mt-1 text-xs text-gray-500"></p>
                                </div>

                                <!-- URL Imagen -->
                                <div>
                                    <label for="url_imagen" class="block text-sm font-medium text-gray-700 mb-1">
                                        URL de Imagen <span class="text-red-500">*</span>
                                    </label>
                                    <input type="url" 
                                           id="url_imagen" 
                                           name="url_imagen" 
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="https://..."
                                           onchange="previsualizarImagen()">
                                </div>
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Describe la trama de la pelicula..."></textarea>
                            </div>

                            <!-- Idiomas -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Idiomas Disponibles
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <?php if (empty($idiomas)): ?>
                                    <p class="text-sm text-gray-500">No hay idiomas disponibles. <a href="../idioma/crear.php" class="text-blue-600 hover:underline">Crear idioma</a></p>
                                    <?php else: ?>
                                    <?php foreach ($idiomas as $idioma): ?>
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-blue-50 transition-colors">
                                        <input type="checkbox" 
                                               name="idiomas[]" 
                                               value="<?php echo $idioma['id_idioma']; ?>"
                                               class="mr-2 rounded text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm"><?php echo htmlspecialchars($idioma['idioma']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Formatos -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Formatos Disponibles
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <?php if (empty($formatos)): ?>
                                    <p class="text-sm text-gray-500">No hay formatos disponibles. <a href="../formato/crear.php" class="text-blue-600 hover:underline">Crear formato</a></p>
                                    <?php else: ?>
                                    <?php foreach ($formatos as $formato): ?>
                                    <label class="inline-flex items-center px-3 py-2 bg-gray-50 rounded-lg border cursor-pointer hover:bg-purple-50 transition-colors">
                                        <input type="checkbox" 
                                               name="formatos[]" 
                                               value="<?php echo $formato['id_formato']; ?>"
                                               class="mr-2 rounded text-purple-600 focus:ring-purple-500">
                                        <span class="text-sm"><?php echo htmlspecialchars($formato['nombre']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
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
                </div>

                <!-- Preview -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6 sticky top-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-eye text-blue-600 mr-2"></i>
                            Vista Previa
                        </h3>

                        <div class="bg-gray-100 rounded-lg overflow-hidden">
                            <!-- Imagen Preview -->
                            <div class="h-64 bg-gray-200 flex items-center justify-center">
                                <img id="imagenPreview" 
                                     src="https://via.placeholder.com/300x450?text=Imagen+Pelicula" 
                                     alt="Preview"
                                     class="w-full h-full object-cover hidden">
                                <div id="imagenPlaceholder" class="text-center text-gray-400">
                                    <i class="fas fa-image text-4xl mb-2"></i>
                                    <p class="text-sm">Vista previa de imagen</p>
                                </div>
                            </div>

                            <div class="p-4">
                                <h4 id="nombrePreview" class="font-bold text-gray-800">Nombre de la pelicula</h4>
                                <p id="duracionPreview" class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i> -- min
                                </p>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">
                                <i class="fas fa-info-circle"></i> Consejos
                            </h4>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li>• Usa imagenes en formato vertical (poster)</li>
                                <li>• Recomendado: 300x450 pixels minimo</li>
                                <li>• Los idiomas y formatos se pueden editar despues</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <script>
            // Actualizar preview del nombre
            document.getElementById('nombre').addEventListener('input', function() {
                document.getElementById('nombrePreview').textContent = this.value || 'Nombre de la pelicula';
            });

            // Actualizar duracion formateada
            function actualizarDuracion() {
                const minutos = parseInt(document.getElementById('duracion').value) || 0;
                const horas = Math.floor(minutos / 60);
                const mins = minutos % 60;
                
                let texto = '';
                if (horas > 0) {
                    texto = horas + 'h ' + mins + 'min';
                } else if (mins > 0) {
                    texto = mins + ' min';
                }
                
                document.getElementById('duracionFormateada').textContent = texto ? `= ${texto}` : '';
                document.getElementById('duracionPreview').innerHTML = `<i class="fas fa-clock mr-1"></i> ${texto || '-- min'}`;
            }

            // Previsualizar imagen
            function previsualizarImagen() {
                const url = document.getElementById('url_imagen').value;
                const img = document.getElementById('imagenPreview');
                const placeholder = document.getElementById('imagenPlaceholder');
                
                if (url) {
                    img.src = url;
                    img.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    
                    img.onerror = function() {
                        img.classList.add('hidden');
                        placeholder.classList.remove('hidden');
                        placeholder.innerHTML = '<i class="fas fa-exclamation-triangle text-4xl mb-2 text-red-400"></i><p class="text-sm text-red-400">Error al cargar imagen</p>';
                    };
                } else {
                    img.classList.add('hidden');
                    placeholder.classList.remove('hidden');
                }
            }

            // Guardar pelicula
            function guardarPelicula(event) {
                event.preventDefault();
                
                const nombre = document.getElementById('nombre').value.trim();
                const duracion = document.getElementById('duracion').value;
                const url_imagen = document.getElementById('url_imagen').value.trim();
                const sinopsis = document.getElementById('sinopsis').value.trim();
                
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
                    nombre: nombre,
                    duracion: parseInt(duracion),
                    url_imagen: url_imagen,
                    sinopsis: sinopsis,
                    idiomas: idiomas,
                    formatos: formatos
                };
                
                fetch('../../../api/pelicula_api.php', {
                    method: 'POST',
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
                    showAlert('Error al guardar: ' + error.message, 'error');
                });
                
                return false;
            }
        </script>

<?php include '../../partials/footer.php'; ?>
