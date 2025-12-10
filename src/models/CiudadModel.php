<?php

class CiudadModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function validarCiudad($data, $modo = "insertar") {
        $requeridos = ["nombre"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }
        return true;
    }

    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM ciudad WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_ciudad != :excludeId";
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
    // CREAR CIUDAD
    // -------------------------------------------------
    public function create($data) {
        $this->validarCiudad($data, "insertar");

        // Verificar si ya existe
        if ($this->existsByNombre($data["nombre"])) {
            return ['success' => false, 'message' => 'Ya existe una ciudad con ese nombre'];
        }

        try {
            $sql = "INSERT INTO ciudad (nombre, estado) VALUES (:nombre, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":nombre" => $data["nombre"]]);

            return [
                'success' => true,
                'message' => 'Ciudad creada correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER CIUDAD POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_ciudad, nombre, estado FROM ciudad WHERE id_ciudad = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS CIUDADES
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_ciudad, nombre, estado FROM ciudad";
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
    // ACTUALIZAR CIUDAD
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        // Verificar si existe otra ciudad con el mismo nombre
        if (isset($data["nombre"]) && $this->existsByNombre($data["nombre"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otra ciudad con ese nombre'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE ciudad SET " . implode(", ", $campos) . " WHERE id_ciudad = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Ciudad actualizada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro la ciudad o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR CIUDAD (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si hay sedes asociadas activas
            $sqlCheck = "SELECT COUNT(*) FROM sede WHERE id_ciudad = :id AND estado = 1";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene sedes asociadas'];
            }

            // Soft delete
            $sql = "UPDATE ciudad SET estado = 0 WHERE id_ciudad = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Ciudad eliminada correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro la ciudad'];
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
            $sql = "UPDATE ciudad SET estado = :estado WHERE id_ciudad = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activada' : 'desactivada';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Ciudad {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro la ciudad'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR CIUDADES
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM ciudad";
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
