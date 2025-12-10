<?php

class TipoSocioModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM tipo_socio WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_socio != :excludeId";
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
    // CREAR TIPO DE SOCIO
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["nombre"]) || trim($data["nombre"]) === "") {
            return ['success' => false, 'message' => 'El nombre del tipo de socio es requerido'];
        }

        if ($this->existsByNombre($data["nombre"])) {
            return ['success' => false, 'message' => 'Ya existe un tipo de socio con ese nombre'];
        }

        try {
            $sql = "INSERT INTO tipo_socio (nombre, desc_dulces, desc_boleto, puntos_por_sol, estado) 
                    VALUES (:nombre, :desc_dulces, :desc_boleto, :puntos_por_sol, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"],
                ":desc_dulces" => $data["desc_dulces"] ?? 0,
                ":desc_boleto" => $data["desc_boleto"] ?? 0,
                ":puntos_por_sol" => $data["puntos_por_sol"] ?? 1
            ]);

            return [
                'success' => true,
                'message' => 'Tipo de socio creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER TIPO DE SOCIO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_socio, nombre, desc_dulces, desc_boleto, puntos_por_sol, estado 
                    FROM tipo_socio WHERE id_socio = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TIPO DE SOCIO POR USUARIO
    // -------------------------------------------------
    public function getByUsuario($idUsuario) {
        if (!is_numeric($idUsuario)) {
            return null;
        }

        try {
            $sql = "SELECT ts.id_socio, ts.nombre, ts.desc_dulces, ts.desc_boleto, ts.puntos_por_sol 
                    FROM tipo_socio ts
                    JOIN socio s ON ts.id_socio = s.id_tipo_socio
                    WHERE s.id_usuario = :id_usuario AND ts.estado = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_usuario" => $idUsuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS TIPOS DE SOCIO
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_socio, nombre, desc_dulces, desc_boleto, puntos_por_sol, estado 
                    FROM tipo_socio";
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
    // ACTUALIZAR TIPO DE SOCIO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        if (isset($data["nombre"]) && $this->existsByNombre($data["nombre"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otro tipo de socio con ese nombre'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
            }
            
            if (isset($data["desc_dulces"])) {
                $campos[] = "desc_dulces = :desc_dulces";
                $params[":desc_dulces"] = $data["desc_dulces"];
            }
            
            if (isset($data["desc_boleto"])) {
                $campos[] = "desc_boleto = :desc_boleto";
                $params[":desc_boleto"] = $data["desc_boleto"];
            }
            
            if (isset($data["puntos_por_sol"])) {
                $campos[] = "puntos_por_sol = :puntos_por_sol";
                $params[":puntos_por_sol"] = $data["puntos_por_sol"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE tipo_socio SET " . implode(", ", $campos) . " WHERE id_socio = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Tipo de socio actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el tipo de socio o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR TIPO DE SOCIO (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si tiene socios asociados
            $sqlCheck = "SELECT COUNT(*) FROM socio WHERE id_tipo_socio = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene socios asociados'];
            }

            // Soft delete
            $sql = "UPDATE tipo_socio SET estado = 0 WHERE id_socio = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Tipo de socio eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el tipo de socio'];
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
            $sql = "UPDATE tipo_socio SET estado = :estado WHERE id_socio = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activado' : 'desactivado';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Tipo de socio {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro el tipo de socio'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR TIPOS DE SOCIO
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tipo_socio";
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
