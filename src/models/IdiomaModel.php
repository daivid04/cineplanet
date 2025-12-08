<?php

class IdiomaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM idioma WHERE LOWER(idioma) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_idioma != :excludeId";
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
    // CREAR IDIOMA
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["idioma"]) || trim($data["idioma"]) === "") {
            return ['success' => false, 'message' => 'El nombre del idioma es requerido'];
        }

        if ($this->existsByNombre($data["idioma"])) {
            return ['success' => false, 'message' => 'Ya existe un idioma con ese nombre'];
        }

        try {
            $sql = "INSERT INTO idioma (idioma, estado) VALUES (:idioma, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":idioma" => $data["idioma"]]);

            return [
                'success' => true,
                'message' => 'Idioma creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER IDIOMA POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT id_idioma, idioma, estado FROM idioma WHERE id_idioma = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS IDIOMAS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT id_idioma, idioma, estado FROM idioma";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $sql .= " ORDER BY idioma ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR IDIOMA
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        if (isset($data["idioma"]) && $this->existsByNombre($data["idioma"], $id)) {
            return ['success' => false, 'message' => 'Ya existe otro idioma con ese nombre'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["idioma"])) {
                $campos[] = "idioma = :idioma";
                $params[":idioma"] = $data["idioma"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE idioma SET " . implode(", ", $campos) . " WHERE id_idioma = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Idioma actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el idioma o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR IDIOMA (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID invalido'];
        }

        try {
            // Verificar si tiene peliculas asociadas
            $sqlCheck = "SELECT COUNT(*) FROM idiomas_pelicula WHERE id_idioma = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene peliculas asociadas'];
            }

            // Soft delete
            $sql = "UPDATE idioma SET estado = 0 WHERE id_idioma = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Idioma eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontro el idioma'];
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
            $sql = "UPDATE idioma SET estado = :estado WHERE id_idioma = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activado' : 'desactivado';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Idioma {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontro el idioma'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR IDIOMAS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM idioma";
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
