<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Cineplanet'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        cineplanet: {
                            blue: '#00539f',
                            hover: '#004280',
                            yellow: '#ffc600',
                            dark: '#0f172a', // Slate 900
                        }
                    },
                    boxShadow: {
                        'soft': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)',
                    }
                },
            },
        };
    </script>
</head>
<body class="bg-slate-50 text-slate-600 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            <?php include __DIR__ . '/partials/header.php'; ?>
            
            <main class="w-full flex-grow p-8">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">Reportes y Analíticas</h2>
                        <p class="text-sm text-slate-500 mt-1">Métricas clave de rendimiento y asistencia.</p>
                    </div>
                    <div class="flex gap-3">
                        <form action="" method="GET" class="flex gap-2">
                            <div class="relative">
                                <input type="text" name="search" placeholder="Buscar empleado..." value="<?php echo htmlspecialchars($search ?? ''); ?>" class="block w-full rounded-lg border-slate-200 bg-white pl-4 pr-10 focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm py-2.5 shadow-sm">
                                <button type="submit" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-cineplanet-blue">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </button>
                            </div>
                        </form>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="inline-flex items-center justify-center rounded-lg bg-cineplanet-yellow px-4 py-2.5 text-sm font-medium text-slate-900 shadow-soft hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar
                        </a>
                    </div>
                </div>

                <!-- KPIs -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <p class="text-sm font-medium text-slate-500 mb-1">Tasa de Ausentismo</p>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-rose-500"><?php echo htmlspecialchars($kpis['ausentismo'] ?? 'N/A'); ?></span>
                            <span class="ml-2 text-sm font-medium text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full">+2.5%</span>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <p class="text-sm font-medium text-slate-500 mb-1">Rotación de Personal</p>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-amber-500"><?php echo htmlspecialchars($kpis['rotacion'] ?? 'N/A'); ?></span>
                            <span class="ml-2 text-sm font-medium text-slate-500">Mensual</span>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <p class="text-sm font-medium text-slate-500 mb-1">Horas Extras Totales</p>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-blue-500"><?php echo htmlspecialchars($kpis['horas_totales'] ?? 0); ?></span>
                            <span class="ml-1 text-lg text-slate-400">hrs</span>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <p class="text-sm font-medium text-slate-500 mb-1">Satisfacción</p>
                        <div class="flex items-baseline">
                            <span class="text-3xl font-bold text-emerald-500"><?php echo htmlspecialchars($kpis['satisfaccion'] ?? 'N/A'); ?></span>
                            <span class="ml-2 text-sm font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Alta</span>
                        </div>
                    </div>
                </div>

                <!-- Worker Stats Table -->
                <div class="rounded-xl bg-white shadow-soft border border-slate-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="font-bold text-lg text-slate-800">Detalle por Empleado</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                                <tr>
                                    <th class="px-6 py-4">Empleado</th>
                                    <th class="px-6 py-4">DNI</th>
                                    <th class="px-6 py-4">Sede</th>
                                    <th class="px-6 py-4">Horas Trabajadas</th>
                                    <th class="px-6 py-4">% Inasistencia</th>
                                    <th class="px-6 py-4">Tardanzas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php if (isset($workers) && count($workers) > 0): ?>
                                    <?php foreach ($workers as $stat): ?>
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="px-6 py-4 font-medium text-slate-900">
                                                <div class="flex items-center">
                                                    <div class="h-8 w-8 rounded-full bg-slate-200 mr-3 flex items-center justify-center text-xs font-bold text-slate-500">
                                                        <?php echo substr($stat['nombre'], 0, 1) . substr($stat['apellido'], 0, 1); ?>
                                                    </div>
                                                    <?php echo htmlspecialchars($stat['nombre'] . ' ' . $stat['apellido']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-mono text-xs"><?php echo htmlspecialchars($stat['dni']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($stat['sede']); ?></td>
                                            <td class="px-6 py-4 font-semibold text-slate-700"><?php echo htmlspecialchars($stat['horas_trabajadas']); ?> hrs</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <span class="text-slate-700 mr-2"><?php echo htmlspecialchars($stat['inasistencia']); ?>%</span>
                                                    <div class="w-16 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                        <div class="h-full bg-rose-500 rounded-full" style="width: <?php echo $stat['inasistencia']; ?>%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <?php if ($stat['tardanzas'] > 0): ?>
                                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                        <?php echo $stat['tardanzas']; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-slate-400">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                            No se encontraron datos para mostrar.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
