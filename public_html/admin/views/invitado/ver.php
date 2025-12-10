<?php
$pageTitle = 'Detalle del Invitado';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/InvitadoAdminModel.php';
require_once __DIR__ . '/../../../../src/models/SocioAdminModel.php';

$model = new InvitadoAdminModel($conn);
$socioModel = new SocioAdminModel($conn);

// Verificar ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: index.php');
    exit;
}

$invitado = $model->getById($id);
if (!$invitado) {
    header('Location: index.php');
    exit;
}

$comprasInfo = $model->getComprasInfo($id);
$comprasCount = $model->countCompras($id);
$tiposSocio = $socioModel->getTiposSocio(true);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Detalle del Invitado</h1>
                    <p class="text-gray-600">Información completa del invitado</p>
                </div>
                <div class="flex gap-2">
                    <a href="editar.php?id=<?php echo $id; ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                    <a href="index.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tarjeta de Perfil -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-8">
                            <div class="flex items-center">
                                <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                    <span class="text-yellow-600 font-bold text-2xl">
                                        <?php echo strtoupper(substr($invitado['nombre'], 0, 2)); ?>
                                    </span>
                                </div>
                                <div class="ml-6 text-white">
                                    <h2 class="text-2xl font-bold">
                                        <?php echo htmlspecialchars($invitado['nombre']); ?>
                                    </h2>
                                    <?php if (!empty($invitado['correo'])): ?>
                                        <p class="opacity-90">
                                            <i class="fas fa-envelope mr-1"></i>
                                            <?php echo htmlspecialchars($invitado['correo']); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="opacity-75 italic">
                                            <i class="fas fa-envelope mr-1"></i>
                                            Sin correo registrado
                                        </p>
                                    <?php endif; ?>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-user-tag mr-1"></i>
                                            Invitado
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                     <?php echo $invitado['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <i class="fas <?php echo $invitado['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                            <?php echo $invitado['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Info del Usuario -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                            Información del Usuario
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm text-gray-500">ID Usuario</label>
                                <p class="text-gray-900 font-medium"><?php echo $invitado['id_usuario']; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Tipo de Usuario</label>
                                <p class="text-gray-900 font-medium">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-user mr-1"></i>
                                        Invitado
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Correo Electrónico</label>
                                <p class="text-gray-900 font-medium">
                                    <?php if (!empty($invitado['correo'])): ?>
                                        <i class="fas fa-envelope mr-1 text-gray-400"></i>
                                        <?php echo htmlspecialchars($invitado['correo']); ?>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic">No registrado</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Estado</label>
                                <p class="text-gray-900 font-medium">
                                    <?php echo $invitado['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de Compras -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-shopping-cart mr-2 text-green-600"></i>
                            Historial de Compras
                        </h3>
                        
                        <?php if ($comprasCount > 0): ?>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-3xl font-bold text-blue-600"><?php echo $comprasInfo['total'] ?? 0; ?></p>
                                    <p class="text-sm text-gray-500">Total Compras</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-3xl font-bold text-green-600">
                                        S/ <?php echo number_format($comprasInfo['monto_total'] ?? 0, 2); ?>
                                    </p>
                                    <p class="text-sm text-gray-500">Monto Total</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo $comprasInfo['primera_compra'] ? date('d/m/Y', strtotime($comprasInfo['primera_compra'])) : '-'; ?>
                                    </p>
                                    <p class="text-sm text-gray-500">Primera Compra</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 text-center">
                                    <p class="text-sm font-medium text-gray-900">
                                        <?php echo $comprasInfo['ultima_compra'] ? date('d/m/Y', strtotime($comprasInfo['ultima_compra'])) : '-'; ?>
                                    </p>
                                    <p class="text-sm text-gray-500">Última Compra</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-shopping-cart text-4xl mb-2 opacity-50"></i>
                                <p>Este invitado aún no tiene compras registradas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="space-y-6">
                    <!-- Acciones Rápidas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                            Acciones Rápidas
                        </h3>
                        <div class="space-y-3">
                            <button onclick="toggleEstadoInvitado()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg
                                           <?php echo $invitado['estado'] == 1 ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?>">
                                <i class="fas <?php echo $invitado['estado'] == 1 ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                <?php echo $invitado['estado'] == 1 ? 'Desactivar' : 'Activar'; ?>
                            </button>
                            
                            <button onclick="showConvertirModal()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200">
                                <i class="fas fa-user-plus"></i>
                                Convertir a Socio
                            </button>
                            
                            <?php if ($comprasCount == 0): ?>
                            <button onclick="confirmDeleteInvitado()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                                <i class="fas fa-trash"></i>
                                Eliminar
                            </button>
                            <?php else: ?>
                            <div class="text-center text-sm text-gray-500 p-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                No se puede eliminar: tiene compras
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Beneficios de ser Socio -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-6 border border-purple-200">
                        <h3 class="text-lg font-medium text-purple-900 mb-4">
                            <i class="fas fa-crown mr-2 text-purple-600"></i>
                            ¿Convertir a Socio?
                        </h3>
                        <p class="text-purple-700 text-sm mb-4">
                            Al convertir este invitado a socio, podrá acceder a:
                        </p>
                        <ul class="space-y-2 text-sm text-purple-700">
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                                Descuentos en boletos
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                                Descuentos en dulcería
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                                Acumulación de puntos
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-purple-500 mr-2"></i>
                                Promociones exclusivas
                            </li>
                        </ul>
                    </div>

                    <!-- Info del Sistema -->
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                        <p>
                            <strong>ID Usuario:</strong> <?php echo $invitado['id_usuario']; ?>
                        </p>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i id="modalIcon" class="fas fa-question-circle text-4xl text-blue-500 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2" id="modalTitle">Confirmar</h3>
                <p class="text-gray-500 mb-6" id="modalMessage">¿Estás seguro?</p>
            </div>
            <div class="flex gap-3 justify-center">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button id="btnConfirm" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Conversión a Socio -->
    <div id="modalConvertir" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fas fa-user-plus text-purple-600 mr-2"></i>
                    Convertir a Socio
                </h3>
                <button onclick="closeConvertirModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-600 mb-4">
                Convirtiendo a: <strong><?php echo htmlspecialchars($invitado['nombre']); ?></strong>
            </p>
            <form id="formConvertir" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellido *</label>
                    <input type="text" id="conv_apellido" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Documento (DNI) *</label>
                        <input type="text" id="conv_documento" required maxlength="8" pattern="[0-9]{8}" class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Género *</label>
                        <select id="conv_genero" required class="w-full border rounded-lg px-3 py-2">
                            <option value="">Seleccione</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                    <input type="date" id="conv_fecha_nacimiento" required class="w-full border rounded-lg px-3 py-2">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Membresía *</label>
                    <select id="conv_tipo_socio" required class="w-full border rounded-lg px-3 py-2">
                        <option value="">Seleccione tipo</option>
                        <?php foreach ($tiposSocio as $tipo): ?>
                            <option value="<?php echo $tipo['id_socio']; ?>">
                                <?php echo htmlspecialchars($tipo['nombre']); ?> (<?php echo $tipo['desc_boleto']; ?>% desc.)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña *</label>
                    <input type="password" id="conv_contrasena" required minlength="6" class="w-full border rounded-lg px-3 py-2" placeholder="Mínimo 6 caracteres">
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="submit" class="flex-1 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700">
                        <i class="fas fa-user-plus mr-2"></i>Convertir
                    </button>
                    <button type="button" onclick="closeConvertirModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const API_URL = '../../api/invitado_admin_api.php';
        const INVITADO_ID = <?php echo $id; ?>;
        const ESTADO_ACTUAL = <?php echo $invitado['estado']; ?>;

        function toggleEstadoInvitado() {
            const nuevoEstado = ESTADO_ACTUAL == 1 ? 'inactivo' : 'activo';
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado a "${nuevoEstado}"?`,
                'fa-question-circle text-blue-500',
                async () => {
                    const response = await fetch(`${API_URL}?id=${INVITADO_ID}&action=toggle`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            );
        }

        function confirmDeleteInvitado() {
            showModal(
                'Eliminar Invitado',
                '¿Estás seguro de eliminar este invitado? Esta acción no se puede deshacer.',
                'fa-exclamation-triangle text-red-500',
                async () => {
                    const response = await fetch(`${API_URL}?id=${INVITADO_ID}`, {
                        method: 'DELETE'
                    });
                    const data = await response.json();
                    if (data.success) {
                        window.location.href = 'index.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                }
            );
        }

        function showConvertirModal() {
            // Configurar fecha máxima
            const fechaInput = document.getElementById('conv_fecha_nacimiento');
            const hoy = new Date();
            const fechaMax = new Date(hoy.getFullYear() - 13, hoy.getMonth(), hoy.getDate());
            fechaInput.max = fechaMax.toISOString().split('T')[0];
            
            document.getElementById('modalConvertir').classList.remove('hidden');
            document.getElementById('modalConvertir').classList.add('flex');
        }

        function closeConvertirModal() {
            document.getElementById('modalConvertir').classList.add('hidden');
            document.getElementById('modalConvertir').classList.remove('flex');
            document.getElementById('formConvertir').reset();
        }

        // Validar DNI solo números
        document.getElementById('conv_documento').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 8);
        });

        document.getElementById('formConvertir').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const data = {
                apellido: document.getElementById('conv_apellido').value.trim(),
                documento: document.getElementById('conv_documento').value,
                genero: document.getElementById('conv_genero').value,
                fecha_nacimiento: document.getElementById('conv_fecha_nacimiento').value,
                id_tipo_socio: document.getElementById('conv_tipo_socio').value,
                contrasena: document.getElementById('conv_contrasena').value
            };
            
            if (data.documento.length !== 8) {
                alert('El documento debe tener exactamente 8 dígitos');
                return;
            }
            
            try {
                const response = await fetch(`${API_URL}?id=${INVITADO_ID}&convertir=1`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Invitado convertido a socio correctamente');
                    window.location.href = '../socio/ver.php?id=' + INVITADO_ID;
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al convertir');
            }
        });

        function showModal(title, message, iconClass, onConfirm) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalIcon').className = 'fas ' + iconClass + ' text-4xl mb-4';
            
            document.getElementById('btnConfirm').onclick = async () => {
                closeModal();
                try {
                    await onConfirm();
                } catch (error) {
                    console.error('Error:', error);
                    alert('Ocurrió un error');
                }
            };
            
            document.getElementById('modalConfirm').classList.remove('hidden');
            document.getElementById('modalConfirm').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('modalConfirm').classList.add('hidden');
            document.getElementById('modalConfirm').classList.remove('flex');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeConvertirModal();
            }
        });
    </script>

<?php include '../../partials/footer.php'; ?>
