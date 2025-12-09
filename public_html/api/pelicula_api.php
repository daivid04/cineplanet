<?php
/**
 * API REST para Pelicula
 * 
 * Endpoints:
 * GET    /api/pelicula_api.php                  - Listar todas las peliculas
 * GET    /api/pelicula_api.php?id=1             - Obtener pelicula por ID
 * GET    /api/pelicula_api.php?search=matrix    - Buscar peliculas por nombre
 * GET    /api/pelicula_api.php?activos=1        - Solo peliculas activas
 * POST   /api/pelicula_api.php                  - Crear nueva pelicula
 * PUT    /api/pelicula_api.php                  - Actualizar pelicula
 * PATCH  /api/pelicula_api.php                  - Cambiar estado (toggle)
 * DELETE /api/pelicula_api.php?id=1             - Eliminar pelicula
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/services/conexion.php';
require_once __DIR__ . '/../../src/controllers/PeliculaController.php';

$controller = new PeliculaController($conn);
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
             if(isset($_GET["all"])){
              echo json_encode($controller->getAll());
              break;
             }
             if (isset($_GET["billBoard"])) {
                echo json_encode($controller->getCartelera($_GET["billBoard"]));
                break;
            }
            if (isset($_GET['id'])) {
                $response = $controller->getById(intval($_GET['id']));
            } else if (isset($_GET['search'])) {
                $response = $controller->search($_GET['search']);
            } else {
                $soloActivos = isset($_GET['activos']) && $_GET['activos'] == '1';
                $response = $controller->getAll($soloActivos);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['nombre']) || trim($data['nombre']) === '') {
                $response = ['success' => false, 'message' => 'El nombre de la pelicula es requerido'];
            } else if (!isset($data['duracion']) || !is_numeric($data['duracion'])) {
                $response = ['success' => false, 'message' => 'La duracion es requerida'];
            } else if (!isset($data['url_imagen']) || trim($data['url_imagen']) === '') {
                $response = ['success' => false, 'message' => 'La URL de imagen es requerida'];
            } else if (!isset($data['sinopsis']) || trim($data['sinopsis']) === '') {
                $response = ['success' => false, 'message' => 'La sinopsis es requerida'];
            } else {
                $response = $controller->create($data);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id'])) {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
            } else {
                $id = intval($data['id']);
                unset($data['id']);
                $response = $controller->update($id, $data);
            }
            break;

        case 'PATCH':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['id']) || !isset($data['estado'])) {
                $response = ['success' => false, 'message' => 'ID y estado son requeridos'];
            } else {
                $response = $controller->toggleEstado(intval($data['id']), intval($data['estado']));
            }
          
            if (isset($_GET["billBoard"])) {
                echo json_encode($controller->getCartelera($_GET["billBoard"]));
                break;
            }

        case 'DELETE':
            if (!isset($_GET['id'])) {
                $response = ['success' => false, 'message' => 'El ID es requerido'];
            } else {
                $response = $controller->delete(intval($_GET['id']));
            }
            break;

        default:
            http_response_code(405);
            $response = ['success' => false, 'message' => 'Metodo no permitido'];
    }
} catch (Exception $e) {
    http_response_code(500);
    $response = ['success' => false, 'message' => 'Error del servidor: ' . $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);