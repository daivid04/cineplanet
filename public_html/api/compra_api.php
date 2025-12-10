<?php
/**
 * API de Compra
 * Endpoint para procesar compras usando stored procedures
 * 
 * POST /api/compra_api.php - Realizar compra
 * DELETE /api/compra_api.php?id=X - Cancelar compra
 * GET /api/compra_api.php?id=X - Obtener compra
 * GET /api/compra_api.php?usuario=X - Historial de usuario
 * POST /api/compra_api.php?verificar=1 - Verificar asientos
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/CompraController.php';

$controller = new CompraController($conn);

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Verificar asientos
            if (isset($_GET['verificar'])) {
                $asientos = $data['asientos'] ?? [];
                echo json_encode($controller->verificarAsientos($asientos));
                break;
            }
            
            // Procesar compra
            echo json_encode($controller->procesarCompra($data));
            break;

        case 'GET':
            if (isset($_GET['id'])) {
                echo json_encode($controller->obtenerCompra($_GET['id']));
            } elseif (isset($_GET['usuario'])) {
                echo json_encode($controller->historialUsuario($_GET['usuario']));
            } else {
                echo json_encode(['success' => false, 'message' => 'Parámetro requerido: id o usuario']);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                echo json_encode($controller->cancelarCompra($_GET['id']));
            } else {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $e->getMessage()
    ]);
}
?>
