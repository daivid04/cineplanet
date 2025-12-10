<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Encabezados (Copiado de tu `usuario_api.php`) ---
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS"); 
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == 'OPTIONS') {
    http_response_code(200);
    exit;
}

// --- Incluir tu conexión a la BD (PDO) ---
// ¡IMPORTANTE! Asegúrate de que esta ruta sea correcta
require_once __DIR__ .  "/../../src/services/conexion.php"; 

$input = json_decode(file_get_contents("php://input"), true);
$response = ["ok" => false, "error" => "Error desconocido"];

try {
    if ($_SERVER["REQUEST_METHOD"] == 'POST') {
        if (!isset($input["documento"]) || !isset($input["contrasena"])) {
            throw new Exception("Se requieren 'documento' y 'contrasena'");
        }

        $documento = $input["documento"];
        $contrasena_ingresada = $input["contrasena"];

        // Usamos PDO (porque tu `conexion.php` usa PDO)
        // Buscamos al socio por su documento
        $sql = "SELECT id_usuario, nombre, apellido, documento, id_tipo_socio, contrasena 
                FROM socio 
                WHERE documento = :documento";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([":documento" => $documento]);
        
        $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $usuario_encontrado = null;

        foreach ($socios as $socio) {
            // 1. Verificar con password_verify (hash)
            if (password_verify($contrasena_ingresada, $socio["contrasena"])) {
                $usuario_encontrado = $socio;
                break;
            }
            // 2. Verificar texto plano (Legacy/Inseguro - pero necesario para datos actuales)
            if ($contrasena_ingresada === $socio["contrasena"]) {
                $usuario_encontrado = $socio;
                break;
            }
        }

        if ($usuario_encontrado) {
            // ¡Éxito! Contraseña correcta.
            $socio = $usuario_encontrado;
            
            $usuario_data = [
                "id_usuario" => $socio["id_usuario"],
                "nombre" => $socio["nombre"],
                "apellido" => $socio["apellido"],
                "documento" => $socio["documento"],
                "id_tipo_socio" => $socio["id_tipo_socio"]
            ];
            
            $response = [
                "ok" => true,
                "usuario" => $usuario_data
            ];
            
        } else {
            // Ningún usuario coincidió con la contraseña, o no se encontró el documento
            if (count($socios) > 0) {
                // El documento existe, pero la contraseña no coincidió para ninguno
                http_response_code(401); // No autorizado
                $response = ["ok" => false, "error" => "Documento o contraseña incorrecta"];
            } else {
                // Documento no encontrado
                http_response_code(404); // No encontrado
                $response = ["ok" => false, "error" => "Socio no encontrado"];
            }
        }
        
    } else {
        http_response_code(405); // Método no permitido
        throw new Exception("Método no permitido. Use POST.");
    }
} catch (Exception $e) {
    // Captura cualquier otra excepción
    http_response_code(400); // Bad Request
    $response = [
        "ok" => false,
        "error" => $e->getMessage()
    ];
}

// $conn->close(); // PDO cierra la conexión automáticamente
echo json_encode($response);
?>