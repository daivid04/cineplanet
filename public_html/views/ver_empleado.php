<?php
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoController.php';

$controller = new EmpleadoController($conn);
$id = $_GET['id'] ?? null;
$empleado = null;
$error = '';

if ($id) {
    $result = $controller->getById($id);
    if ($result['success']) {
        $empleado = $result['data'];
    } else {
        $error = $result['message'];
    }
} else {
    $error = "ID de empleado no especificado.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title>Ver Empleado - Cineplanet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        'cineplanet-blue': '#00539f',
                        'cineplanet-yellow': '#ffc600',
                        'cineplanet-gray': '#f3f4f6',
                        'cineplanet-dark': '#1e293b',
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-cineplanet-dark text-white p-6 flex justify-between items-center">
            <h1 class="text-2xl font-bold">Detalles del Empleado</h1>
            <a href="/cineplanet/public_html/dashboard.php" class="text-gray-300 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
        
        <div class="p-8">
            <?php if ($error): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
                <div class="mt-4">
                    <a href="/cineplanet/public_html/dashboard.php" class="text-cineplanet-blue hover:underline">Volver al dashboard</a>
                </div>
            <?php elseif ($empleado): ?>
                <div class="flex items-center mb-8">
                    <img class="h-24 w-24 rounded-full object-cover mr-6 border-4 border-cineplanet-gray" src="https://i.pravatar.cc/150?u=<?php echo $empleado['id_trabajador']; ?>" alt="Avatar">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?></h2>
                        <p class="text-lg text-cineplanet-blue font-medium"><?php echo htmlspecialchars($empleado['cargo']); ?></p>
                        <div class="mt-2">
                            <?php if ($empleado['estado']): ?>
                                <span class="rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800">Activo</span>
                            <?php else: ?>
                                <span class="rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800">Inactivo</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 border-t border-gray-200 pt-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Correo Electrónico</p>
                        <p class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($empleado['correo']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Teléfono</p>
                        <p class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($empleado['numero'] ?: 'No registrado'); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">DNI</p>
                        <p class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($empleado['dni']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Sede</p>
                        <p class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($empleado['sede_nombre'] . ' - ' . $empleado['ciudad_nombre']); ?></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Fecha de Ingreso</p>
                        <p class="mt-1 text-lg text-gray-900"><?php echo htmlspecialchars($empleado['fecha_ingreso'] ?? 'No registrado'); ?></p>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <a href="/cineplanet/public_html/dashboard.php" class="rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2">Volver</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
