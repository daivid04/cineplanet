<?php
/**
 * CompraModel - Modelo para procesar compras usando stored procedures
 */

class CompraModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Realizar compra completa usando el stored procedure
     * 
     * @param array $data Datos de la compra:
     *   - id_usuario: int
     *   - id_metodo: int
     *   - id_funcion: int
     *   - id_sala: int
     *   - id_sede: int
     *   - precio_total_boleto: float
     *   - asientos: array [["id_asiento" => 1, "id_tipo_entrada" => 1], ...]
     *   - dulceria: array [["id_combo" => 1, "precio" => 35.00], ...]
     *   - precio_total_dulceria: float
     * 
     * @return array ['success' => bool, 'message' => string, 'id_compra' => int|null]
     */
    public function realizarCompra($data) {
        try {
            // Validaciones básicas
            if (!isset($data['id_usuario']) || !is_numeric($data['id_usuario'])) {
                return ['success' => false, 'message' => 'Usuario no válido'];
            }
            
            if (!isset($data['id_metodo']) || !is_numeric($data['id_metodo'])) {
                return ['success' => false, 'message' => 'Método de pago no válido'];
            }
            
            if (!isset($data['id_funcion']) || !is_numeric($data['id_funcion'])) {
                return ['success' => false, 'message' => 'Función no válida'];
            }
            
            if (!isset($data['asientos']) || !is_array($data['asientos']) || count($data['asientos']) === 0) {
                return ['success' => false, 'message' => 'Debe seleccionar al menos un asiento'];
            }

            // Convertir arrays a JSON para el procedure
            $asientosJson = json_encode($data['asientos']);
            $dulceriaJson = isset($data['dulceria']) && is_array($data['dulceria']) 
                ? json_encode($data['dulceria']) 
                : '[]';
            
            $precioTotalDulceria = $data['precio_total_dulceria'] ?? 0;

            // Llamar al stored procedure
            $sql = "CALL sp_realizar_compra(
                :id_usuario,
                :id_metodo,
                :id_funcion,
                :id_sala,
                :id_sede,
                :precio_total_boleto,
                :asientos_json,
                :dulceria_json,
                :precio_total_dulceria,
                @id_compra,
                @resultado
            )";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_usuario' => $data['id_usuario'],
                ':id_metodo' => $data['id_metodo'],
                ':id_funcion' => $data['id_funcion'],
                ':id_sala' => $data['id_sala'],
                ':id_sede' => $data['id_sede'],
                ':precio_total_boleto' => $data['precio_total_boleto'],
                ':asientos_json' => $asientosJson,
                ':dulceria_json' => $dulceriaJson,
                ':precio_total_dulceria' => $precioTotalDulceria
            ]);

            // Obtener variables de salida
            $result = $this->conn->query("SELECT @id_compra as id_compra, @resultado as resultado")->fetch(PDO::FETCH_ASSOC);

            if ($result['id_compra']) {
                return [
                    'success' => true,
                    'message' => $result['resultado'],
                    'id_compra' => (int) $result['id_compra']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $result['resultado'] ?? 'Error desconocido al procesar la compra'
                ];
            }

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error de base de datos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Cancelar una compra
     */
    public function cancelarCompra($idCompra) {
        try {
            $stmt = $this->conn->prepare("CALL sp_cancelar_compra(:id_compra, @resultado)");
            $stmt->execute([':id_compra' => $idCompra]);

            $result = $this->conn->query("SELECT @resultado as resultado")->fetch(PDO::FETCH_ASSOC);

            $success = strpos($result['resultado'], 'ERROR') === false;
            
            return [
                'success' => $success,
                'message' => $result['resultado']
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verificar disponibilidad de asientos
     */
    public function verificarAsientos($asientosIds) {
        try {
            $asientosJson = json_encode($asientosIds);
            
            $stmt = $this->conn->prepare("CALL sp_verificar_asientos(:asientos, @disponibles, @mensaje)");
            $stmt->execute([':asientos' => $asientosJson]);

            $result = $this->conn->query("SELECT @disponibles as disponibles, @mensaje as mensaje")->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'disponibles' => (bool) $result['disponibles'],
                'message' => $result['mensaje']
            ];

        } catch (PDOException $e) {
            return [
                'success' => false,
                'disponibles' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener compra por ID
     */
    public function getById($idCompra) {
        try {
            $sql = "SELECT 
                        c.id_compra,
                        c.fecha,
                        c.id_usuario,
                        m.nombre_metodo,
                        cb.precio_total_boleto,
                        f.id_funcion,
                        p.nombre as pelicula,
                        s.nombre as sede
                    FROM compra c
                    JOIN metodo m ON c.id_metodo = m.id_metodo
                    LEFT JOIN compra_boleto cb ON c.id_compra = cb.id_compra
                    LEFT JOIN funcion f ON cb.id_funcion = f.id_funcion
                    LEFT JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    LEFT JOIN sede s ON cb.id_sede = s.id_sede
                    WHERE c.id_compra = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $idCompra]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtener compras de un usuario
     */
    public function getByUsuario($idUsuario) {
        try {
            $sql = "SELECT 
                        c.id_compra,
                        c.fecha,
                        m.nombre_metodo,
                        cb.precio_total_boleto,
                        p.nombre as pelicula,
                        f.fecha as fecha_funcion,
                        f.hora as hora_funcion
                    FROM compra c
                    JOIN metodo m ON c.id_metodo = m.id_metodo
                    LEFT JOIN compra_boleto cb ON c.id_compra = cb.id_compra
                    LEFT JOIN funcion f ON cb.id_funcion = f.id_funcion
                    LEFT JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    WHERE c.id_usuario = :id_usuario
                    ORDER BY c.fecha DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_usuario' => $idUsuario]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return [];
        }
    }
}
?>
