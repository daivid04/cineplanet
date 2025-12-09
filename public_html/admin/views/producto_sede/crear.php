<?php
$pageTitle = 'Asignar Producto a Sede';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SedeModel.php';
require_once __DIR__ . '/../../../../src/models/ProductoModel.php';

$sedeModel = new SedeModel($conn);
$productoModel = new ProductoModel($conn);

$sedes = $sedeModel->getAll(true);
$productos = $productoModel->getAll(true);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al inventario
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Asignar Producto a Sede</h1>
                <p class="text-gray-600">Agrega un producto al inventario de una sede</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6">
                        <!-- Tabs -->
                        <div class="flex border-b mb-6">
                            <button id="tabSimple" onclick="cambiarTab('simple')" 
                                    class="px-4 py-2 font-medium text-blue-600 border-b-2 border-blue-600">
                                Asignación Simple
                            </button>
                            <button id="tabMasiva" onclick="cambiarTab('masiva')" 
                                    class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700">
                                Asignación Masiva
                            </button>
                        </div>

                        <!-- Formulario Simple -->
                        <form id="formSimple" onsubmit="guardarSimple(event)">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="producto" class="block text-sm font-medium text-gray-700 mb-1">
                                        Producto <span class="text-red-500">*</span>
                                    </label>
                                    <select id="producto" name="id_producto" required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                            onchange="mostrarInfoProducto()">
                                        <option value="">Seleccionar producto...</option>
                                        <?php foreach ($productos as $p): ?>
                                        <option value="<?php echo $p['id_producto']; ?>" 
                                                data-precio="<?php echo $p['precio_unitario']; ?>">
                                            <?php echo htmlspecialchars($p['nombre']); ?> - S/ <?php echo number_format($p['precio_unitario'], 2); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="sede" class="block text-sm font-medium text-gray-700 mb-1">
                                        Sede <span class="text-red-500">*</span>
                                    </label>
                                    <select id="sede" name="id_sede" required
                                            class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Seleccionar sede...</option>
                                        <?php foreach ($sedes as $s): ?>
                                        <option value="<?php echo $s['id_sede']; ?>">
                                            <?php echo htmlspecialchars($s['nombre'] . ' - ' . $s['ciudad_nombre']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">
                                    Stock Inicial <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="stock" name="stock" min="0" value="0" required
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="calcularValor()">
                                <p class="text-sm text-gray-500 mt-1">Cantidad inicial de unidades</p>
                            </div>

                            <!-- Info Producto -->
                            <div id="infoProducto" class="hidden mb-6 p-4 bg-blue-50 rounded-lg">
                                <h4 class="font-medium text-blue-800 mb-2">Información del Producto</h4>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Precio unitario:</span>
                                        <span id="infoPrecio" class="font-medium ml-2">-</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Valor inventario:</span>
                                        <span id="infoValor" class="font-medium ml-2">-</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <a href="index.php" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-save mr-2"></i>Guardar
                                </button>
                            </div>
                        </form>

                        <!-- Formulario Masivo -->
                        <form id="formMasivo" class="hidden" onsubmit="guardarMasivo(event)">
                            <div class="mb-4">
                                <label for="productoMasivo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Producto <span class="text-red-500">*</span>
                                </label>
                                <select id="productoMasivo" name="id_producto" required
                                        class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Seleccionar producto...</option>
                                    <?php foreach ($productos as $p): ?>
                                    <option value="<?php echo $p['id_producto']; ?>">
                                        <?php echo htmlspecialchars($p['nombre']); ?> - S/ <?php echo number_format($p['precio_unitario'], 2); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sedes <span class="text-red-500">*</span>
                                </label>
                                <div class="border rounded-lg p-4 max-h-60 overflow-y-auto">
                                    <div class="mb-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" id="checkAll" onchange="toggleAllSedes()"
                                                   class="rounded text-blue-600">
                                            <span class="font-medium text-gray-700">Seleccionar todas</span>
                                        </label>
                                    </div>
                                    <hr class="my-2">
                                    <?php foreach ($sedes as $s): ?>
                                    <label class="flex items-center gap-2 py-1 cursor-pointer hover:bg-gray-50">
                                        <input type="checkbox" name="sedes[]" value="<?php echo $s['id_sede']; ?>"
                                               class="sede-checkbox rounded text-blue-600">
                                        <span class="text-gray-700">
                                            <?php echo htmlspecialchars($s['nombre']); ?>
                                            <span class="text-gray-400">- <?php echo htmlspecialchars($s['ciudad_nombre']); ?></span>
                                        </span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label for="stockMasivo" class="block text-sm font-medium text-gray-700 mb-1">
                                    Stock Inicial (para todas las sedes)
                                </label>
                                <input type="number" id="stockMasivo" name="stock" min="0" value="0"
                                       class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div class="flex justify-end gap-3">
                                <a href="index.php" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                    Cancelar
                                </a>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    <i class="fas fa-layer-group mr-2"></i>Asignar a Sedes Seleccionadas
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Panel Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-blue-600"></i>
                            Información
                        </h3>
                        
                        <div class="space-y-4 text-sm text-gray-600">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <p>Asigna productos a sedes para gestionar el inventario de dulcería.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <p>Cada producto solo puede asignarse una vez por sede.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                <p>El stock inicial puede ser 0 si planeas agregar existencias después.</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <i class="fas fa-layer-group text-blue-500 mt-0.5"></i>
                                <p><strong>Asignación masiva:</strong> Asigna un producto a múltiples sedes a la vez.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen Productos -->
                    <div class="bg-white rounded-lg shadow p-6 mt-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                            Resumen
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm text-gray-600">Productos disponibles</span>
                                <span class="text-lg font-bold text-purple-600"><?php echo count($productos); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-600">Sedes activas</span>
                                <span class="text-lg font-bold text-blue-600"><?php echo count($sedes); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

<script>
const API_URL = '../../api/producto_sede_api.php';

function cambiarTab(tab) {
    const tabSimple = document.getElementById('tabSimple');
    const tabMasiva = document.getElementById('tabMasiva');
    const formSimple = document.getElementById('formSimple');
    const formMasivo = document.getElementById('formMasivo');
    
    if (tab === 'simple') {
        tabSimple.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        tabSimple.classList.remove('text-gray-500');
        tabMasiva.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        tabMasiva.classList.add('text-gray-500');
        formSimple.classList.remove('hidden');
        formMasivo.classList.add('hidden');
    } else {
        tabMasiva.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        tabMasiva.classList.remove('text-gray-500');
        tabSimple.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
        tabSimple.classList.add('text-gray-500');
        formMasivo.classList.remove('hidden');
        formSimple.classList.add('hidden');
    }
}

function mostrarInfoProducto() {
    const select = document.getElementById('producto');
    const option = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('infoProducto');
    
    if (select.value) {
        const precio = parseFloat(option.dataset.precio);
        document.getElementById('infoPrecio').textContent = `S/ ${precio.toFixed(2)}`;
        calcularValor();
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

function calcularValor() {
    const select = document.getElementById('producto');
    const option = select.options[select.selectedIndex];
    const stock = parseInt(document.getElementById('stock').value) || 0;
    
    if (select.value) {
        const precio = parseFloat(option.dataset.precio);
        const valor = precio * stock;
        document.getElementById('infoValor').textContent = `S/ ${valor.toFixed(2)}`;
    }
}

function toggleAllSedes() {
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.sede-checkbox');
    checkboxes.forEach(cb => cb.checked = checkAll.checked);
}

async function guardarSimple(e) {
    e.preventDefault();
    
    const data = {
        id_producto: parseInt(document.getElementById('producto').value),
        id_sede: parseInt(document.getElementById('sede').value),
        stock: parseInt(document.getElementById('stock').value)
    };
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Producto asignado correctamente');
            window.location.href = 'index.php';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}

async function guardarMasivo(e) {
    e.preventDefault();
    
    const checkboxes = document.querySelectorAll('.sede-checkbox:checked');
    if (checkboxes.length === 0) {
        alert('Selecciona al menos una sede');
        return;
    }
    
    const sedes = Array.from(checkboxes).map(cb => parseInt(cb.value));
    
    const data = {
        asignacion_masiva: true,
        id_producto: parseInt(document.getElementById('productoMasivo').value),
        sedes: sedes,
        stock: parseInt(document.getElementById('stockMasivo').value) || 0
    };
    
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            let mensaje = result.message;
            if (result.errores && result.errores.length > 0) {
                mensaje += '\n\nAdvertencias:\n' + result.errores.join('\n');
            }
            alert(mensaje);
            window.location.href = 'index.php';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
}
</script>

<?php include '../../partials/footer.php'; ?>
