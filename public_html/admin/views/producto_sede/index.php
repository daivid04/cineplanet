<?php
$pageTitle = 'Inventario por Sede';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/ProductoSedeModel.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/ProductoModel.php';

$productoSedeModel = new ProductoSedeModel($conn);
$sedeModel = new SedeModel($conn);
$productoModel = new ProductoModel($conn);

// Filtros
$sedeSeleccionada = $_GET['sede'] ?? null;
$soloConStock = isset($_GET['con_stock']) && $_GET['con_stock'] === '1';

// Obtener datos
$sedes = $sedeModel->getAll(true);
$productos = $productoModel->getAll(true);
$inventario = $productoSedeModel->getAll($sedeSeleccionada, null, $soloConStock);

// Estadísticas globales
$productosSinStock = $productoSedeModel->getProductosSinStock($sedeSeleccionada);
$productosBajoStock = $productoSedeModel->getProductosBajoStock(10, $sedeSeleccionada);

// Agrupar por sede para vista
$inventarioPorSede = [];
foreach ($inventario as $item) {
    $sedeId = $item['id_sede'];
    if (!isset($inventarioPorSede[$sedeId])) {
        $inventarioPorSede[$sedeId] = [
            'sede_nombre' => $item['sede_nombre'],
            'ciudad_nombre' => $item['ciudad_nombre'],
            'productos' => []
        ];
    }
    $inventarioPorSede[$sedeId]['productos'][] = $item;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Inventario por Sede</h1>
                    <p class="text-gray-600">Gestiona el stock de productos en cada sede</p>
                </div>
                <a href="crear.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Asignar Producto
                </a>
            </div>

            <!-- Alertas -->
            <?php if (count($productosSinStock) > 0): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <div>
                        <p class="font-medium text-red-800">
                            <?php echo count($productosSinStock); ?> producto(s) sin stock
                        </p>
                        <p class="text-sm text-red-700">
                            <?php 
                            $nombres = array_slice(array_column($productosSinStock, 'producto_nombre'), 0, 3);
                            echo implode(', ', $nombres);
                            if (count($productosSinStock) > 3) echo '...';
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (count($productosBajoStock) > 0): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                    <div>
                        <p class="font-medium text-yellow-800">
                            <?php echo count($productosBajoStock); ?> producto(s) con stock bajo (≤10)
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sede</label>
                        <select id="filtroSede" onchange="aplicarFiltros()" 
                                class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todas las sedes</option>
                            <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>" 
                                    <?php echo $sedeSeleccionada == $sede['id_sede'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sede['nombre'] . ' - ' . $sede['ciudad_nombre']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="filtroStock" onchange="aplicarFiltros()" 
                                   <?php echo $soloConStock ? 'checked' : ''; ?>
                                   class="rounded text-blue-600">
                            <span class="text-sm text-gray-700">Solo con stock</span>
                        </label>
                    </div>
                    <div>
                        <button onclick="limpiarFiltros()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <?php if (empty($inventarioPorSede)): ?>
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">No hay productos asignados a sedes</p>
                <a href="crear.php" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                    <i class="fas fa-plus mr-2"></i>Asignar primer producto
                </a>
            </div>
            <?php else: ?>

            <!-- Vista por sedes -->
            <?php foreach ($inventarioPorSede as $sedeId => $sedeData): ?>
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b bg-gray-50 rounded-t-lg">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-building text-blue-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800">
                                    <?php echo htmlspecialchars($sedeData['sede_nombre']); ?>
                                </h2>
                                <p class="text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?php echo htmlspecialchars($sedeData['ciudad_nombre']); ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm text-gray-500">
                                <?php echo count($sedeData['productos']); ?> productos
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($sedeData['productos'] as $item): ?>
                            <tr class="hover:bg-gray-50" id="row-<?php echo $item['id_producto_sede']; ?>">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-box text-indigo-600"></i>
                                        <span class="font-medium"><?php echo htmlspecialchars($item['producto_nombre']); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-gray-900">S/ <?php echo number_format($item['precio_unitario'], 2); ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php
                                    $stockClass = $item['stock'] == 0 ? 'bg-red-100 text-red-800' : 
                                                  ($item['stock'] <= 10 ? 'bg-yellow-100 text-yellow-800' : 
                                                  'bg-green-100 text-green-800');
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-sm font-medium <?php echo $stockClass; ?>">
                                        <?php echo $item['stock']; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-gray-600">
                                        S/ <?php echo number_format($item['stock'] * $item['precio_unitario'], 2); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if ($item['producto_estado'] == 1): ?>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Activo
                                    </span>
                                    <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                        Inactivo
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick="ajustarStock(<?php echo $item['id_producto_sede']; ?>, 'incrementar')"
                                                class="p-2 bg-green-100 text-green-600 rounded hover:bg-green-200" title="Agregar stock">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button onclick="ajustarStock(<?php echo $item['id_producto_sede']; ?>, 'decrementar')"
                                                class="p-2 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200" title="Quitar stock">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button onclick="editarStock(<?php echo $item['id_producto_sede']; ?>, <?php echo $item['stock']; ?>)"
                                                class="p-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200" title="Editar stock">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $item['id_producto_sede']; ?>, '<?php echo htmlspecialchars($item['producto_nombre']); ?>')"
                                                class="p-2 bg-red-100 text-red-600 rounded hover:bg-red-200" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

            <?php endif; ?>

            <div class="mt-4 text-sm text-gray-500">
                Total: <?php echo count($inventario); ?> registros
            </div>
        </main>

<!-- Modal Editar Stock -->
<div id="modalStock" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <h3 class="text-lg font-semibold mb-4">Ajustar Stock</h3>
        <form id="formStock" onsubmit="guardarStock(event)">
            <input type="hidden" id="stockId">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nuevo Stock</label>
                <input type="number" id="stockValor" min="0" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModal()" 
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajustar Cantidad -->
<div id="modalCantidad" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-96">
        <h3 id="modalCantidadTitulo" class="text-lg font-semibold mb-4">Ajustar Cantidad</h3>
        <form id="formCantidad" onsubmit="guardarCantidad(event)">
            <input type="hidden" id="cantidadId">
            <input type="hidden" id="cantidadAccion">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                <input type="number" id="cantidadValor" min="1" value="1" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="cerrarModalCantidad()" 
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" id="btnCantidad"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Aplicar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const API_URL = '../api/producto_sede_api.php';

function aplicarFiltros() {
    const sede = document.getElementById('filtroSede').value;
    const conStock = document.getElementById('filtroStock').checked;
    
    let url = 'index.php?';
    if (sede) url += `sede=${sede}&`;
    if (conStock) url += `con_stock=1&`;
    
    window.location.href = url;
}

function limpiarFiltros() {
    window.location.href = 'index.php';
}

function editarStock(id, stockActual) {
    document.getElementById('stockId').value = id;
    document.getElementById('stockValor').value = stockActual;
    document.getElementById('modalStock').classList.remove('hidden');
    document.getElementById('modalStock').classList.add('flex');
}

function cerrarModal() {
    document.getElementById('modalStock').classList.add('hidden');
    document.getElementById('modalStock').classList.remove('flex');
}

async function guardarStock(e) {
    e.preventDefault();
    
    const id = document.getElementById('stockId').value;
    const stock = document.getElementById('stockValor').value;
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ stock: parseInt(stock) })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

