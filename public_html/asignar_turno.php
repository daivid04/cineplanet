<?php
// Incluir conexión y controlador
// Incluir conexión y controlador
require_once __DIR__ . '/../src/services/conexion.php';
require_once __DIR__ . '/../src/controllers/HorarioController.php';

// Instanciar controlador
$controller = new HorarioController($conn);

// Variables para la vista
$pageTitle = "Asignar Nuevo Turno";
$activePage = "horarios";
$message = '';
$error = '';

// Manejar POST (Guardar turno)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $controller->store($_POST);
    if ($result['success']) {
        // Redirigir a la lista de horarios con mensaje de éxito (simulado por ahora)
        header("Location: horarios.php");
        exit;
    } else {
        $error = $result['message'];
    }
}

// Obtener datos para el formulario (Lista de empleados)
$createData = $controller->create();
$workers = $createData['success'] ? $createData['workers'] : [];

// Renderizar vista
include __DIR__ . '/views/asignar_turno.php';
?>
