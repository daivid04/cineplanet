<?php
/**
 * Modelo para Tipo de Entrada
 */
class TipoEntradaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Obtener todos los tipos de entrada activos
     */
    public function getAll($soloActivos = true) {
        try {
            $sql = "SELECT * FROM tipo_entrada";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $sql .= " ORDER BY categoria, precio";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtener por ID
     */
    public function getById($id) {
        try {
            $sql = "SELECT * FROM tipo_entrada WHERE id_tipo_entrada = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Obtener por categoría
     */
    public function getByCategoria($categoria) {
        try {
            $sql = "SELECT * FROM tipo_entrada WHERE categoria = :categoria AND estado = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':categoria' => $categoria]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Crear nuevo tipo de entrada
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO tipo_entrada (nombre, categoria, precio, descripcion, estado) 
                    VALUES (:nombre, :categoria, :precio, :descripcion, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':categoria' => $data['categoria'],
                ':precio' => $data['precio'],
                ':descripcion' => $data['descripcion'] ?? ''
            ]);
            return [
                'success' => true,
                'message' => 'Tipo de entrada creado',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Actualizar tipo de entrada
     */
    public function update($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];

            if (isset($data['nombre'])) {
                $fields[] = "nombre = :nombre";
                $params[':nombre'] = $data['nombre'];
            }
            if (isset($data['categoria'])) {
                $fields[] = "categoria = :categoria";
                $params[':categoria'] = $data['categoria'];
            }
            if (isset($data['precio'])) {
                $fields[] = "precio = :precio";
                $params[':precio'] = $data['precio'];
            }
            if (isset($data['descripcion'])) {
                $fields[] = "descripcion = :descripcion";
                $params[':descripcion'] = $data['descripcion'];
            }
            if (isset($data['estado'])) {
                $fields[] = "estado = :estado";
                $params[':estado'] = $data['estado'];
            }

            if (empty($fields)) {
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }

            $sql = "UPDATE tipo_entrada SET " . implode(", ", $fields) . " WHERE id_tipo_entrada = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'message' => 'Tipo de entrada actualizado'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Cambiar estado
     */
    public function toggleEstado($id, $estado) {
        return $this->update($id, ['estado' => $estado]);
    }
}
