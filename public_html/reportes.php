<?php
// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/ReporteController.php';

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
$kpis = $result['success'] ? $result['data']['kpis'] : [];
$workers = $result['success'] ? $result['data']['workers'] : [];

// Variables para la vista
$pageTitle = "Reportes y Analíticas";
$activePage = "reportes";

// Renderizar vista
include __DIR__ . '/views/reportes.php';
?>
