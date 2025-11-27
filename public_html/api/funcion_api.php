<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Encabezados CORS y configuración
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

// Manejar solicitudes OPTIONS (preflight)
if ($_SERVER["REQUEST_METHOD"] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir archivos necesarios
require_once __DIR__ . "/../../src/services/conexion.php";
require_once __DIR__ . "/../../src/controllers/funcion_controller.php";

// Inicializar el controlador
$funcionController = new FuncionController($conn);

// Obtener el método HTTP
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener el input JSON
$input = json_decode(file_get_contents("php://input"), true);

// Obtener parámetros de la URL
$id = isset($_GET['id']) ? $_GET['id'] : null;
$accion = isset($_GET['accion']) ? $_GET['accion'] : null;

$response = ["success" => false, "message" => "Error desconocido"];

try {
    switch ($metodo) {
        
        // ========== GET - OBTENER FUNCIONES ==========
        case 'GET':
            
            // Obtener función por ID
            if ($id) {
                $response = $funcionController->getById($id);
            }
            // Obtener funciones con diferentes filtros
            else if ($accion) {
                switch ($accion) {
                    case 'por_pelicula':
                        if (!isset($_GET['id_pelicula'])) {
                            throw new Exception("Se requiere 'id_pelicula'");
                        }
                        $response = $funcionController->getByPelicula($_GET['id_pelicula']);
                        break;

                    case 'por_sede':
                        if (!isset($_GET['id_sede'])) {
                            throw new Exception("Se requiere 'id_sede'");
                        }
                        $response = $funcionController->getBySede($_GET['id_sede']);
                        break;

                    case 'por_fecha':
                        if (!isset($_GET['fecha'])) {
                            throw new Exception("Se requiere 'fecha' (formato YYYY-MM-DD)");
                        }
                        $response = $funcionController->getByFecha($_GET['fecha']);
                        break;

                    case 'con_filtros':
                        $filters = [];
                        if (isset($_GET['id_pelicula'])) $filters['id_pelicula'] = $_GET['id_pelicula'];
                        if (isset($_GET['id_sede'])) $filters['id_sede'] = $_GET['id_sede'];
                        if (isset($_GET['id_ciudad'])) $filters['id_ciudad'] = $_GET['id_ciudad'];
                        if (isset($_GET['fecha'])) $filters['fecha'] = $_GET['fecha'];
                        if (isset($_GET['fecha_desde'])) $filters['fecha_desde'] = $_GET['fecha_desde'];
                        if (isset($_GET['fecha_hasta'])) $filters['fecha_hasta'] = $_GET['fecha_hasta'];
                        
                        $response = $funcionController->getByFilters($filters);
                        break;

                    case 'agrupadas_por_pelicula':
                        $filters = [];
                        if (isset($_GET['id_ciudad'])) $filters['id_ciudad'] = $_GET['id_ciudad'];
                        if (isset($_GET['fecha'])) $filters['fecha'] = $_GET['fecha'];
                        if (isset($_GET['fecha_desde'])) $filters['fecha_desde'] = $_GET['fecha_desde'];
                        if (isset($_GET['fecha_hasta'])) $filters['fecha_hasta'] = $_GET['fecha_hasta'];
                        
                        $response = $funcionController->getFuncionesAgrupadasPorPelicula($filters);
                        break;

                    case 'horarios_disponibles':
                        if (!isset($_GET['id_pelicula'])) {
                            throw new Exception("Se requiere 'id_pelicula'");
                        }
                        $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : null;
                        $id_ciudad = isset($_GET['id_ciudad']) ? $_GET['id_ciudad'] : null;
                        
                        $response = $funcionController->getHorariosDisponibles(
                            $_GET['id_pelicula'],
                            $fecha,
                            $id_ciudad
                        );
                        break;

                    case 'verificar_disponibilidad':
                        if (!isset($_GET['id_sala']) || !isset($_GET['fecha']) || 
                            !isset($_GET['hora']) || !isset($_GET['duracion'])) {
                            throw new Exception("Se requieren: id_sala, fecha, hora, duracion");
                        }
                        
                        $id_funcion_excluir = isset($_GET['id_funcion_excluir']) ? $_GET['id_funcion_excluir'] : null;
                        
                        $response = $funcionController->verificarDisponibilidadSala(
                            $_GET['id_sala'],
                            $_GET['fecha'],
                            $_GET['hora'],
                            $_GET['duracion'],
                            $id_funcion_excluir
                        );
                        break;

                    default:
                        throw new Exception("Acción no válida");
                }
            }
            // Obtener todas las funciones
            else {
                $response = $funcionController->getAll();
            }
            break;

        // ========== POST - CREAR FUNCIÓN ==========
        case 'POST':
            if (!$input || !isset($input['fecha']) || !isset($input['hora']) || 
                !isset($input['id_pelicula']) || !isset($input['id_sala'])) {
                throw new Exception("Se requieren: fecha, hora, id_pelicula, id_sala");
            }

            $response = $funcionController->create($input);
            
            if ($response['success']) {
                http_response_code(201); // Created
            } else {
                http_response_code(400); // Bad Request
            }
            break;

        // ========== PUT - ACTUALIZAR FUNCIÓN ==========
        case 'PUT':
            if (!$id) {
                throw new Exception("Se requiere el ID de la función en la URL");
            }

            if (!$input || empty($input)) {
                throw new Exception("Se requieren datos para actualizar");
            }

            $response = $funcionController->update($id, $input);
            
            if (!$response['success']) {
                http_response_code(400); // Bad Request
            }
            break;

        // ========== DELETE - ELIMINAR FUNCIÓN ==========
        case 'DELETE':
            if (!$id) {
                throw new Exception("Se requiere el ID de la función en la URL");
            }

            $response = $funcionController->delete($id);
            
            if (!$response['success']) {
                http_response_code(404); // Not Found
            }
            break;

        default:
            http_response_code(405); // Method Not Allowed
            throw new Exception("Método HTTP no permitido");
    }

} catch (Exception $e) {
    http_response_code(400); // Bad Request
    $response = [
        "success" => false,
        "message" => $e->getMessage()
    ];
}

// Enviar respuesta
echo json_encode($response, JSON_UNESCAPED_UNICODE);
