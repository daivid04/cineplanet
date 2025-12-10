<?php
$pageTitle = 'Peliculas';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/PeliculaModel.php';

$model = new PeliculaModel($conn);
$peliculas = $model->getAll();
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8 overflow-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Peliculas</h1>
                    <p class="text-gray-600">Gestiona el catalogo de peliculas</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Pelicula
                </a>
            </div>

            <!-- Buscador -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" 
                                   id="buscador" 
                                   placeholder="Buscar peliculas por nombre..."
                                   onkeyup="filtrarPeliculas()"
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <select id="filtroEstado" onchange="filtrarPeliculas()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                    <span id="contadorFiltro" class="text-sm text-gray-500"></span>
                </div>
            </div>

            <!-- Grid de Peliculas -->
            <div id="gridPeliculas" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php if (empty($peliculas)): ?>
                <div class="col-span-full text-center py-12 text-gray-500">
                    <i class="fas fa-film text-4xl mb-4"></i>
                    <p>No hay peliculas registradas</p>
                </div>
                <?php else: ?>
                <?php foreach ($peliculas as $pelicula): ?>
                <div class="pelicula-card bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow"
                     data-nombre="<?php echo strtolower(htmlspecialchars($pelicula['nombre'])); ?>"
                     data-estado="<?php echo $pelicula['estado']; ?>">
                    <!-- Imagen -->
                    <div class="relative h-64 bg-gray-200">
                        <img src="<?php echo htmlspecialchars($pelicula['url_imagen']); ?>" 
                             alt="<?php echo htmlspecialchars($pelicula['nombre']); ?>"
                             class="w-full h-full object-cover"
                             onerror="this.src='https://via.placeholder.com/300x450?text=Sin+Imagen'">
                        
                        <!-- Badge de estado -->
                        <div class="absolute top-2 right-2">
                            <?php if ($pelicula['estado'] == 1): ?>
                            <span class="px-2 py-1 text-xs font-medium bg-green-500 text-white rounded-full shadow">
                                Activa
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs font-medium bg-red-500 text-white rounded-full shadow">
                                Inactiva
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Duracion -->
                        <div class="absolute bottom-2 left-2">
                            <span class="px-2 py-1 text-xs font-medium bg-black bg-opacity-70 text-white rounded">
                                <i class="fas fa-clock mr-1"></i>
                                <?php echo PeliculaModel::formatDuration($pelicula['duracion']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2" title="<?php echo htmlspecialchars($pelicula['nombre']); ?>">
                            <?php echo htmlspecialchars($pelicula['nombre']); ?>
                        </h3>

                        <!-- Tags de idiomas y formatos -->
                        <div class="flex flex-wrap gap-1 mb-3">
                            <?php if ($pelicula['idiomas_texto']): ?>
                                <?php foreach (explode(', ', $pelicula['idiomas_texto']) as $idioma): ?>
                                <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">
                                    <?php echo htmlspecialchars($idioma); ?>
                                </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if ($pelicula['formatos_texto']): ?>
                                <?php foreach (explode(', ', $pelicula['formatos_texto']) as $formato): ?>
                                <span class="px-2 py-0.5 text-xs bg-purple-100 text-purple-800 rounded">
                                    <?php echo htmlspecialchars($formato); ?>
                                </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Sinopsis truncada -->
                        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                            <?php echo htmlspecialchars(substr($pelicula['sinopsis'], 0, 100)) . (strlen($pelicula['sinopsis']) > 100 ? '...' : ''); ?>
                        </p>

                        <!-- Acciones -->
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <div class="flex gap-2">
                                <button onclick="toggleEstado(<?php echo $pelicula['id_pelicula']; ?>, <?php echo $pelicula['estado']; ?>, 'pelicula_api.php')"
                                        class="p-2 text-yellow-600 hover:bg-yellow-50 rounded" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $pelicula['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                
                                <a href="editar.php?id=<?php echo $pelicula['id_pelicula']; ?>" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <button onclick="confirmDelete(<?php echo $pelicula['id_pelicula']; ?>, '<?php echo htmlspecialchars(addslashes($pelicula['nombre'])); ?>', 'pelicula_api.php')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <a href="editar.php?id=<?php echo $pelicula['id_pelicula']; ?>" 
                               class="text-sm text-blue-600 hover:underline">
                                Ver detalles
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-6 text-sm text-gray-500">
                Total: <span id="totalPeliculas"><?php echo count($peliculas); ?></span> peliculas
            </div>
        </main>

        <script>
            function filtrarPeliculas() {
                const busqueda = document.getElementById('buscador').value.toLowerCase();
                const estado = document.getElementById('filtroEstado').value;
                const cards = document.querySelectorAll('.pelicula-card');
                let visibles = 0;

                cards.forEach(card => {
                    const nombre = card.dataset.nombre;
                    const cardEstado = card.dataset.estado;
                    
                    const matchNombre = nombre.includes(busqueda);
                    const matchEstado = !estado || cardEstado === estado;

                    if (matchNombre && matchEstado) {
                        card.style.display = '';
                        visibles++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                document.getElementById('totalPeliculas').textContent = visibles;
                
                const contador = document.getElementById('contadorFiltro');
                if (busqueda || estado) {
                    contador.textContent = `(${visibles} resultados)`;
                } else {
                    contador.textContent = '';
                }
            }
        </script>

        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>

<?php include '../../partials/footer.php'; ?>
