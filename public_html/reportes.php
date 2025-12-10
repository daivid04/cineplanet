<?php
// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/ReporteController.php';

try {
    // Instanciar controlador
    $controller = new ReporteController($conn);

    // Manejar Exportación
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        $search = $_GET['search'] ?? '';
        $controller->export($search);
    }

    $search = $_GET['search'] ?? '';
    $result = $controller->index($search);

    $chartData = $result['success'] ? $result['data']['chart'] : ['labels' => [], 'data' => []];
    $kpis = $result['success'] ? $result['data']['kpis'] : [
        'horas_totales' => 0,
        'ausentismo' => 'N/A',
        'rotacion' => 'N/A',
        'satisfaccion' => 'N/A'
    ];
    $workers = $result['success'] ? $result['data']['workers'] : [];
} catch (Exception $e) {
    $chartData = ['labels' => [], 'data' => []];
    $kpis = [
        'horas_totales' => 0,
        'ausentismo' => 'N/A',
        'rotacion' => 'N/A',
        'satisfaccion' => 'N/A'
    ];
    $workers = [];
}

// Variables para la vista
$pageTitle = "Reportes y Analíticas";
$activePage = "reportes";

// Renderizar vista
include __DIR__ . '/views/reportes.php';
?>
