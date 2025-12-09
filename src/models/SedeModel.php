<?php

class SedeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM sede WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_sede != :excludeId";
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
    // CREAR SEDE
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["nombre"]) || trim($data["nombre"]) === "") {
            return ['success' => false, 'message' => 'El nombre de la sede es requerido'];
        }

        if (!isset($data["id_ciudad"]) || !is_numeric($data["id_ciudad"])) {
            return ['success' => false, 'message' => 'La ciudad es requerida'];
        }

        if ($this->existsByNombre($data["nombre"])) {
            return ['success' => false, 'message' => 'Ya existe una sede con ese nombre'];
        }

        try {
            $sql = "INSERT INTO sede (nombre, direccion, telefono, id_ciudad, estado) 
                    VALUES (:nombre, :direccion, :telefono, :id_ciudad, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"],
                ":direccion" => $data["direccion"] ?? null,
                ":telefono" => $data["telefono"] ?? null,
                ":id_ciudad" => $data["id_ciudad"]
            ]);

            return [
                'success' => true,
                'message' => 'Sede creada correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER SEDE POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT s.id_sede, s.nombre, s.direccion, s.telefono, s.id_ciudad, s.estado,
                           c.nombre as ciudad_nombre
                    FROM sede s
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    WHERE s.id_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS SEDES
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT s.id_sede, s.nombre, s.direccion, s.telefono, s.id_ciudad, s.estado,
                           c.nombre as ciudad_nombre
                    FROM sede s
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad";
            if ($soloActivos) {
                $sql .= " WHERE s.estado = 1";
            }
            $sql .= " ORDER BY c.nombre ASC, s.nombre ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR SEDE
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        if (isset($data["nombre"]) && $this->existsByNombre($data["nombre"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otra sede con ese nombre'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
            }
            
            if (isset($data["direccion"])) {
                $campos[] = "direccion = :direccion";
                $params[":direccion"] = $data["direccion"];
            }
            
            if (isset($data["telefono"])) {
                $campos[] = "telefono = :telefono";
                $params[":telefono"] = $data["telefono"];
            }
            
            if (isset($data["id_ciudad"])) {
                $campos[] = "id_ciudad = :id_ciudad";
                $params[":id_ciudad"] = $data["id_ciudad"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE sede SET " . implode(", ", $campos) . " WHERE id_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Sede actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro la sede o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR SEDE (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si tiene salas asociadas
            $sqlCheck = "SELECT COUNT(*) FROM sala WHERE id_sede = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene salas asociadas'];
            }

            // Verificar si tiene productos en producto_sede
            $sqlCheck2 = "SELECT COUNT(*) FROM producto_sede WHERE id_sede = :id";
            $stmtCheck2 = $this->conn->prepare($sqlCheck2);
            $stmtCheck2->execute([":id" => $id]);
            
            if ($stmtCheck2->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene productos asociados'];
            }

            // Verificar si tiene trabajadores asociados
            $sqlCheck3 = "SELECT COUNT(*) FROM trabajador WHERE id_sede = :id";
            $stmtCheck3 = $this->conn->prepare($sqlCheck3);
            $stmtCheck3->execute([":id" => $id]);
            
            if ($stmtCheck3->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene trabajadores asociados'];
            }

            // Soft delete
            $sql = "UPDATE sede SET estado = 0 WHERE id_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Sede eliminada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro la sede'];
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
            $sql = "UPDATE sede SET estado = :estado WHERE id_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activada' : 'desactivada';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Sede {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro la sede'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR SEDES
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM sede";
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

    // -------------------------------------------------
    // OBTENER SEDES POR CIUDAD
    // -------------------------------------------------
    public function getByCiudad($idCiudad) {
        try {
            $sql = "SELECT id_sede, nombre, direccion, telefono, estado 
                    FROM sede 
                    WHERE id_ciudad = :id_ciudad AND estado = 1
                    ORDER BY nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_ciudad" => $idCiudad]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}