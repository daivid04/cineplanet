<?php

class GeneroModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM genero WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_genero != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    // -------------------------------------------------
    // CREAR GÉNERO
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["nombre"]) || trim($data["nombre"]) === "") {
            return ['success' => false, 'message' => 'El nombre del género es requerido'];
        }

        if ($this->existsByNombre($data["nombre"])) {
            return ['success' => false, 'message' => 'Ya existe un género con ese nombre'];
        }

        try {
            $sql = "INSERT INTO genero (nombre, estado) VALUES (:nombre, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":nombre" => trim($data["nombre"])]);

            return [
                'success' => true,
                'message' => 'Género creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER GÉNERO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_genero, nombre, estado FROM genero WHERE id_genero = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS GÉNEROS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_genero, nombre, estado FROM genero";
            
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            
            $sql .= " ORDER BY nombre ASC";
            
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR GÉNERO
    // -------------------------------------------------
    public function update($id, $data) {
        $genero = $this->getById($id);
        if (!$genero) {
            return ['success' => false, 'message' => 'Género no encontrado'];
        }

        if (isset($data['nombre']) && trim($data['nombre']) === '') {
            return ['success' => false, 'message' => 'El nombre no puede estar vacío'];
        }

        if (isset($data['nombre']) && $this->existsByNombre($data['nombre'], $id)) {
            return ['success' => false, 'message' => 'Ya existe otro género con ese nombre'];
        }

        try {
            $campos = [];
            $params = [':id' => $id];

            if (isset($data['nombre'])) {
                $campos[] = "nombre = :nombre";
                $params[':nombre'] = trim($data['nombre']);
            }

            if (isset($data['estado'])) {
                $campos[] = "estado = :estado";
                $params[':estado'] = intval($data['estado']);
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No hay datos para actualizar'];
            }

            $sql = "UPDATE genero SET " . implode(', ', $campos) . " WHERE id_genero = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'message' => 'Género actualizado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO (TOGGLE)
    // -------------------------------------------------
    public function toggleEstado($id, $estadoActual) {
        $genero = $this->getById($id);
        if (!$genero) {
            return ['success' => false, 'message' => 'Género no encontrado'];
        }

        try {
            $nuevoEstado = $estadoActual == 1 ? 0 : 1;
            $sql = "UPDATE genero SET estado = :estado WHERE id_genero = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);

            return [
                'success' => true,
                'message' => $nuevoEstado == 1 ? 'Género activado' : 'Género desactivado'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR GÉNERO (SOFT DELETE)
    // -------------------------------------------------
    public function delete($id) {
        $genero = $this->getById($id);
        if (!$genero) {
            return ['success' => false, 'message' => 'Género no encontrado'];
        }

        try {
            // Soft delete - cambiar estado a 0
            $sql = "UPDATE genero SET estado = 0 WHERE id_genero = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return ['success' => true, 'message' => 'Género eliminado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR GÉNEROS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM genero";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($result['total']);
        } catch (PDOException $e) {
            return 0;
        }
    }
}
