<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

require_once "../../src/services/conexion.php";
require_once "../../src/controllers/ciudad_controller.php";

$controller = new CiudadController($conn);
$method = $_SERVER["REQUEST_METHOD"];

try {
    switch ($method) {
        case 'GET':
            // Obtener todas las ciudades
            if (isset($_GET["all"])) {
                echo json_encode($controller->getAll());
                break;
            }

            // Obtener ciudad por ID
            if (isset($_GET["id"])) {
                echo json_encode($controller->getById($_GET["id"]));
                break;
            }

            // Si no se pasa ningún parámetro, lanzar un error
            throw new Exception("Parámetros inválidos para la solicitud GET");
            break;

        case 'POST':
            // Crear nueva ciudad
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception("No se recibieron datos para crear la ciudad");
            }

            echo json_encode($controller->create($data));
            break;

        case 'PUT':
            // Actualizar ciudad
            if (!isset($_GET["id"])) {
                throw new Exception("ID de ciudad no especificado");
            }

            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception("No se recibieron datos para actualizar la ciudad");
            }

            echo json_encode($controller->update($_GET["id"], $data));
            break;

        case 'DELETE':
            // Eliminar ciudad
            if (!isset($_GET["id"])) {
                throw new Exception("ID de ciudad no especificado");
            }

            echo json_encode($controller->delete($_GET["id"]));
            break;

        default:
            http_response_code(405);
            echo json_encode([
                "success" => false,
                "message" => "Método no permitido"
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
