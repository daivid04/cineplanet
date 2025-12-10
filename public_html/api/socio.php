<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ .  "/../../src/services/conexion.php";
require_once __DIR__ . "/../../src/controllers/SocioController.php";

$controller = new SocioController($conn);

$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {
        case 'GET':
            if (isset($_GET["id"])) {
                $result = $controller->getId($_GET["id"]);
                
                error_log("Resultado GET por ID: ". json_encode($result));
                echo json_encode($result);
            } else {
                $result = $controller->get();
                error_log("Resultado GET todos: " . json_encode($result));
                echo json_encode($result);
            }
            break;

        case 'POST':
            $result = $controller->create($input);
            error_log("Resultado POST: " . json_encode($result));
            echo json_encode(["ok" => $result]);
            break;

        case 'PUT':
            if (!isset($_GET["id"])) throw new Exception("Falta el ID");
            $result = $controller->update($_GET["id"], $input);
            error_log("Resultado PUT: " . json_encode($result));
            echo json_encode(["ok" => $result]);
            break;

        case 'DELETE':
            if (!isset($_GET["id"])) throw new Exception("Falta el ID");
            $result = $controller->delete($_GET["id"]);
            error_log("Resultado DELETE: " . json_encode($result));
            echo json_encode(["ok" => $result]);
            break;

        default:
            throw new Exception("Método no permitido");
    }
} catch (Exception $e) {
    error_log("Error en socio.php: " . $e->getMessage());
    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ]);
}

