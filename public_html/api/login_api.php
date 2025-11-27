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
        
        $socio = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($socio) {
            // ¡Usuario encontrado! Ahora verificamos la contraseña
            
            // password_verify() compara la contraseña ingresada con el hash guardado
            if (password_verify($contrasena_ingresada, $socio["contrasena"])) {
                
                // ¡Éxito! Contraseña correcta.
                // Preparamos los datos del usuario para devolver al frontend
                // No incluimos la contraseña en la respuesta
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
                // Contraseña incorrecta
                http_response_code(401); // No autorizado
                $response = ["ok" => false, "error" => "Documento o contraseña incorrecta"];
            }
        } else {
            // Usuario (documento) no encontrado
            http_response_code(404); // No encontrado
            $response = ["ok" => false, "error" => "Socio no encontrado"];
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