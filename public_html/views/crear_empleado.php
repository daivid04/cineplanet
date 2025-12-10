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
        'id_sede' => $_POST['id_sede'] ?? '',
        'fecha_ingreso' => $_POST['fecha_ingreso'] ?? '',
        // Datos del turno opcional
        'fecha_turno' => $_POST['fecha_turno'] ?? '',
        'tipo_turno' => $_POST['tipo_turno'] ?? '',
        'hora_inicio' => $_POST['hora_inicio'] ?? '',
        'hora_fin' => $_POST['hora_fin'] ?? ''
    ];

    $result = $controller->create($data);

    if ($result['success']) {
        $message = "Empleado creado exitosamente. <a href='/cineplanet/public_html/dashboard.php' class='underline'>Volver al dashboard</a>";
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
            <a href="/cineplanet/public_html/dashboard.php" class="text-gray-300 hover:text-white">
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

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="dni" class="block text-sm font-medium text-gray-700">DNI</label>
                        <input type="text" name="dni" id="dni" required pattern="\d{8}" title="Debe tener 8 dígitos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                    </div>
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700">Cargo</label>
                        <select name="tipo" id="tipo" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                            <option value="">Seleccione un cargo</option>
                            <option value="Staff de Ventas">Staff de Ventas</option>
                            <option value="Supervisora de Turno">Supervisora de Turno</option>
                            <option value="Atención al Cliente">Atención al Cliente</option>
                            <option value="Staff de Dulcería">Staff de Dulcería</option>
                            <option value="Gerente">Gerente</option>
                            <option value="Boletería">Boletería</option>
                            <option value="Dulcería">Dulcería</option>
                            <option value="Limpieza">Limpieza</option>
                            <option value="Proyeccionista">Proyeccionista</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
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
                    <div>
                        <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700">Fecha de Ingreso</label>
                        <input type="date" name="fecha_ingreso" id="fecha_ingreso" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>

                <!-- Sección de Asignación de Turno Inicial -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Asignar Turno Inicial (Opcional)</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label for="fecha_turno" class="block text-sm font-medium text-gray-700">Fecha del Turno</label>
                            <input type="date" name="fecha_turno" id="fecha_turno" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="tipo_turno" class="block text-sm font-medium text-gray-700">Tipo de Turno</label>
                            <select name="tipo_turno" id="tipo_turno" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                                <option value="">Seleccione tipo</option>
                                <option value="Apertura">Apertura</option>
                                <option value="Intermedio">Intermedio</option>
                                <option value="Cierre">Cierre</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="hora_inicio" class="block text-sm font-medium text-gray-700">Hora Inicio</label>
                            <input type="time" name="hora_inicio" id="hora_inicio" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                        </div>
                        <div>
                            <label for="hora_fin" class="block text-sm font-medium text-gray-700">Hora Fin</label>
                            <input type="time" name="hora_fin" id="hora_fin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm border p-2">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <a href="/cineplanet/public_html/dashboard.php" class="mr-4 rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2">Cancelar</a>
                    <button type="submit" class="rounded-md border border-transparent bg-cineplanet-blue py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2">Guardar Empleado</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
