<?php
/**
 * API para gestión de Productos en Combos (Admin)
 * Tabla intermedia: producto_combo
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir conexión y controlador
require_once __DIR__ . '/../../../src/services/conexion.php';
require_once __DIR__ . '/../../../src/controllers/producto_combo_admin_controller.php';

$controller = new ProductoComboAdminController();

$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    switch ($method) {
        case 'GET':
            handleGet($controller, $action, $id);
            break;
        case 'POST':
            handlePost($controller, $action);
            break;
        case 'DELETE':
            handleDelete($controller, $id);
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Método no soportado'
            ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}

/**
 * Manejar peticiones GET
 */
function handleGet($controller, $action, $id) {
    switch ($action) {
        case 'getById':
            if ($id > 0) {
                echo json_encode($controller->getById($id));
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID requerido'
                ]);
            }
            break;

        case 'getByCombo':
            $idCombo = isset($_GET['id_combo']) ? intval($_GET['id_combo']) : 0;
            if ($idCombo > 0) {
                echo json_encode($controller->getByCombo($idCombo));
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de combo requerido'
                ]);
            }
            break;

        case 'getCombosConProductos':
            echo json_encode($controller->getCombosConProductos());
            break;

        case 'getProductosDisponibles':
            $idCombo = isset($_GET['id_combo']) ? intval($_GET['id_combo']) : null;
            echo json_encode($controller->getProductosDisponibles($idCombo));
            break;

        case 'getCombos':
            echo json_encode($controller->getCombos());
            break;

        default:
            // Por defecto, obtener todos
            echo json_encode($controller->getAll());
            break;
    }
}

/**
 * Manejar peticiones POST
 */
function handlePost($controller, $action) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        echo json_encode([
            'success' => false,
            'message' => 'Datos JSON inválidos'
        ]);
        return;
    }

    switch ($action) {
        case 'addProducto':
            echo json_encode($controller->addProductoToCombo($data));
            break;

        case 'addMultiples':
            echo json_encode($controller->addMultiplesProductos($data));
            break;

        case 'removeProducto':
            echo json_encode($controller->removeProductoFromCombo($data));
            break;

        case 'updateProductos':
            echo json_encode($controller->updateProductosCombo($data));
            break;

        default:
            // Por defecto, agregar producto
            echo json_encode($controller->addProductoToCombo($data));
            break;
    }
}

/**
 * Manejar peticiones DELETE
 */
function handleDelete($controller, $id) {
    if ($id > 0) {
        echo json_encode($controller->delete($id));
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'ID requerido para eliminar'
        ]);
    }
}
