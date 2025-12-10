<?php
/**
 * API REST para gestión de Socios (Admin)
 * Endpoints: GET, POST, PUT, PATCH, DELETE
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . "/../../../src/controllers/SocioAdminController.php";

$controller = new SocioAdminController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGet($controller);
            break;
        case 'POST':
            handlePost($controller);
            break;
        case 'PUT':
            handlePut($controller);
            break;
        case 'PATCH':
            handlePatch($controller);
            break;
        case 'DELETE':
            handleDelete($controller);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()]);
}

// =====================================================
// GET - Obtener socios
// =====================================================
function handleGet($controller) {
    // Si se pide tipos de socio
    if (isset($_GET['tipos_socio'])) {
        $soloActivos = isset($_GET['activos']) && $_GET['activos'] === '1';
        $tipos = $controller->getTiposSocio($soloActivos);
        echo json_encode(['success' => true, 'data' => $tipos]);
        return;
    }

    // Si se piden estadísticas
    if (isset($_GET['estadisticas'])) {
        $stats = $controller->getEstadisticas();
        echo json_encode(['success' => true, 'data' => $stats]);
        return;
    }

    // Si se pide info de compras
    if (isset($_GET['compras']) && isset($_GET['id'])) {
        $info = $controller->getComprasInfo($_GET['id']);
        echo json_encode(['success' => true, 'data' => $info]);
        return;
    }

    // Obtener por ID
    if (isset($_GET['id'])) {
        $socio = $controller->getById($_GET['id']);
        if ($socio) {
            // Agregar info de compras
            $socio['compras_count'] = $controller->countCompras($_GET['id']);
            echo json_encode(['success' => true, 'data' => $socio]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Socio no encontrado']);
        }
        return;
    }

    // Obtener todos con filtros
    $soloActivos = isset($_GET['activos']) && $_GET['activos'] === '1';
    $filtroTipo = isset($_GET['tipo']) ? $_GET['tipo'] : null;
    $busqueda = isset($_GET['busqueda']) ? $_GET['busqueda'] : null;
    
    $socios = $controller->getAll($soloActivos, $filtroTipo, $busqueda);
    echo json_encode(['success' => true, 'data' => $socios, 'total' => count($socios)]);
}

// =====================================================
// POST - Crear socio
// =====================================================
function handlePost($controller) {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        return;
    }

    $resultado = $controller->create($data);
    
    if ($resultado['success']) {
        http_response_code(201);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($resultado);
}

// =====================================================
// PUT - Actualizar socio completo
// =====================================================
function handlePut($controller) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        return;
    }

    $resultado = $controller->update($_GET['id'], $data);
    
    if ($resultado['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($resultado);
}

// =====================================================
// PATCH - Operaciones parciales (toggle estado, reset password)
// =====================================================
function handlePatch($controller) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        return;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $action = isset($_GET['action']) ? $_GET['action'] : (isset($data['action']) ? $data['action'] : 'toggle');

    switch ($action) {
        case 'toggle':
        case 'toggle_estado':
            $resultado = $controller->toggleEstado($_GET['id']);
            break;

        case 'reset_password':
            if (!isset($data['contrasena'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nueva contraseña requerida']);
                return;
            }
            $resultado = $controller->resetPassword($_GET['id'], $data['contrasena']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            return;
    }

    if ($resultado['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($resultado);
}

// =====================================================
// DELETE - Eliminar socio
// =====================================================
function handleDelete($controller) {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'ID requerido']);
        return;
    }

    $resultado = $controller->delete($_GET['id']);
    
    if ($resultado['success']) {
        http_response_code(200);
    } else {
        http_response_code(400);
    }
    
    echo json_encode($resultado);
}
