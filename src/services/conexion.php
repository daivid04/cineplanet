<?php
try {
    $host = "srv812.hstgr.io";
    $username = "u914095763_g1";
    $password = "bd4jUV12";
    $database = "u914095763_g1";
    
    $conn = new PDO("mysql:host=$host;port=3306;dbname=$database;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    die("Error de conexion: " . $e->getMessage());
}
?>