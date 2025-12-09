<?php
/**
 * API REST para Asiento
 * 
 * Endpoints:
 * GET    /api/asiento_api.php                   - Listar todos los asientos
 * GET    /api/asiento_api.php?id=1              - Obtener asiento por ID
 * GET    /api/asiento_api.php?sala=1            - Obtener asientos por sala
 * GET    /api/asiento_api.php?sala=1&mapa=1     - Obtener mapa de asientos de sala
 * POST   /api/asiento_api.php                   - Crear nuevo asiento
 * POST   /api/asiento_api.php (generar)         - Generar asientos automáticamente
 * PUT    /api/asiento_api.php                   - Actualizar asiento
 * PATCH  /api/asiento_api.php                   - Cambiar estado (toggle)
 * DELETE /api/asiento_api.php?id=1              - Eliminar asiento
 * DELETE /api/asiento_api.php?sala=1&all=1      - Eliminar todos los asientos de una sala
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
require_once __DIR__ . '/../../src/controllers/AsientoController.php';

$controller = new AsientoController($conn);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $response = $controller->getById(intval($_GET['id']));
            } else if (isset($_GET['sala'])) {
                $idSala = intval($_GET['sala']);
                
                // Si se solicita el mapa
                if (isset($_GET['mapa'])) {
                    $response = $controller->getMapaBySala($idSala);
                } else {
                    $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                    $response = $controller->getBySala($idSala, $soloActivos);
                }
            } else {
                $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                $response = $controller->getAll($soloActivos);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Generar asientos automáticamente
            if (isset($data['generar']) && $data['generar'] === true) {
                if (!isset($data['id_sala']) || !is_numeric($data['id_sala'])) {
                    $response = ['success' => false, 'message' => 'La sala es requerida'];
                } else if (!isset($data['filas']) || !is_numeric($data['filas'])) {
                    $response = ['success' => false, 'message' => 'El numero de filas es requerido'];
                } else if (!isset($data['columnas']) || !is_numeric($data['columnas'])) {
                    $response = ['success' => false, 'message' => 'El numero de columnas es requerido'];
                } else {
                    $response = $controller->generarAsientos(
                        intval($data['id_sala']),
                        intval($data['filas']),
                        intval($data['columnas'])
                    );
                }
            } 
            // Crear asiento individual
            else {
                if (!$data || !isset($data['fila_asiento']) || trim($data['fila_asiento']) === '') {
                    $response = ['success' => false, 'message' => 'La fila del asiento es requerida'];
                } else if (!isset($data['columna_asiento']) || trim($data['columna_asiento']) === '') {
                    $response = ['success' => false, 'message' => 'La columna del asiento es requerida'];
                } else if (!isset($data['id_sala']) || !is_numeric($data['id_sala'])) {
                    $response = ['success' => false, 'message' => 'La sala es requerida'];
                } else {
                    $response = $controller->create($data);
                }
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
            // Eliminar todos los asientos de una sala
            if (isset($_GET['sala']) && isset($_GET['all'])) {
                $response = $controller->deleteAllBySala(intval($_GET['sala']));
            }
            // Eliminar asiento individual
            else if (isset($_GET['id'])) {
                $response = $controller->delete(intval($_GET['id']));
            } else {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
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
