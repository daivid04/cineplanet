<?php
/**
 * API REST para Combos
 * 
 * Endpoints:
 * GET    /api/combo_api.php          - Listar todos los combos
 * GET    /api/combo_api.php?id=1     - Obtener combo por ID
 * POST   /api/combo_api.php          - Crear nuevo combo
 * PUT    /api/combo_api.php          - Actualizar combo
 * PATCH  /api/combo_api.php          - Cambiar estado (toggle)
 * DELETE /api/combo_api.php?id=1     - Eliminar combo (soft delete)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/ComboController.php';

$controller = new ComboController($conn);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $response = $controller->getById(intval($_GET['id']));
            } else {
                $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                $response = $controller->getAll($soloActivos);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre']) || trim($data['nombre']) === '') {
                $response = ['success' => false, 'message' => 'El nombre del combo es requerido'];
            } else if (!isset($data['precio']) || !is_numeric($data['precio'])) {
                $response = ['success' => false, 'message' => 'El precio es requerido'];
            } else {
                $response = $controller->create($data);
            }
            break;

        case 'PUT':
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
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id']) || !isset($data['estado'])) {
                $response = ['success' => false, 'message' => 'ID y estado son requeridos'];
            } else {
                $response = $controller->toggleEstado(intval($data['id']), intval($data['estado']));
            }
            break;

        case 'DELETE':
            if (!isset($_GET['id'])) {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
            } else {
                $response = $controller->delete(intval($_GET['id']));
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
