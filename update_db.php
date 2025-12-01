<?php
require_once __DIR__ . '/src/services/conexion.php';

try {
    // Check if column exists
    $checkSql = "SHOW COLUMNS FROM trabajador LIKE 'fecha_ingreso'";
    $stmt = $conn->query($checkSql);
    
    if ($stmt->rowCount() == 0) {
        // Add column
        $sql = "ALTER TABLE trabajador ADD COLUMN fecha_ingreso DATE DEFAULT NULL";
        $conn->exec($sql);
        echo "Columna 'fecha_ingreso' agregada exitosamente.<br>";
        
        // Update existing records with a default date (e.g., today or random)
        // For simulation, we'll just set it to current date for now, or leave null if acceptable.
        // Let's set a default for existing ones so the UI looks populated.
        $updateSql = "UPDATE trabajador SET fecha_ingreso = CURDATE() WHERE fecha_ingreso IS NULL";
        $conn->exec($updateSql);
        echo "Registros existentes actualizados con fecha actual.<br>";
    } else {
        echo "La columna 'fecha_ingreso' ya existe.<br>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
