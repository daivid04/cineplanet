<?php
/**
 * API para Dashboard - Estadísticas del sistema
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../../../src/services/conexion.php';

try {
    $stats = [];

    // Contar ciudades
    $stmt = $conn->query("SELECT COUNT(*) as total FROM ciudad");
    $stats['ciudades'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar sedes
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sede");
    $stats['sedes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar sedes activas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sede WHERE estado = 1");
    $stats['sedes_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar películas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pelicula");
    $stats['peliculas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar películas activas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pelicula WHERE estado = 1");
    $stats['peliculas_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar productos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM producto");
    $stats['productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar productos activos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM producto WHERE estado = 1");
    $stats['productos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar combos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM combos");
    $stats['combos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar combos activos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM combos WHERE estado = 1");
    $stats['combos_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar funciones
    $stmt = $conn->query("SELECT COUNT(*) as total FROM funcion");
    $stats['funciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar funciones activas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM funcion WHERE estado = 1");
    $stats['funciones_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar salas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sala");
    $stats['salas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar salas activas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM sala WHERE estado = 1");
    $stats['salas_activas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar usuarios
    $stmt = $conn->query("SELECT COUNT(*) as total FROM usuario");
    $stats['usuarios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar usuarios activos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM usuario WHERE estado = 1");
    $stats['usuarios_activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar socios
    $stmt = $conn->query("SELECT COUNT(*) as total FROM socio");
    $stats['socios'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar invitados
    $stmt = $conn->query("SELECT COUNT(*) as total FROM invitado");
    $stats['invitados'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar idiomas
    $stmt = $conn->query("SELECT COUNT(*) as total FROM idioma");
    $stats['idiomas'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar formatos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM formato");
    $stats['formatos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar métodos de pago
    $stmt = $conn->query("SELECT COUNT(*) as total FROM metodo");
    $stats['metodos_pago'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar tipos de socio
    $stmt = $conn->query("SELECT COUNT(*) as total FROM tipo_socio");
    $stats['tipos_socio'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Contar asientos
    $stmt = $conn->query("SELECT COUNT(*) as total FROM asiento");
    $stats['asientos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Últimas películas agregadas
    $stmt = $conn->query("SELECT id_pelicula, nombre as titulo, duracion, estado FROM pelicula ORDER BY id_pelicula DESC LIMIT 5");
    $stats['ultimas_peliculas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Últimas funciones
    $stmt = $conn->query("
        SELECT f.id_funcion, p.nombre as pelicula, CONCAT('Sala ', s.num_sala) as sala, se.nombre as sede,
               f.fecha, f.hora, f.estado
        FROM funcion f
        INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
        INNER JOIN sala s ON f.id_sala = s.id_sala
        INNER JOIN sede se ON s.id_sede = se.id_sede
        ORDER BY f.id_funcion DESC LIMIT 5
    ");
    $stats['ultimas_funciones'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Productos por sede (inventario)
    $stmt = $conn->query("
        SELECT se.nombre as sede, COUNT(ps.id_producto_sede) as productos, SUM(ps.stock) as stock_total
        FROM sede se
        LEFT JOIN producto_sede ps ON se.id_sede = ps.id_sede
        GROUP BY se.id_sede, se.nombre
        ORDER BY productos DESC
        LIMIT 5
    ");
    $stats['inventario_por_sede'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Socios por tipo
    $stmt = $conn->query("
        SELECT ts.nombre as tipo, COUNT(s.id_usuario) as total
        FROM tipo_socio ts
        LEFT JOIN socio s ON ts.id_socio = s.id_tipo_socio
        GROUP BY ts.id_socio, ts.nombre
        ORDER BY total DESC
    ");
    $stats['socios_por_tipo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
    ]);
}
