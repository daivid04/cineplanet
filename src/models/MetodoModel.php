<?php

class MetodoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM metodo WHERE LOWER(nombre_metodo) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_metodo != :excludeId";
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
    // CREAR METODO
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["nombre_metodo"]) || trim($data["nombre_metodo"]) === "") {
            return ['success' => false, 'message' => 'El nombre del metodo es requerido'];
        }

        if ($this->existsByNombre($data["nombre_metodo"])) {
            return ['success' => false, 'message' => 'Ya existe un metodo con ese nombre'];
        }

        try {
            $sql = "INSERT INTO metodo (nombre_metodo, descripcion, icono, estado) VALUES (:nombre, :descripcion, :icono, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre_metodo"],
                ":descripcion" => $data["descripcion"] ?? null,
                ":icono" => $data["icono"] ?? null
            ]);

            return [
                'success' => true,
                'message' => 'Metodo de pago creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER METODO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_metodo, nombre_metodo, descripcion, icono, estado FROM metodo WHERE id_metodo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS METODOS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_metodo, nombre_metodo, descripcion, icono, estado FROM metodo";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $sql .= " ORDER BY nombre_metodo ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR METODO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        if (isset($data["nombre_metodo"]) && $this->existsByNombre($data["nombre_metodo"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otro metodo con ese nombre'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre_metodo"])) {
                $campos[] = "nombre_metodo = :nombre";
                $params[":nombre"] = $data["nombre_metodo"];
            }
            
            if (isset($data["descripcion"])) {
                $campos[] = "descripcion = :descripcion";
                $params[":descripcion"] = $data["descripcion"];
            }
            
            if (isset($data["icono"])) {
                $campos[] = "icono = :icono";
                $params[":icono"] = $data["icono"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE metodo SET " . implode(", ", $campos) . " WHERE id_metodo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Metodo actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el metodo o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR METODO (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si tiene compras asociadas
            $sqlCheck = "SELECT COUNT(*) FROM compra WHERE id_metodo = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene compras asociadas'];
            }

            // Soft delete
            $sql = "UPDATE metodo SET estado = 0 WHERE id_metodo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Metodo eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el metodo'];
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
            $sql = "UPDATE metodo SET estado = :estado WHERE id_metodo = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activado' : 'desactivado';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Metodo {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro el metodo'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR METODOS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM metodo";
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
