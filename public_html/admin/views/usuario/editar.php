<?php
$pageTitle = 'Editar Usuario';
include '../../partials/header.php';
include '../../partials/sidebar.php';

require_once __DIR__ . '/../../../../src/services/conexion.php';
require_once __DIR__ . '/../../../../src/models/UsuarioAdminModel.php';

$model = new UsuarioAdminModel($conn);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: index.php');
    exit;
}

$usuario = $model->getDetallesCompletos($id);

if (!$usuario) {
    header('Location: index.php');
    exit;
}
?>

        <!-- Contenido Principal -->
        <main class="flex-1 p-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="index.php" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a usuarios
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Editar Usuario</h1>
                <p class="text-gray-600">Modifica la información del usuario</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Formulario -->
                <div class="lg:col-span-2">
                    <form id="formUsuario" class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user-edit text-blue-600"></i>
                            Datos del Usuario
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID Usuario</label>
                                <input type="text" value="<?php echo $usuario['id_usuario']; ?>" 
                                       class="w-full border rounded-lg px-3 py-2 bg-gray-100" disabled>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <input type="text" value="<?php echo $usuario['tipo_usuario']; ?>" 
                                       class="w-full border rounded-lg px-3 py-2 bg-gray-100" disabled>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="correo" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="correo" name="correo" required
                                   value="<?php echo htmlspecialchars($usuario['correo']); ?>"
                                   class="w-full border rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="usuario@ejemplo.com">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" name="estado" value="1" 
                                           <?php echo $usuario['estado'] == 1 ? 'checked' : ''; ?>
                                           class="mr-2 text-blue-600">
                                    <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Activo</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="estado" value="0" 
                                           <?php echo $usuario['estado'] == 0 ? 'checked' : ''; ?>
                                           class="mr-2 text-blue-600">
                                    <span class="text-red-600"><i class="fas fa-times-circle mr-1"></i>Inactivo</span>
                                </label>
                            </div>
                        </div>

                        <?php if ($usuario['total_compras'] > 0): ?>
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center gap-2 text-yellow-800">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="font-medium">Este usuario tiene <?php echo $usuario['total_compras']; ?> compra(s) registrada(s)</span>
                            </div>
                            <p class="text-sm text-yellow-700 mt-1">
                                Si desactivas este usuario, sus compras permanecerán en el historial.
                            </p>
                        </div>
                        <?php endif; ?>

                        <div class="flex justify-end gap-3">
                            <a href="index.php" class="px-4 py-2 border rounded-lg hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Panel Info -->
                <div class="lg:col-span-1">
                    <!-- Info del Tipo -->
                    <?php if ($usuario['tipo_usuario'] === 'Socio' && isset($usuario['socio'])): ?>
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user-tie text-purple-600"></i>
                            Datos de Socio
                        </h3>
                        
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-gray-500">Nombre:</span>
                                <span class="font-medium ml-2">
                                    <?php echo htmlspecialchars($usuario['socio']['nombre'] . ' ' . $usuario['socio']['apellido']); ?>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Documento:</span>
                                <span class="font-medium ml-2"><?php echo htmlspecialchars($usuario['socio']['documento']); ?></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Membresía:</span>
                                <span class="font-medium ml-2 text-purple-600">
                                    <?php echo htmlspecialchars($usuario['socio']['tipo_socio_nombre']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Para editar datos de socio, usa la sección de Socios.
                        </p>
                    </div>
                    <?php elseif ($usuario['tipo_usuario'] === 'Invitado' && isset($usuario['invitado'])): ?>
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-yellow-600"></i>
                            Datos de Invitado
                        </h3>
                        
                        <div class="text-sm">
                            <span class="text-gray-500">Nombre:</span>
                            <span class="font-medium ml-2"><?php echo htmlspecialchars($usuario['invitado']['nombre']); ?></span>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-question-circle text-gray-600"></i>
                            Sin Tipo Asignado
                        </h3>
                        
                        <p class="text-sm text-gray-600">
                            Este usuario no tiene un perfil de socio ni invitado asociado.
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Estadísticas -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-bar text-blue-600"></i>
                            Estadísticas
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-600">Total Compras</span>
                                <span class="text-lg font-bold text-blue-600"><?php echo $usuario['total_compras']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

<script>
const API_URL = '../../api/usuario_admin_api.php';
const usuarioId = <?php echo $usuario['id_usuario']; ?>;

document.getElementById('formUsuario').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const data = {
        correo: document.getElementById('correo').value,
        estado: parseInt(document.querySelector('input[name="estado"]:checked').value)
    };
    
    try {
        const response = await fetch(`${API_URL}?id=${usuarioId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Usuario actualizado correctamente');
            window.location.href = 'index.php';
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error de conexión');
    }
});
</script>

<?php include '../../partials/footer.php'; ?>
