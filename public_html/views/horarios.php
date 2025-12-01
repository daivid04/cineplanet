<?php
// Incluir conexión y controlador
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/HorarioController.php';

// Instanciar controlador
$controller = new HorarioController($conn);

// Manejar Exportación
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    $search = $_GET['search'] ?? '';
    $controller->export($search);
}

// Obtener parámetros de búsqueda
$search = $_GET['search'] ?? '';
$result = $controller->index($search);
$data = $result['success'] ? $result['data'] : [];
$dates = $result['success'] ? $result['dates'] : [];
$weekRange = $result['success'] ? $result['week_range'] : '';

// Variables para la vista
$pageTitle = "Planificación de Horarios";
$activePage = "horarios";

// Renderizar vista
include __DIR__ . '/lista_horarios.php';
?>
