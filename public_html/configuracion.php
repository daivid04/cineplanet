<?php
// Habilitar errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/ConfiguracionController.php';

$controller = new ConfiguracionController($conn);

$message = '';
$messageType = '';

// Manejar POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_profile') {
            $result = $controller->updateProfile($_POST);
        } elseif ($_POST['action'] === 'update_password') {
            $result = $controller->updatePassword($_POST);
        }

        if (isset($result)) {
            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
        }
    }
}

// Obtener datos del usuario
$response = $controller->index();
$user = $response['success'] ? $response['data'] : null;

// Variables para la vista
$pageTitle = "Configuración de Cuenta";
$activePage = "configuracion"; // Para resaltar en sidebar si se agrega lógica

// Renderizar vista
include __DIR__ . '/views/configuracion.php';
?>
