<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Cineplanet'; ?></title>
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
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #e5e7eb; }
        .mockup-container { width: 1024px; height: 768px; box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.25); overflow: hidden; }
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
                <header class="flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4">
                    <h1 class="text-2xl font-bold text-cineplanet-dark">Reportes y Analíticas</h1>
                    <div class="flex items-center gap-4">
                        <form action="" method="GET" class="flex gap-2">
                            <input type="text" name="search" placeholder="Buscar empleado..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="rounded-lg border-gray-300 shadow-sm focus:border-cineplanet-blue focus:ring-cineplanet-blue px-4 py-2">
                            <button type="submit" class="rounded-lg bg-cineplanet-blue px-4 py-2 text-white hover:bg-blue-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </form>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="rounded-lg bg-cineplanet-yellow px-4 py-2 text-sm font-semibold text-cineplanet-dark hover:bg-yellow-400 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar
                        </a>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto p-6">
                    <!-- KPIs -->
                    <div class="grid grid-cols-1 gap-6 mb-8">
                        <div class="rounded-lg bg-white p-6 shadow">
                             <h3 class="font-bold text-lg text-gray-800 mb-4">Indicadores Clave</h3>
                             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="flex flex-col justify-center items-center p-4 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-600 mb-2">Tasa de Ausentismo</span>
                                    <span class="font-bold text-2xl text-red-500"><?php echo htmlspecialchars($kpis['ausentismo'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex flex-col justify-center items-center p-4 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-600 mb-2">Rotación de Personal</span>
                                    <span class="font-bold text-2xl text-yellow-500"><?php echo htmlspecialchars($kpis['rotacion'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex flex-col justify-center items-center p-4 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-600 mb-2">Horas Extras Totales</span>
                                    <span class="font-bold text-2xl text-blue-500"><?php echo htmlspecialchars($kpis['horas_totales'] ?? 0); ?> hrs</span>
                                </div>
                                <div class="flex flex-col justify-center items-center p-4 bg-gray-50 rounded-lg">
                                    <span class="font-medium text-gray-600 mb-2">Satisfacción</span>
                                    <span class="font-bold text-2xl text-green-500"><?php echo htmlspecialchars($kpis['satisfaccion'] ?? 'N/A'); ?></span>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Worker Stats Table -->
                    <div class="rounded-lg bg-white shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="font-bold text-lg text-gray-800">Detalle por Empleado</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DNI</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horas Trabajadas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Inasistencia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tardanzas</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (!empty($workers)): ?>
                                        <?php foreach ($workers as $worker): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($worker['nombre'] . ' ' . $worker['apellido']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($worker['dni']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($worker['sede']); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                                                    <?php echo htmlspecialchars($worker['horas_trabajadas']); ?> hrs
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <?php 
                                                        $inasistencia = $worker['inasistencia'];
                                                        $colorClass = $inasistencia > 3 ? 'text-red-600' : ($inasistencia > 1 ? 'text-yellow-600' : 'text-green-600');
                                                    ?>
                                                    <span class="<?php echo $colorClass; ?> font-bold"><?php echo htmlspecialchars($inasistencia); ?>%</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($worker['tardanzas'] ?? 0); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron empleados.</td>
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
