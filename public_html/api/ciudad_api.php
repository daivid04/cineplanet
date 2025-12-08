<?php
/**
 * API REST para Ciudad
 * 
 * Endpoints:
 * GET    /api/ciudad_api.php          - Listar todas las ciudades
 * GET    /api/ciudad_api.php?id=1     - Obtener ciudad por ID
 * POST   /api/ciudad_api.php          - Crear nueva ciudad
 * PUT    /api/ciudad_api.php          - Actualizar ciudad
 * PATCH  /api/ciudad_api.php          - Cambiar estado (toggle)
 * DELETE /api/ciudad_api.php?id=1     - Eliminar ciudad (soft delete)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Incluir dependencias
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/CiudadController.php';

// Inicializar controller
$controller = new CiudadController($conn);

// Obtener metodo HTTP
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Obtener por ID o listar todos
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $response = $controller->getById($id);
            } else {
                // Parametro opcional para solo activos
                $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                $response = $controller->getAll($soloActivos);
            }
            break;

        case 'POST':
            // Crear nueva ciudad
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre']) || trim($data['nombre']) === '') {
                $response = ['success' => false, 'message' => 'El nombre es requerido'];
            } else {
                $response = $controller->create($data);
            }
            break;

        case 'PUT':
            // Actualizar ciudad
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id'])) {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
            } else {
                $id = intval($data['id']);
                unset($data['id']);
                $response = $controller->update($id, $data);
            }
            break;

        case 'PATCH':
            // Cambiar estado
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id']) || !isset($data['estado'])) {
                $response = ['success' => false, 'message' => 'ID y estado son requeridos'];
            } else {
                $id = intval($data['id']);
                $estado = intval($data['estado']);
                $response = $controller->toggleEstado($id, $estado);
            }
            break;

        case 'DELETE':
            // Eliminar ciudad
            if (!isset($_GET['id'])) {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
            } else {
                $id = intval($_GET['id']);
                $response = $controller->delete($id);
            }
            break;

        default:
            http_response_code(405);
            $response = ['success' => false, 'message' => 'Metodo no permitido'];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
