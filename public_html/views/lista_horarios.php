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
                        <h2 class="text-2xl font-bold text-slate-800">Horarios Semanales</h2>
                        <p class="text-sm text-slate-500 mt-1"><?php echo htmlspecialchars($weekRange); ?></p>
                    </div>
                    <div class="flex gap-3">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['action' => 'export'])); ?>" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Exportar CSV
                        </a>
                        <a href="/cineplanet/public_html/asignar_turno.php" class="inline-flex items-center justify-center rounded-lg bg-cineplanet-blue px-4 py-2.5 text-sm font-medium text-white shadow-soft hover:bg-cineplanet-hover focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Asignar Turno
                        </a>
                    </div>
                </div>

                <div class="mb-8 rounded-xl bg-white p-5 shadow-soft border border-slate-100">
                    <form action="" method="GET" class="flex gap-4">
                        <div class="relative flex-grow">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" placeholder="Buscar por nombre o apellido..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="block w-full rounded-lg border-slate-200 bg-slate-50 pl-10 focus:border-cineplanet-blue focus:ring-cineplanet-blue sm:text-sm py-2.5">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-cineplanet-blue px-6 py-2.5 text-sm font-medium text-white shadow-sm hover:bg-cineplanet-hover focus:outline-none focus:ring-2 focus:ring-cineplanet-blue focus:ring-offset-2 transition-colors">
                            Buscar
                        </button>
                    </form>
                </div>

                <div class="rounded-xl bg-white shadow-soft overflow-hidden border border-slate-100">
                    <!-- Header de la tabla (Días dinámicos) -->
                    <div class="grid grid-cols-8 text-center font-semibold text-slate-600 bg-slate-50 border-b border-slate-200">
                        <div class="py-4 border-r border-slate-200 px-2 bg-slate-100/50">Empleado</div>
                        <?php foreach ($dates as $date): ?>
                            <div class="py-4 border-r border-slate-200 px-2 last:border-r-0">
                                <div class="text-xs uppercase text-slate-500 mb-1"><?php echo $date['day_name']; ?></div>
                                <div class="text-lg font-bold text-slate-800"><?php echo $date['day_num']; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Cuerpo de la tabla -->
                    <div class="grid grid-cols-8 text-sm">
                        <?php if (empty($data)): ?>
                            <div class="col-span-8 p-12 text-center text-slate-500">
                                <p class="text-lg font-medium text-slate-900">No hay horarios disponibles</p>
                                <p class="text-sm mt-1">Intenta asignar turnos o cambiar los filtros.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($data as $workerId => $workerData): ?>
                                <!-- Fila del Empleado -->
                                <div class="p-4 border-b border-r border-slate-100 font-medium text-slate-800 flex items-center bg-white hover:bg-slate-50 transition-colors">
                                    <div class="h-10 w-10 rounded-full bg-slate-200 mr-3 flex-shrink-0 overflow-hidden border-2 border-white shadow-sm">
                                        <img src="https://i.pravatar.cc/150?u=<?php echo $workerId; ?>" alt="Avatar" class="h-full w-full object-cover">
                                    </div>
                                    <span class="truncate text-sm font-semibold"><?php echo htmlspecialchars($workerData['info']['nombre'] . ' ' . $workerData['info']['apellido']); ?></span>
                                </div>

                                <!-- Celdas de Días -->
                                <?php foreach ($dates as $date): ?>
                                    <div class="p-2 border-b border-r border-slate-100 flex flex-col justify-center items-center min-h-[80px] hover:bg-slate-50 transition-colors last:border-r-0">
                                        <?php 
                                        $dateKey = $date['full'];
                                        if (isset($workerData['dias'][$dateKey]) && !empty($workerData['dias'][$dateKey])): 
                                            foreach ($workerData['dias'][$dateKey] as $shift):
                                        ?>
                                            <div class="w-full rounded-lg border px-2 py-1.5 text-center text-xs shadow-sm mb-1 last:mb-0 <?php echo $shift['color_class']; ?>">
                                                <div class="font-bold mb-0.5"><?php echo htmlspecialchars($shift['tipo']); ?></div>
                                                <div class="text-[10px] opacity-90">
                                                    <?php echo $shift['entrada'] . ' - ' . $shift['salida']; ?>
                                                </div>
                                            </div>
                                        <?php 
                                            endforeach;
                                        else: 
                                        ?>
                                            <span class="text-slate-300 text-xs italic">Sin asignar</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
