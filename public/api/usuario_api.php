<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
header("Content-Type: application/json; charset=utf-8");

require_once "../../src/services/conexion.php";
require_once "../../src/controllers/UsuarioController.php";

$controller = new UsuarioController($conn);
$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true) ?? [];

try {
    switch ($method) {
        case 'GET':
            // Obtener todos los usuarios
            if (!isset($_GET["tipo"]) && !isset($_GET["id"]) && !isset($_GET["email"])) {
                echo json_encode($controller->getAll());
                break;
            }

            // Buscar por email
            if (isset($_GET["email"])) {
                echo json_encode($controller->searchByEmail($_GET["email"]));
                break;
            }

            // Obtener usuario específico por tipo e ID
            if (isset($_GET["tipo"]) && isset($_GET["id"])) {
                echo json_encode($controller->getById($_GET["tipo"], $_GET["id"]));
            } else {
                throw new Exception("Para obtener un usuario específico, se requiere 'tipo' e 'id'");
            }
            break;

        case 'POST':
            // Crear nuevo usuario
            if (!isset($_GET["tipo"])) {
                throw new Exception("Falta el parámetro 'tipo' (socio/invitado)");
            }

            if (empty($input)) {
                throw new Exception("Datos vacíos");
            }

            echo json_encode([
                "ok" => $controller->create($_GET["tipo"], $input),
                "mensaje" => "Usuario creado exitosamente"
            ]);
            break;

        case 'PUT':
            // Actualizar usuario
            if (!isset($_GET["tipo"]) || !isset($_GET["id"])) {
                throw new Exception("Faltan parámetros: se requiere 'tipo' e 'id'");
            }

            if (empty($input)) {
                throw new Exception("Datos vacíos");
            }

            echo json_encode([
                "ok" => $controller->update($_GET["tipo"], $_GET["id"], $input),
                "mensaje" => "Usuario actualizado exitosamente"
            ]);
            break;

        case 'DELETE':
            // Eliminar usuario
            if (!isset($_GET["tipo"]) || !isset($_GET["id"])) {
                throw new Exception("Faltan parámetros: se requiere 'tipo' e 'id'");
            }

            echo json_encode([
                "ok" => $controller->delete($_GET["tipo"], $_GET["id"]),
                "mensaje" => "Usuario eliminado exitosamente"
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode([
                "ok" => false, 
                "msg" => "Método no permitido"
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ]);
}
?>