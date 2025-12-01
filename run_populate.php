<?php
require_once __DIR__ . '/src/services/conexion.php';

function runSqlFile($conn, $file) {
    echo "Procesando archivo: " . basename($file) . "\n";
    $sql = file_get_contents($file);
    
    // Split by semicolon
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
            } catch (PDOException $e) {
                echo "Error ejecutando sentencia: " . substr($statement, 0, 50) . "... \n";
                echo "Mensaje: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Archivo procesado.\n";
}

try {
    runSqlFile($conn, __DIR__ . '/db/populate_horarios.sql');
    echo "Datos de horarios poblados exitosamente.\n";
} catch (PDOException $e) {
    echo "Error crítico: " . $e->getMessage() . "\n";
}
?>
