<?php
// Habilitar visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/EmpleadoControllerV2.php';

// Instanciar controlador
try {
    $controller = new EmpleadoController($conn);
    
    // Manejar Exportación
    if (isset($_GET['action']) && $_GET['action'] === 'export') {
        $filters = [
            'sede' => $_GET['sede'] ?? '',
            'cargo' => $_GET['cargo'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        $controller->export($filters);
    }

    // Capturar filtros y paginación
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $filters = [
        'sede' => $_GET['sede'] ?? '',
        'cargo' => $_GET['cargo'] ?? '',
        'estado' => $_GET['estado'] ?? '',
        'search' => $_GET['search'] ?? ''
    ];

    $result = $controller->list($page, $filters);
    
    if ($result['success']) {
        $data = $result['data'];
    } else {
        $data = [
            'empleados' => [], 
            'stats' => [], 
            'total' => 0, 
            'page' => 1, 
            'limit' => 10, 
            'sedes' => [], 
            'cargos' => []
        ];
        // echo "Error: " . $result['message']; // Opcional: mostrar error en vista
    }
} catch (Exception $e) {
    // echo "Error crítico: " . $e->getMessage();
    $data = [
        'empleados' => [], 
        'stats' => [], 
        'total' => 0, 
        'page' => 1, 
        'limit' => 10, 
        'sedes' => [], 
        'cargos' => []
    ];
}

// Variables para la vista
$pageTitle = "Gestión de Empleados";
$activePage = "empleados";

// Renderizar vista
include __DIR__ . '/views/lista_empleados.php';
?>
