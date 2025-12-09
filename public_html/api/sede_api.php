<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

require_once "../../src/services/conexion.php";
require_once "../../src/controllers/sede_controller.php";

$controller = new SedeController($conn);
$method = $_SERVER["REQUEST_METHOD"];

try {
    switch ($method) {
        case 'GET':
            // Obtener todas las sedes
            if (isset($_GET["all"])) {
                echo json_encode($controller->getAll());
                break;
            }

            // Obtener sede por ID
            if (isset($_GET["id"])) {
                echo json_encode($controller->getById($_GET["id"]));
                break;
            }

            // Si no se pasa ningún parámetro, lanzar un error
            throw new Exception("Parámetros inválidos para la solicitud GET");
            break;

        case 'POST':
            // Crear nueva sede
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception("No se recibieron datos para crear la sede");
            }

            echo json_encode($controller->create($data));
            break;

        case 'PUT':
            // Actualizar sede
            if (!isset($_GET["id"])) {
                throw new Exception("ID de sede no especificado");
            }

            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception("No se recibieron datos para actualizar la sede");
            }

            echo json_encode($controller->update($_GET["id"], $data));
            break;

        case 'DELETE':
            // Eliminar sede
            if (!isset($_GET["id"])) {
                throw new Exception("ID de sede no especificado");
            }

            echo json_encode($controller->delete($_GET["id"]));
            break;

        default:
            throw new Exception("Método no permitido");
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
