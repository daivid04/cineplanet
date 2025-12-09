<?php
$pageTitle = 'Editar Invitado';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/InvitadoAdminModel.php';

$model = new InvitadoAdminModel($conn);

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

$comprasCount = $model->countCompras($id);
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Editar Invitado</h1>
                    <p class="text-gray-600">
                        Editando: <strong><?php echo htmlspecialchars($invitado['nombre']); ?></strong>
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="ver.php?id=<?php echo $id; ?>" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-eye"></i>
                        Ver Detalle
                    </a>
                    <a href="index.php" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i>
                        Volver
                    </a>
                </div>
            </div>

            <?php if ($comprasCount > 0): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-yellow-500 mr-3"></i>
                    <p class="text-yellow-700">
                        Este invitado tiene <strong><?php echo $comprasCount; ?></strong> compra(s) registrada(s).
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
                <form id="formInvitado" class="space-y-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" required maxlength="100"
                               value="<?php echo htmlspecialchars($invitado['nombre']); ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                            Correo Electrónico
                            <span class="text-gray-400 text-xs">(opcional)</span>
                        </label>
                        <input type="email" id="correo" name="correo"
                               value="<?php echo htmlspecialchars($invitado['correo'] ?? ''); ?>"
                               class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="ejemplo@correo.com">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <div class="flex items-center mt-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $invitado['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                <i class="fas <?php echo $invitado['estado'] == 1 ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-1"></i>
                                <?php echo $invitado['estado'] == 1 ? 'Activo' : 'Inactivo'; ?>
                            </span>
                            <button type="button" onclick="toggleEstadoInvitado()" 
                                    class="ml-3 text-sm text-blue-600 hover:text-blue-800">
                                Cambiar estado
                            </button>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-4 pt-4 border-t">
                        <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Cambios
                        </button>
                        <a href="index.php" 
                           class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Modal de confirmación -->
    <div id="modalConfirm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <i class="fas fa-question-circle text-4xl text-blue-500 mb-4"></i>
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

    <script>
        const API_URL = '../../api/invitado_admin_api.php';
        const INVITADO_ID = <?php echo $id; ?>;
        const ESTADO_ACTUAL = <?php echo $invitado['estado']; ?>;

        function toggleEstadoInvitado() {
            const nuevoEstado = ESTADO_ACTUAL == 1 ? 'inactivo' : 'activo';
            showModal(
                'Cambiar estado',
                `¿Deseas cambiar el estado a "${nuevoEstado}"?`,
                async () => {
                    try {
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
                    } catch (error) {
                        alert('Error al cambiar estado');
                    }
                }
            );
        }

        document.getElementById('formInvitado').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                nombre: document.getElementById('nombre').value.trim(),
                correo: document.getElementById('correo').value.trim() || null
            };

            if (formData.nombre.length < 2) {
                alert('El nombre debe tener al menos 2 caracteres');
                return;
            }

            try {
                const response = await fetch(`${API_URL}?id=${INVITADO_ID}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Invitado actualizado correctamente');
                    window.location.href = 'index.php';
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al actualizar el invitado');
            }
        });

        function showModal(title, message, onConfirm) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            document.getElementById('btnConfirm').onclick = () => {
                closeModal();
                if (onConfirm) onConfirm();
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
