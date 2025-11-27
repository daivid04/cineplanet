<?php
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/EmpleadoControllerV2.php';

$controller = new EmpleadoController($conn);
$sedesResult = $controller->getSedes();
$sedes = $sedesResult['success'] ? $sedesResult['data'] : [];

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nombre' => $_POST['nombre'] ?? '',
        'apellido' => $_POST['apellido'] ?? '',
        'correo' => $_POST['correo'] ?? '',
        'numero' => $_POST['numero'] ?? '',
        'dni' => $_POST['dni'] ?? '',
        'tipo' => $_POST['tipo'] ?? '',
        'id_sede' => $_POST['id_sede'] ?? ''
    ];

    $result = $controller->create($data);

    if ($result['success']) {
        $message = "Empleado creado exitosamente. <a href='../dashboard_tester.php' class='underline'>Volver al dashboard</a>";
    } else {
        $error = "Error: " . $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title>Agregar Empleado - Cineplanet</title>
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
            <h1 class="text-2xl font-bold">Agregar Nuevo Empleado</h1>
            <a href="../dashboard_tester.php" class="text-gray-300 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        </div>
        
        <div class="p-8">
            <?php if ($message): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $message; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                        <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700">Apellido</label>
                        <input type="text" name="apellido" id="apellido" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="correo" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" name="numero" id="numero" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                    </div>
                </div>


                <div>
                    <label for="id_sede" class="block text-sm font-medium text-gray-700">Sede</label>
                    <select name="id_sede" id="id_sede" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                        <option value="">Seleccione una sede</option>
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?php echo $sede['id_sede']; ?>">
                                <?php echo htmlspecialchars($sede['nombre'] . ' - ' . $sede['ciudad_nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex justify-end pt-4">
                    <a href="../dashboard_tester.php" class="mr-4 rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2">Cancelar</a>
                    <button type="submit" class="rounded-md border border-transparent bg-cineplanet-blue py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2">Guardar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
