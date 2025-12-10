<?php
// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/EmpleadoControllerV2.php';

// Instanciar controlador
try {
    $controller = new EmpleadoController($conn);
    $response = $controller->index();

    if ($response['success']) {
        $data = $response['data'];
    } else {
        $data = [
            'empleados' => [],
            'stats' => [
                'total_empleados' => 0,
                'nuevas_contrataciones' => 0,
                'turnos_activos' => 0,
                'ausencias' => 0
            ],
            'charts' => [
                'sedes' => ['labels' => [], 'values' => []],
                'cargos' => ['labels' => [], 'values' => []]
            ]
        ];
        // Error silenciado para producción
    }
} catch (Exception $e) {
    $data = [
        'empleados' => [],
        'stats' => [
            'total_empleados' => 0,
            'nuevas_contrataciones' => 0,
            'turnos_activos' => 0,
            'ausencias' => 0
        ],
        'charts' => [
            'sedes' => ['labels' => [], 'values' => []],
            'cargos' => ['labels' => [], 'values' => []]
        ]
    ];
    // Error silenciado para producción
}

// Variables para la vista
$pageTitle = "Dashboard de Control";
$activePage = "dashboard";

// Renderizar vista
include __DIR__ . '/views/dashboard_empleado.php';
?>
