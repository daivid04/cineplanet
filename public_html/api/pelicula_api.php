<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=utf-8");

require_once "../../src/services/conexion.php";
require_once "../../src/controllers/pelicula_controler.php";

$controller = new PeliculaController($conn);
$method = $_SERVER["REQUEST_METHOD"];

try {
    switch ($method) {
        case 'GET':

            if(isset($_GET["all"])){
              echo json_encode($controller->getAll());
              break;
            }
            // Obtener película por ID
            if (isset($_GET["id"])) {
                echo json_encode($controller->getById($_GET["id"]));
                break;
            }

            // Buscar películas por nombre
            if (isset($_GET["name"])) {
                echo json_encode($controller->searchByName($_GET["name"]));
                break;
            }
          
            if (isset($_GET["billBoard"])) {
                echo json_encode($controller->getCartelera($_GET["billBoard"]));
                break;
            }

            // Si no se pasa ningún parámetro, lanzar un error
            throw new Exception("Parámetros inválidos para la solicitud GET");
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