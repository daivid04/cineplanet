<?php
/**
 * API REST para Sede
 * 
 * Endpoints:
 * GET    /api/sede_api.php                  - Listar todas las sedes
 * GET    /api/sede_api.php?id=1             - Obtener sede por ID
 * GET    /api/sede_api.php?ciudad=1         - Obtener sedes por ciudad
 * POST   /api/sede_api.php                  - Crear nueva sede
 * PUT    /api/sede_api.php                  - Actualizar sede
 * PATCH  /api/sede_api.php                  - Cambiar estado (toggle)
 * DELETE /api/sede_api.php?id=1             - Eliminar sede (soft delete)
 */

// Limpiar cualquier salida previa
ob_clean();

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
require_once __DIR__ . '/../../src/controllers/SedeController.php';

$controller = new SedeController($conn);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $response = $controller->getById(intval($_GET['id']));
            } else if (isset($_GET['ciudad'])) {
                $response = $controller->getByCiudad(intval($_GET['ciudad']));
            } else {
                $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                $response = $controller->getAll($soloActivos);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre']) || trim($data['nombre']) === '') {
                $response = ['success' => false, 'message' => 'El nombre de la sede es requerido'];
            } else if (!isset($data['id_ciudad']) || !is_numeric($data['id_ciudad'])) {
                $response = ['success' => false, 'message' => 'La ciudad es requerida'];
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
