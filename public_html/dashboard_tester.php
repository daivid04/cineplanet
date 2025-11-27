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
            'stats' => []
        ];
        echo "<div style='background:red;color:white;padding:10px;'>Error: " . $response['message'] . "</div>";
    }
} catch (Exception $e) {
    $data = [
        'empleados' => [],
        'stats' => []
    ];
    echo "<div style='background:red;color:white;padding:10px;'>Excepción: " . $e->getMessage() . "</div>";
}

// Variables para la vista
$pageTitle = "Dashboard de Control";
$activePage = "dashboard";

// Renderizar vista
include __DIR__ . '/views/dashboard_empleado.php';
?>
