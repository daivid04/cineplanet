<?php

class ComboModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM combos WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_combo != :excludeId";
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
    // CREAR COMBO
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["nombre"]) || trim($data["nombre"]) === "") {
            return ['success' => false, 'message' => 'El nombre del combo es requerido'];
        }

        if (!isset($data["precio"]) || !is_numeric($data["precio"]) || $data["precio"] <= 0) {
            return ['success' => false, 'message' => 'El precio debe ser un numero mayor a 0'];
        }

        if ($this->existsByNombre($data["nombre"])) {
            return ['success' => false, 'message' => 'Ya existe un combo con ese nombre'];
        }

        try {
            $sql = "INSERT INTO combos (nombre, precio, estado, url_combo) VALUES (:nombre, :precio, 1, :url_combo)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"],
                ":precio" => $data["precio"],
                ":url_combo" => $data["url_combo"] ?? null
            ]);

            return [
                'success' => true,
                'message' => 'Combo creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER COMBO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_combo, nombre, precio, estado, url_combo FROM combos WHERE id_combo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS COMBOS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_combo, nombre, precio, estado, url_combo FROM combos";
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
    // ACTUALIZAR COMBO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        if (isset($data["nombre"]) && $this->existsByNombre($data["nombre"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otro combo con ese nombre'];
        }

        if (isset($data["precio"]) && (!is_numeric($data["precio"]) || $data["precio"] <= 0)) {
            return ['success' => false, 'message' => 'El precio debe ser un numero mayor a 0'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
            }
            
            if (isset($data["precio"])) {
                $campos[] = "precio = :precio";
                $params[":precio"] = $data["precio"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE combos SET " . implode(", ", $campos) . " WHERE id_combo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Combo actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el combo o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR COMBO (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si tiene productos asociados en producto_combo
            $sqlCheck = "SELECT COUNT(*) FROM producto_combo WHERE id_combo = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene productos asociados'];
            }

            // Verificar si tiene compras asociadas en compra_cliente
            $sqlCheck2 = "SELECT COUNT(*) FROM compra_cliente WHERE id_combo = :id";
            $stmtCheck2 = $this->conn->prepare($sqlCheck2);
            $stmtCheck2->execute([":id" => $id]);
            
            if ($stmtCheck2->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene compras asociadas'];
            }

            // Soft delete
            $sql = "UPDATE combos SET estado = 0 WHERE id_combo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Combo eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el combo'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            $sql = "UPDATE combos SET estado = :estado WHERE id_combo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activado' : 'desactivado';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Combo {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro el combo'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR COMBOS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM combos";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $stmt = $this->conn->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
