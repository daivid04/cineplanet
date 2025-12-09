<?php
$pageTitle = 'Detalle del Socio';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/SocioAdminModel.php';

$model = new SocioAdminModel($conn);

// Verificar ID
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header('Location: index.php');
    exit;
}

$socio = $model->getById($id);
if (!$socio) {
    header('Location: index.php');
    exit;
}

$comprasInfo = $model->getComprasInfo($id);
$comprasCount = $model->countCompras($id);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Detalle del Socio</h1>
                    <p class="text-gray-600">Información completa del socio</p>
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
                        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-8">
                            <div class="flex items-center">
                                <div class="h-20 w-20 rounded-full bg-white flex items-center justify-center shadow-lg">
                                    <span class="text-blue-600 font-bold text-2xl">
                                        <?php echo strtoupper(substr($socio['nombre'], 0, 1) . substr($socio['apellido'], 0, 1)); ?>
                                    </span>
                                </div>
                                <div class="ml-6 text-white">
                                    <h2 class="text-2xl font-bold">
                                        <?php echo htmlspecialchars($socio['nombre'] . ' ' . $socio['apellido']); ?>
                                    </h2>
                                    <p class="opacity-90">
                                        <i class="fas fa-envelope mr-1"></i>
                                        <?php echo htmlspecialchars($socio['correo']); ?>
                                    </p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                                     <?php echo $socio['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <i class="fas <?php echo $socio['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                            <?php echo $socio['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos Personales -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-id-card mr-2 text-blue-600"></i>
                            Datos Personales
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm text-gray-500">ID Usuario</label>
                                <p class="text-gray-900 font-medium"><?php echo $socio['id_usuario']; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Documento (DNI)</label>
                                <p class="text-gray-900 font-medium">
                                    <i class="fas fa-id-card mr-1 text-gray-400"></i>
                                    <?php echo htmlspecialchars($socio['documento']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Género</label>
                                <p class="text-gray-900 font-medium">
                                    <?php if ($socio['genero'] === 'Masculino'): ?>
                                        <i class="fas fa-mars text-blue-500 mr-1"></i>
                                    <?php elseif ($socio['genero'] === 'Femenino'): ?>
                                        <i class="fas fa-venus text-pink-500 mr-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-genderless text-gray-500 mr-1"></i>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($socio['genero']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-500">Fecha de Nacimiento</label>
                                <p class="text-gray-900 font-medium">
                                    <i class="fas fa-birthday-cake mr-1 text-pink-400"></i>
                                    <?php echo date('d/m/Y', strtotime($socio['fecha_nacimiento'])); ?>
                                    <span class="text-gray-500 text-sm ml-1">(<?php echo $socio['edad']; ?> años)</span>
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
                                <p>Este socio aún no tiene compras registradas</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Columna Lateral -->
                <div class="space-y-6">
                    <!-- Tarjeta de Membresía -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-white font-bold text-lg">
                                    <i class="fas fa-crown mr-2"></i>
                                    Membresía
                                </h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="text-center mb-6">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-yellow-100 text-yellow-800">
                                    <?php echo htmlspecialchars($socio['tipo_socio_nombre']); ?>
                                </span>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-pink-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-candy-cane text-pink-500 mr-3"></i>
                                        <span class="text-gray-700">Desc. Dulcería</span>
                                    </div>
                                    <span class="font-bold text-pink-600"><?php echo $socio['desc_dulces']; ?>%</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-ticket-alt text-blue-500 mr-3"></i>
                                        <span class="text-gray-700">Desc. Boletos</span>
                                    </div>
                                    <span class="font-bold text-blue-600"><?php echo $socio['desc_boleto']; ?>%</span>
                                </div>
                                
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-coins text-yellow-500 mr-3"></i>
                                        <span class="text-gray-700">Puntos por Sol</span>
                                    </div>
                                    <span class="font-bold text-yellow-600"><?php echo $socio['puntos_por_sol']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones Rápidas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                            Acciones Rápidas
                        </h3>
                        <div class="space-y-3">
                            <button onclick="toggleEstadoSocio()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg
                                           <?php echo $socio['estado'] == 1 ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?>">
                                <i class="fas <?php echo $socio['estado'] == 1 ? 'fa-user-slash' : 'fa-user-check'; ?>"></i>
                                <?php echo $socio['estado'] == 1 ? 'Desactivar Socio' : 'Activar Socio'; ?>
                            </button>
                            
                            <button onclick="showResetPasswordModal()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-yellow-100 text-yellow-700 hover:bg-yellow-200">
                                <i class="fas fa-key"></i>
                                Resetear Contraseña
                            </button>
                            
                            <?php if ($comprasCount == 0): ?>
                            <button onclick="confirmDeleteSocio()" 
                                    class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">
                                <i class="fas fa-trash"></i>
                                Eliminar Socio
                            </button>
                            <?php else: ?>
                            <div class="text-center text-sm text-gray-500 p-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                No se puede eliminar: tiene compras
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Info del Sistema -->
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                        <p class="mb-1">
                            <strong>ID:</strong> <?php echo $socio['id_usuario']; ?>
                        </p>
                        <p class="mb-1">
                            <strong>Tipo Membresía ID:</strong> <?php echo $socio['id_tipo_socio']; ?>
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
                <div id="modalInput" class="hidden mb-4">
                    <input type="password" id="newPassword" class="w-full border rounded-lg px-3 py-2" placeholder="Nueva contraseña (mínimo 6 caracteres)">
                </div>
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

    <script>
        const API_URL = '../../api/socio_admin_api.php';
        const SOCIO_ID = <?php echo $id; ?>;
        const ESTADO_ACTUAL = <?php echo $socio['estado']; ?>;

        function toggleEstadoSocio() {
            const nuevoEstado = ESTADO_ACTUAL == 1 ? 'inactivo' : 'activo';
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado a "${nuevoEstado}"?`,
                'fa-question-circle text-blue-500',
                async () => {
                    const response = await fetch(`${API_URL}?id=${SOCIO_ID}&action=toggle`, {
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

        function showResetPasswordModal() {
            showModal(
                'Resetear Contraseña',
                'Ingrese la nueva contraseña para este socio:',
                'fa-key text-yellow-500',
                async () => {
                    const newPassword = document.getElementById('newPassword').value;
                    if (!newPassword || newPassword.length < 6) {
                        alert('La contraseña debe tener al menos 6 caracteres');
                        return;
                    }
                    
                    const response = await fetch(`${API_URL}?id=${SOCIO_ID}&action=reset_password`, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ contrasena: newPassword })
                    });
                    const data = await response.json();
                    if (data.success) {
                        alert('Contraseña actualizada correctamente');
                        closeModal();
                    } else {
                        alert('Error: ' + data.message);
                    }
                },
                true // showInput
            );
        }

        function confirmDeleteSocio() {
            showModal(
                'Eliminar Socio',
                '¿Estás seguro de eliminar este socio? Esta acción no se puede deshacer.',
                'fa-exclamation-triangle text-red-500',
                async () => {
                    const response = await fetch(`${API_URL}?id=${SOCIO_ID}`, {
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

        function showModal(title, message, iconClass, onConfirm, showInput = false) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('modalIcon').className = 'fas ' + iconClass + ' text-4xl mb-4';
            document.getElementById('modalInput').classList.toggle('hidden', !showInput);
            
            if (showInput) {
                document.getElementById('newPassword').value = '';
            }
            
            document.getElementById('btnConfirm').onclick = async () => {
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
            if (e.key === 'Escape') closeModal();
        });
    </script>

<?php include '../../partials/footer.php'; ?>
