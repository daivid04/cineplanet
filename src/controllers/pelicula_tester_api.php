<?php
/**
 * API para probar el controlador de Películas
 * 
 * Permite ejecutar operaciones CRUD sobre películas
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir archivos necesarios
require_once __DIR__ . "/../services/conexion.php";
require_once __DIR__ . "/pelicula_controller.php";

// Configurar salida para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Obtener la acción a realizar
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if (empty($action)) {
        throw new Exception("Debe especificar una acción");
    }
    
    // Instanciar el controlador
    $controller = new PeliculaController($conn);
    
    // Obtener datos del body
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Procesar según la acción
    switch ($action) {
        
        case 'getAll':
            $result = $controller->getAll();
            break;
        
        case 'getById':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            $result = $controller->getById($input['id']);
            break;
        
        case 'getActivas':
            $result = $controller->getActivas();
            break;
        
        case 'searchByName':
            if (!isset($input['name'])) {
                throw new Exception("El campo 'name' es requerido");
            }
            $result = $controller->searchByName($input['name']);
            break;
        
        case 'create':
            if (!isset($input['data']) || empty($input['data'])) {
                throw new Exception("Debe proporcionar datos para crear");
            }
            $result = $controller->create($input['data']);
            break;
        
        case 'update':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            if (!isset($input['data']) || empty($input['data'])) {
                throw new Exception("Debe proporcionar datos para actualizar");
            }
            $result = $controller->update($input['id'], $input['data']);
            break;
        
        case 'delete':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            $result = $controller->delete($input['id']);
            break;
        
        case 'cambiarEstado':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            if (!isset($input['estado'])) {
                throw new Exception("El campo 'estado' es requerido");
            }
            $result = $controller->cambiarEstado($input['id'], $input['estado']);
            break;
        
        case 'getMethods':
            // Obtener todos los métodos públicos del controlador
            $reflection = new ReflectionClass($controller);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            $methodList = [];
            foreach ($methods as $method) {
                if ($method->class === 'PeliculaController') {
                    $methodList[] = [
                        'name' => $method->getName(),
                        'parameters' => array_map(function($param) {
                            return [
                                'name' => $param->getName(),
                                'optional' => $param->isOptional()
                            ];
                        }, $method->getParameters())
                    ];
                }
            }
            
            $result = [
                'success' => true,
                'controller' => 'PeliculaController',
                'methods' => $methodList
            ];
            break;
        
        default:
            throw new Exception("Acción no válida: " . $action);
    }
    
    // Devolver resultado
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
