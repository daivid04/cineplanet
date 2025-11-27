<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title>Dashboard Cineplanet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #e5e7eb;
        }
        .mockup-container {
            width: 1024px;
            height: 768px;
            box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25);
            overflow: hidden;
        }
    </style>
</head>
<body>
    <div class="mockup-container">
        <div class="flex h-full w-full bg-cineplanet-gray">
            <!-- Sidebar -->
            <?php include __DIR__ . '/partials/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex flex-1 flex-col overflow-hidden">
                <!-- Header -->
                <?php include __DIR__ . '/partials/header.php'; ?>

                <!-- Content Area -->
                <main class="flex-1 overflow-y-auto p-6">
                    <!-- Stat Cards -->
                    <div class="grid grid-cols-4 gap-6">
                        <div class="rounded-lg bg-white p-4 shadow">
                            <p class="text-sm font-medium text-gray-500">Total Empleados</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900"><?php echo $data['stats']['total_empleados'] ?? 0; ?></p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                            <p class="text-sm font-medium text-gray-500">Nuevas Contrataciones</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900"><?php echo $data['stats']['nuevas_contrataciones'] ?? 0; ?></p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                           <p class="text-sm font-medium text-gray-500">Turnos Activos Hoy</p>
                           <p class="mt-2 text-3xl font-bold text-gray-900"><?php echo $data['stats']['turnos_activos'] ?? 0; ?></p>
                        </div>
                        <div class="rounded-lg bg-white p-4 shadow">
                           <p class="text-sm font-medium text-gray-500">Ausencias</p>
                           <p class="mt-2 text-3xl font-bold text-red-500"><?php echo $data['stats']['ausencias'] ?? 0; ?></p>
                        </div>
                    </div>

                    <!-- Employees Table -->
                    <div class="mt-8 rounded-lg bg-white p-6 shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Lista de Empleados</h2>
                            <a href="views/crear_empleado.php" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">+ Agregar Empleado</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-500">
                                <thead class="bg-gray-50 text-xs uppercase text-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nombre</th>
                                        <th scope="col" class="px-6 py-3">Cargo</th>
                                        <th scope="col" class="px-6 py-3">Sede</th>
                                        <th scope="col" class="px-6 py-3">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($data['empleados']) && count($data['empleados']) > 0): ?>
                                        <?php foreach ($data['empleados'] as $empleado): ?>
                                            <tr class="border-b bg-white hover:bg-gray-50">
                                                <td class="px-6 py-3 font-medium text-gray-900">
                                                    <div class="flex items-center">
                                                        <img class="h-8 w-8 rounded-full mr-3" src="https://i.pravatar.cc/150?u=<?php echo $empleado['id_trabajador']; ?>" alt="Avatar">
                                                        <?php echo htmlspecialchars($empleado['nombre'] . ' ' . $empleado['apellido']); ?>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($empleado['cargo']); ?></td>
                                                <td class="px-6 py-3"><?php echo htmlspecialchars($empleado['sede_nombre']); ?></td>
                                                <td class="px-6 py-3">
                                                    <?php if ($empleado['estado']): ?>
                                                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">Activo</span>
                                                    <?php else: ?>
                                                        <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800">Inactivo</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-3 text-center">
                                                    <a href="views/ver_empleado.php?id=<?php echo $empleado['id_trabajador']; ?>" class="font-medium text-cineplanet-blue hover:underline">Ver</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="px-6 py-3 text-center">No hay empleados registrados.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</body>
</html>
