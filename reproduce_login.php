<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/src/services/conexion.php";

$documento = "12345678";
$contrasena_ingresada = "12345678"; // The one the user is trying

echo "Testing login for document: $documento\n";

try {
    $sql = "SELECT id_usuario, nombre, apellido, documento, id_tipo_socio, contrasena 
            FROM socio 
            WHERE documento = :documento";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([":documento" => $documento]);
    
    $socios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($socios) . " users with document $documento\n";

    foreach ($socios as $socio) {
        echo "ID: " . $socio['id_usuario'] . ", Name: " . $socio['nombre'] . ", Pass: " . $socio['contrasena'] . "\n";
        
        if (password_verify($contrasena_ingresada, $socio["contrasena"])) {
            echo "  -> password_verify: MATCH\n";
        } elseif ($contrasena_ingresada === $socio["contrasena"]) {
             echo "  -> Plain text check: MATCH\n";
        } else {
             echo "  -> NO MATCH\n";
        }
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
?>
