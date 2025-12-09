<?php
header("Content-Type: application/json; charset=utf-8");

require_once "../../src/services/conexion.php";
require_once "../../src/controllers/invitado_controler.php";

$controller = new InvitadoController($conn);
$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true) ?? [];

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET["id"])) {
                echo json_encode($controller->getId($_GET["id"]));
            } else {
                echo json_encode($controller->get());
            }
            break;

        case 'POST':
            echo json_encode(["ok" => $controller->create($input)]);
            break;

        case 'PUT':
            if (!isset($_GET["id"])) throw new Exception("Falta el ID");
            echo json_encode(["ok" => $controller->update($_GET["id"], $input)]);
            break;

        case 'DELETE':
            if (!isset($_GET["id"])) throw new Exception("Falta el ID");
            echo json_encode(["ok" => $controller->delete($_GET["id"])]);
            break;

        default:
            http_response_code(405);
            echo json_encode(["ok" => false, "msg" => "Método no permitido"]);
            break;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["ok" => false, "error" => $e->getMessage()]);
}
?>