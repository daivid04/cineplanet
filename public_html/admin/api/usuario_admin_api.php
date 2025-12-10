<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../../src/services/conexion.php';
require_once __DIR__ . '/../../../src/controllers/UsuarioAdminController.php';

$controller = new UsuarioAdminController($conn);
$method = $_SERVER['REQUEST_METHOD'];

ob_clean();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Obtener usuario específico
                $detallado = isset($_GET['detallado']) && $_GET['detallado'] === 'true';
                
                if ($detallado) {
                    $result = $controller->getDetallesCompletos($_GET['id']);
                } else {
                    $result = $controller->getById($_GET['id']);
                }
                
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                }
            } elseif (isset($_GET['search'])) {
                // Buscar usuarios
                $result = $controller->search($_GET['search']);
                echo json_encode(['success' => true, 'data' => $result]);
            } elseif (isset($_GET['count'])) {
                // Contar usuarios
                $soloActivos = isset($_GET['soloActivos']) && $_GET['soloActivos'] === 'true';
                $filtroTipo = $_GET['tipo'] ?? null;
                $count = $controller->count($soloActivos, $filtroTipo);
                echo json_encode(['success' => true, 'count' => $count]);
            } else {
                // Listar todos los usuarios
                $soloActivos = isset($_GET['soloActivos']) && $_GET['soloActivos'] === 'true';
                $filtroTipo = $_GET['tipo'] ?? null;
                $result = $controller->getAll($soloActivos, $filtroTipo);
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $controller->create($data);
            
            if ($result['success']) {
                http_response_code(201);
            } else {
                http_response_code(400);
            }
            echo json_encode($result);
            break;

        case 'PUT':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID es requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $controller->update($_GET['id'], $data);
            
            if (!$result['success']) {
                http_response_code(400);
            }
            echo json_encode($result);
            break;

        case 'PATCH':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID es requerido']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['estado'])) {
                $result = $controller->toggleEstado($_GET['id'], $data['estado']);
            } else {
                $result = ['success' => false, 'message' => 'Acción no válida'];
            }
            
            if (!$result['success']) {
                http_response_code(400);
            }
            echo json_encode($result);
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID es requerido']);
                break;
            }
            
            $result = $controller->delete($_GET['id']);
            
            if (!$result['success']) {
                http_response_code(400);
            }
            echo json_encode($result);
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
