<?php
try {
    $host = "srv812.hstgr.io";
    $username = "u914095763_g1";
    $password = "bd4jUV12";
    $database = "u914095763_g1";
    
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$database;charset=utf8", $username, $password);
    
    // Configurar encoding para PHP
    header('Content-Type: text/html; charset=utf-8');
        
    // Puedes probar una consulta simple
    $stmt = $conn->query("SELECT 1");
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>