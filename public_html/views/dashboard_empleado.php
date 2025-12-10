<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Cineplanet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            <!-- Content Area -->
            <main class="w-full flex-grow p-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-slate-800">Resumen General</h2>
                    <p class="text-sm text-slate-500 mt-1">Vista general del estado de tu sede.</p>
                </div>

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100 flex items-center justify-between hover:shadow-md transition-shadow">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Total Empleados</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo $data['stats']['total_empleados'] ?? 0; ?></p>
                        </div>
                        <div class="p-3 rounded-lg bg-blue-50 text-cineplanet-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100 flex items-center justify-between hover:shadow-md transition-shadow">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Nuevas Contrataciones</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo $data['stats']['nuevas_contrataciones'] ?? 0; ?></p>
                        </div>
                        <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100 flex items-center justify-between hover:shadow-md transition-shadow">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Turnos Activos Hoy</p>
                            <p class="mt-2 text-3xl font-bold text-slate-900"><?php echo $data['stats']['turnos_activos'] ?? 0; ?></p>
                        </div>
                        <div class="p-3 rounded-lg bg-amber-50 text-amber-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100 flex items-center justify-between hover:shadow-md transition-shadow">
                        <div>
                            <p class="text-sm font-medium text-slate-500">Ausencias</p>
                            <p class="mt-2 text-3xl font-bold text-rose-500"><?php echo $data['stats']['ausencias'] ?? 0; ?></p>
                        </div>
                        <div class="p-3 rounded-lg bg-rose-50 text-rose-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-6">Distribución por Sede</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="sedesChart"></canvas>
                        </div>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-soft border border-slate-100">
                        <h3 class="text-lg font-bold text-slate-800 mb-6">Empleados por Cargo</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="cargosChart"></canvas>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Datos para los gráficos
        const sedesData = <?php echo json_encode($data['charts']['sedes']); ?>;
        const cargosData = <?php echo json_encode($data['charts']['cargos']); ?>;

        // Configuración común
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';

        // Gráfico de Sedes
        const ctxSedes = document.getElementById('sedesChart').getContext('2d');
        new Chart(ctxSedes, {
            type: 'doughnut',
            data: {
                labels: sedesData.labels,
                datasets: [{
                    data: sedesData.values,
                    backgroundColor: [
                        '#00539f', '#3b82f6', '#60a5fa', '#93c5fd', '#bfdbfe'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Gráfico de Cargos
        const ctxCargos = document.getElementById('cargosChart').getContext('2d');
        new Chart(ctxCargos, {
            type: 'bar',
            data: {
                labels: cargosData.labels,
                datasets: [{
                    label: 'Empleados',
                    data: cargosData.values,
                    backgroundColor: '#ffc600',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2],
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