function ajustarStock(id, accion) {
    document.getElementById('cantidadId').value = id;
    document.getElementById('cantidadAccion').value = accion;
    
    const titulo = accion === 'incrementar' ? 'Agregar Stock' : 'Quitar Stock';
    const btnClass = accion === 'incrementar' ? 'bg-green-600 hover:bg-green-700' : 'bg-yellow-600 hover:bg-yellow-700';
    
    document.getElementById('modalCantidadTitulo').textContent = titulo;
    document.getElementById('btnCantidad').className = `px-4 py-2 ${btnClass} text-white rounded-lg`;
    document.getElementById('cantidadValor').value = 1;
    
    document.getElementById('modalCantidad').classList.remove('hidden');
    document.getElementById('modalCantidad').classList.add('flex');
}

function cerrarModalCantidad() {
    document.getElementById('modalCantidad').classList.add('hidden');
    document.getElementById('modalCantidad').classList.remove('flex');
}

async function guardarCantidad(e) {
    e.preventDefault();
    
    const id = document.getElementById('cantidadId').value;
    const accion = document.getElementById('cantidadAccion').value;
    const cantidad = parseInt(document.getElementById('cantidadValor').value);
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion, cantidad })
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

async function confirmDelete(id, nombre) {
    if (!confirm(`¿Estás seguro de eliminar "${nombre}" de esta sede?\n\nEsta acción eliminará el registro de inventario.`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            location.reload();
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

// Cerrar modales con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
        cerrarModalCantidad();
    }
});
</script>

<?php include '../../partials/footer.php'; ?>
