<?php
/**
 * API Genérica para probar el controlador de Ciudad
 * 
 * Permite especificar la tabla/controlador y ejecutar operaciones CRUD
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Incluir archivos necesarios
require_once __DIR__ . "/../../src/services/conexion.php";

// Configurar salida para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Obtener el controlador a usar (por defecto ciudad)
    $tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'ciudad';
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    if (empty($tabla)) {
        throw new Exception("Debe especificar una tabla");
    }
    
    // Construir el nombre del controlador
    $controllerFile = __DIR__ . "/../../src/controllers/" . $tabla . "_controller.php";
    $controllerClass = ucfirst($tabla) . "Controller";
    
    // Verificar si existe el archivo del controlador
    if (!file_exists($controllerFile)) {
        throw new Exception("No existe el controlador para la tabla: " . $tabla);
    }
    
    // Incluir el controlador
    require_once $controllerFile;
    
    // Verificar si existe la clase
    if (!class_exists($controllerClass)) {
        throw new Exception("No existe la clase: " . $controllerClass);
    }
    
    // Instanciar el controlador
    $controller = new $controllerClass($conn);
    
    // Obtener datos del body
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Procesar según la acción
    switch ($action) {
        
        case 'getAll':
            if (!method_exists($controller, 'getAll')) {
                throw new Exception("El método getAll no existe en " . $controllerClass);
            }
            $result = $controller->getAll();
            break;
        
        case 'getById':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            if (!method_exists($controller, 'getById')) {
                throw new Exception("El método getById no existe en " . $controllerClass);
            }
            $result = $controller->getById($input['id']);
            break;
        
        case 'create':
            if (!isset($input['data']) || empty($input['data'])) {
                throw new Exception("Debe proporcionar datos para crear");
            }
            if (!method_exists($controller, 'create')) {
                throw new Exception("El método create no existe en " . $controllerClass);
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
            if (!method_exists($controller, 'update')) {
                throw new Exception("El método update no existe en " . $controllerClass);
            }
            $result = $controller->update($input['id'], $input['data']);
            break;
        
        case 'delete':
            if (!isset($input['id'])) {
                throw new Exception("El campo 'id' es requerido");
            }
            if (!method_exists($controller, 'delete')) {
                throw new Exception("El método delete no existe en " . $controllerClass);
            }
            $result = $controller->delete($input['id']);
            break;
        
        case 'getMethods':
            // Obtener todos los métodos públicos del controlador
            $reflection = new ReflectionClass($controller);
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            $methodList = [];
            foreach ($methods as $method) {
                if ($method->class === $controllerClass) {
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
                'tabla' => $tabla,
                'controller' => $controllerClass,
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
