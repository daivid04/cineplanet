<?php
$pageTitle = 'Funciones';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/FuncionModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/PeliculaModel.php';

$funcionModel = new FuncionModel($conn);
$sedeModel = new SedeModel($conn);
$peliculaModel = new PeliculaModel($conn);

$funciones = $funcionModel->getAll();
$sedes = $sedeModel->getAll(true);
$peliculas = $peliculaModel->getAll(true);

// Estadísticas
$totalFunciones = count($funciones);
$funcionesActivas = array_filter($funciones, fn($f) => $f['estado'] == 1);
$funcionesFuturas = array_filter($funciones, fn($f) => strtotime($f['fecha'] . ' ' . $f['hora']) >= time() && $f['estado'] == 1);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Funciones</h1>
                    <p class="text-gray-600">Gestiona las funciones de cine por sede y sala</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Nueva Función
                </a>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Funciones</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo $totalFunciones; ?></p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Activas</p>
                            <p class="text-2xl font-bold text-green-600"><?php echo count($funcionesActivas); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Próximas</p>
                            <p class="text-2xl font-bold text-purple-600"><?php echo count($funcionesFuturas); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Películas en Cartelera</p>
                            <p class="text-2xl font-bold text-orange-600"><?php echo count(array_unique(array_column($funcionesActivas, 'id_pelicula'))); ?></p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-film text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Filtro Sede -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Sede:</label>
                        <select id="filtroSede" onchange="filtrarFunciones()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las sedes</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?php echo $sede['id_sede']; ?>">
                                    <?php echo htmlspecialchars($sede['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro Película -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Película:</label>
                        <select id="filtroPelicula" onchange="filtrarFunciones()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todas las películas</option>
                            <?php foreach ($peliculas as $pelicula): ?>
                                <option value="<?php echo $pelicula['id_pelicula']; ?>">
                                    <?php echo htmlspecialchars($pelicula['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Filtro Fecha -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Fecha:</label>
                        <input type="date" id="filtroFecha" onchange="filtrarFunciones()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Filtro Estado -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 block mb-2">Estado:</label>
                        <select id="filtroEstado" onchange="filtrarFunciones()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Todos</option>
                            <option value="1">Activas</option>
                            <option value="0">Inactivas</option>
                            <option value="futuras">Próximas</option>
                            <option value="pasadas">Pasadas</option>
                        </select>
                    </div>
                </div>
                
                <div class="mt-3 flex justify-between items-center">
                    <button onclick="limpiarFiltros()" class="text-sm text-gray-600 hover:text-blue-600">
                        <i class="fas fa-times mr-1"></i> Limpiar filtros
                    </button>
                    <span id="contadorFiltro" class="text-sm text-gray-500"></span>
                </div>
            </div>

            <!-- Tabla -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Película</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sede / Sala</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Boletos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaFunciones" class="bg-white divide-y divide-gray-200">
                        <?php if (empty($funciones)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-2"></i>
                                <p>No hay funciones registradas</p>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($funciones as $funcion): 
                            $fechaHora = strtotime($funcion['fecha'] . ' ' . $funcion['hora']);
                            $esFutura = $fechaHora >= time();
                            $horaFin = FuncionModel::calcularHoraFin($funcion['hora'], $funcion['pelicula_duracion']);
                        ?>
                        <tr class="hover:bg-gray-50 fila-funcion" 
                            data-sede="<?php echo $funcion['id_sede']; ?>"
                            data-pelicula="<?php echo $funcion['id_pelicula']; ?>"
                            data-fecha="<?php echo $funcion['fecha']; ?>"
                            data-estado="<?php echo $funcion['estado']; ?>"
                            data-futura="<?php echo $esFutura ? '1' : '0'; ?>">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($funcion['pelicula_imagen']); ?>" 
                                         alt="<?php echo htmlspecialchars($funcion['pelicula_nombre']); ?>"
                                         class="w-10 h-14 object-cover rounded"
                                         onerror="this.src='https://via.placeholder.com/40x56?text=N/A'">
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($funcion['pelicula_nombre']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo FuncionModel::formatDuration($funcion['pelicula_duracion']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($funcion['fecha'])); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo strftime('%A', strtotime($funcion['fecha'])); ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900"><?php echo date('H:i', strtotime($funcion['hora'])); ?></p>
                                    <p class="text-xs text-gray-500">hasta <?php echo $horaFin; ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm">
                                    <p class="text-gray-900"><?php echo htmlspecialchars($funcion['sede_nombre']); ?></p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-door-open mr-1"></i>Sala <?php echo $funcion['numero_sala']; ?>
                                    </p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($funcion['boletos_vendidos'] > 0): ?>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                    <i class="fas fa-ticket-alt mr-1"></i>
                                    <?php echo $funcion['boletos_vendidos']; ?>
                                </span>
                                <?php else: ?>
                                <span class="text-xs text-gray-400">Sin ventas</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <?php if (!$esFutura): ?>
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                    <i class="fas fa-history mr-1"></i>Pasada
                                </span>
                                <?php elseif ($funcion['estado'] == 1): ?>
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                    Activa
                                </span>
                                <?php else: ?>
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                    Inactiva
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <?php if ($esFutura): ?>
                                <button onclick="toggleEstado(<?php echo $funcion['id_funcion']; ?>, <?php echo $funcion['estado']; ?>, 'funcion_api.php')"
                                        class="text-yellow-600 hover:text-yellow-900 mr-2" title="Cambiar estado">
                                    <i class="fas fa-toggle-<?php echo $funcion['estado'] == 1 ? 'on' : 'off'; ?>"></i>
                                </button>
                                <?php endif; ?>
                                
                                <a href="editar.php?id=<?php echo $funcion['id_funcion']; ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($funcion['boletos_vendidos'] == 0): ?>
                                <button onclick="confirmDelete(<?php echo $funcion['id_funcion']; ?>, '<?php echo htmlspecialchars($funcion['pelicula_nombre']); ?> - <?php echo date('d/m H:i', strtotime($funcion['fecha'] . ' ' . $funcion['hora'])); ?>', 'funcion_api.php')"
                                        class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <span class="text-gray-400" title="No se puede eliminar (hay boletos vendidos)">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-sm text-gray-500">
                Total: <span id="totalFunciones"><?php echo $totalFunciones; ?></span> funciones
            </div>
        </main>

        <script>
            function filtrarFunciones() {
                const sede = document.getElementById('filtroSede').value;
                const pelicula = document.getElementById('filtroPelicula').value;
                const fecha = document.getElementById('filtroFecha').value;
                const estado = document.getElementById('filtroEstado').value;
                const filas = document.querySelectorAll('.fila-funcion');
                let visibles = 0;

                filas.forEach(fila => {
                    let mostrar = true;

                    if (sede && fila.dataset.sede !== sede) mostrar = false;
                    if (pelicula && fila.dataset.pelicula !== pelicula) mostrar = false;
                    if (fecha && fila.dataset.fecha !== fecha) mostrar = false;
                    
                    if (estado === '1' && fila.dataset.estado !== '1') mostrar = false;
                    if (estado === '0' && fila.dataset.estado !== '0') mostrar = false;
                    if (estado === 'futuras' && fila.dataset.futura !== '1') mostrar = false;
                    if (estado === 'pasadas' && fila.dataset.futura !== '0') mostrar = false;

                    fila.style.display = mostrar ? '' : 'none';
                    if (mostrar) visibles++;
                });

                document.getElementById('totalFunciones').textContent = visibles;
                
                const filtrosActivos = [sede, pelicula, fecha, estado].filter(f => f).length;
                document.getElementById('contadorFiltro').textContent = 
                    filtrosActivos > 0 ? `${filtrosActivos} filtro(s) activo(s) - ${visibles} resultados` : '';
            }

            function limpiarFiltros() {
                document.getElementById('filtroSede').value = '';
                document.getElementById('filtroPelicula').value = '';
                document.getElementById('filtroFecha').value = '';
                document.getElementById('filtroEstado').value = '';
                filtrarFunciones();
            }
        </script>

<?php include '../../partials/footer.php'; ?>
