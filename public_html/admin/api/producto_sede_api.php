<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../../src/services/conexion.php';
require_once __DIR__ . '/../../../src/controllers/ProductoSedeController.php';

$controller = new ProductoSedeController($conn);
$method = $_SERVER['REQUEST_METHOD'];

ob_clean();

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Obtener por ID específico
                $result = $controller->getById($_GET['id']);
                if ($result) {
                    echo json_encode(['success' => true, 'data' => $result]);
                } else {
                    http_response_code(404);
                    echo json_encode(['success' => false, 'message' => 'Registro no encontrado']);
                }
            } elseif (isset($_GET['estadisticas']) && isset($_GET['sede'])) {
                // Estadísticas por sede
                $result = $controller->getEstadisticasBySede($_GET['sede']);
                echo json_encode(['success' => true, 'data' => $result]);
            } elseif (isset($_GET['sin_stock'])) {
                // Productos sin stock
                $idSede = $_GET['sede'] ?? null;
                $result = $controller->getProductosSinStock($idSede);
                echo json_encode(['success' => true, 'data' => $result]);
            } elseif (isset($_GET['bajo_stock'])) {
                // Productos bajo stock
                $umbral = $_GET['umbral'] ?? 10;
                $idSede = $_GET['sede'] ?? null;
                $result = $controller->getProductosBajoStock($umbral, $idSede);
                echo json_encode(['success' => true, 'data' => $result]);
            } elseif (isset($_GET['count'])) {
                // Contar
                $idSede = $_GET['sede'] ?? null;
                $count = $controller->count($idSede);
                echo json_encode(['success' => true, 'count' => $count]);
            } elseif (isset($_GET['producto'])) {
                // Obtener sedes donde está un producto
                $result = $controller->getByProducto($_GET['producto']);
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                // Listar todos con filtros
                $idSede = $_GET['sede'] ?? null;
                $idProducto = $_GET['producto_filter'] ?? null;
                $soloConStock = isset($_GET['con_stock']) && $_GET['con_stock'] === 'true';
                $result = $controller->getAll($idSede, $idProducto, $soloConStock);
                echo json_encode(['success' => true, 'data' => $result]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Verificar si es asignación masiva
            if (isset($data['asignacion_masiva']) && isset($data['sedes'])) {
                $result = $controller->asignarAMultiplesSedes(
                    $data['id_producto'],
                    $data['sedes'],
                    $data['stock'] ?? 0
                );
            } else {
                $result = $controller->create($data);
            }
            
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
            
            if (isset($data['accion'])) {
                switch ($data['accion']) {
                    case 'incrementar':
                        $result = $controller->incrementarStock($_GET['id'], $data['cantidad'] ?? 1);
                        break;
                    case 'decrementar':
                        $result = $controller->decrementarStock($_GET['id'], $data['cantidad'] ?? 1);
                        break;
                    case 'set_stock':
                        $result = $controller->updateStock($_GET['id'], $data['stock']);
                        break;
                    default:
                        $result = ['success' => false, 'message' => 'Acción no válida'];
                }
            } elseif (isset($data['stock'])) {
                $result = $controller->updateStock($_GET['id'], $data['stock']);
            } else {
                $result = ['success' => false, 'message' => 'Datos incompletos'];
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
