<?php
$pageTitle = 'Ver Usuario';
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
            <div class="flex justify-between items-center mb-6">
                <div>
                    <a href="index.php" class="text-blue-600 hover:text-blue-800 mb-2 inline-block">
                        <i class="fas fa-arrow-left mr-2"></i>Volver a usuarios
                    </a>
                    <h1 class="text-3xl font-bold text-gray-800">Detalles del Usuario</h1>
                </div>
                <div class="flex gap-2">
                    <a href="editar.php?id=<?php echo $usuario['id_usuario']; ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        Editar
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Info Principal -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user-circle text-blue-600"></i>
                            Información General
                        </h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">ID Usuario</label>
                                <p class="text-lg font-semibold"><?php echo $usuario['id_usuario']; ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Estado</label>
                                <?php if ($usuario['estado'] == 1): ?>
                                <span class="px-3 py-1 text-sm font-medium bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-check-circle mr-1"></i>Activo
                                </span>
                                <?php else: ?>
                                <span class="px-3 py-1 text-sm font-medium bg-red-100 text-red-800 rounded-full">
                                    <i class="fas fa-times-circle mr-1"></i>Inactivo
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Correo Electrónico</label>
                                <p class="text-lg">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    <?php echo htmlspecialchars($usuario['correo']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Tipo de Usuario</label>
                                <?php 
                                $tipoClase = match($usuario['tipo_usuario']) {
                                    'Socio' => 'bg-purple-100 text-purple-800',
                                    'Invitado' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                                ?>
                                <span class="px-3 py-1 text-sm font-medium <?php echo $tipoClase; ?> rounded-full">
                                    <?php echo $usuario['tipo_usuario']; ?>
                                </span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Total Compras</label>
                                <p class="text-lg font-semibold text-blue-600"><?php echo $usuario['total_compras']; ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($usuario['tipo_usuario'] === 'Socio' && isset($usuario['socio'])): ?>
                    <!-- Info Socio -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user-tie text-purple-600"></i>
                            Información de Socio
                        </h2>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Nombre Completo</label>
                                <p class="text-lg font-semibold">
                                    <?php echo htmlspecialchars($usuario['socio']['nombre'] . ' ' . $usuario['socio']['apellido']); ?>
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Documento</label>
                                <p class="text-lg"><?php echo htmlspecialchars($usuario['socio']['documento']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Género</label>
                                <p class="text-lg"><?php echo htmlspecialchars($usuario['socio']['genero']); ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Fecha de Nacimiento</label>
                                <p class="text-lg">
                                    <?php echo date('d/m/Y', strtotime($usuario['socio']['fecha_nacimiento'])); ?>
                                </p>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Tipo de Membresía</label>
                                <span class="px-3 py-1 text-sm font-medium bg-indigo-100 text-indigo-800 rounded-full">
                                    <i class="fas fa-id-card mr-1"></i>
                                    <?php echo htmlspecialchars($usuario['socio']['tipo_socio_nombre']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($usuario['tipo_usuario'] === 'Invitado' && isset($usuario['invitado'])): ?>
                    <!-- Info Invitado -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-user text-yellow-600"></i>
                            Información de Invitado
                        </h2>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Nombre</label>
                            <p class="text-lg font-semibold">
                                <?php echo htmlspecialchars($usuario['invitado']['nombre']); ?>
                            </p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Panel Lateral -->
                <div class="lg:col-span-1">
                    <?php if ($usuario['tipo_usuario'] === 'Socio' && isset($usuario['socio'])): ?>
                    <!-- Beneficios del Socio -->
                    <div class="bg-white rounded-lg shadow p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-gift text-green-600"></i>
                            Beneficios de Membresía
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-sm text-gray-600">Descuento Dulcería</span>
                                <span class="text-lg font-bold text-green-600">
                                    <?php echo number_format($usuario['socio']['desc_dulces'], 0); ?>%
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-sm text-gray-600">Descuento Boletos</span>
                                <span class="text-lg font-bold text-blue-600">
                                    <?php echo number_format($usuario['socio']['desc_boleto'], 0); ?>%
                                </span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-sm text-gray-600">Puntos por Sol</span>
                                <span class="text-lg font-bold text-purple-600">
                                    <?php echo number_format($usuario['socio']['puntos_por_sol'], 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Acciones -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-cogs text-gray-600"></i>
                            Acciones
                        </h3>
                        
                        <div class="space-y-3">
                            <button onclick="toggleEstadoUsuario(<?php echo $usuario['id_usuario']; ?>, <?php echo $usuario['estado']; ?>)"
                                    class="w-full px-4 py-2 <?php echo $usuario['estado'] == 1 ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-green-100 text-green-700 hover:bg-green-200'; ?> rounded-lg transition">
                                <i class="fas fa-toggle-<?php echo $usuario['estado'] == 1 ? 'off' : 'on'; ?> mr-2"></i>
                                <?php echo $usuario['estado'] == 1 ? 'Desactivar' : 'Activar'; ?> Usuario
                            </button>
                            
                            <?php if ($usuario['total_compras'] == 0): ?>
                            <button onclick="confirmDeleteUsuario(<?php echo $usuario['id_usuario']; ?>)"
                                    class="w-full px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition">
                                <i class="fas fa-trash mr-2"></i>
                                Eliminar Usuario
                            </button>
                            <?php else: ?>
                            <div class="p-3 bg-gray-100 rounded-lg text-sm text-gray-600">
                                <i class="fas fa-info-circle mr-2"></i>
                                Este usuario tiene compras registradas y no puede ser eliminado.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

<script>
const API_URL = '../../api/usuario_admin_api.php';

async function toggleEstadoUsuario(id, estadoActual) {
    const nuevoEstado = estadoActual == 1 ? 0 : 1;
    const accion = nuevoEstado == 1 ? 'activar' : 'desactivar';
    
    if (!confirm(`¿Estás seguro de ${accion} este usuario?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ estado: nuevoEstado })
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

async function confirmDeleteUsuario(id) {
    if (!confirm('¿Estás seguro de eliminar este usuario?\n\nEsta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const response = await fetch(`${API_URL}?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
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
