<?php
require_once __DIR__ . '/src/services/conexion.php';

function runSqlFile($conn, $file) {
    echo "Procesando archivo: " . basename($file) . "\n";
    $sql = file_get_contents($file);
    
    // Remove DROP/CREATE/USE DATABASE commands as we might not have permissions or want to keep the DB
    $sql = preg_replace('/^DROP DATABASE.*;/m', '', $sql);
    $sql = preg_replace('/^CREATE DATABASE.*;/m', '', $sql);
    $sql = preg_replace('/^USE.*;/m', '', $sql);
    
    // Split by semicolon, but be careful with triggers/procedures if any (none in this simple schema)
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            try {
                $conn->exec($statement);
            } catch (PDOException $e) {
                // Ignore "table already exists" if we are just adding, but ideally we dropped them.
                // However, since we removed DROP DATABASE, we might hit errors if tables exist.
                // Let's try to continue and see.
                echo "Error ejecutando sentencia: " . substr($statement, 0, 50) . "... \n";
                echo "Mensaje: " . $e->getMessage() . "\n";
            }
        }
    }
    echo "Archivo procesado.\n";
}

try {
    // Disable foreign key checks to allow dropping tables in any order
    $conn->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    // Get all tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Drop all tables
    foreach ($tables as $table) {
        echo "Eliminando tabla: $table\n";
        $conn->exec("DROP TABLE IF EXISTS `$table`");
    }
    
    // Re-enable foreign key checks
    $conn->exec("SET FOREIGN_KEY_CHECKS = 1");
    
    // Run SQL files
    runSqlFile($conn, __DIR__ . '/db/cineplanet.sql');
    runSqlFile($conn, __DIR__ . '/db/datos.sql');
    
    echo "Instalación completada exitosamente.\n";
    
} catch (PDOException $e) {
    echo "Error crítico: " . $e->getMessage() . "\n";
}
?>
