<?php
class SalaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Verificar si ya existe una sala con el mismo número en la misma sede
    public function existsByNumeroYSede($numSala, $idSede, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM sala WHERE num_sala = :num_sala AND id_sede = :id_sede";
            if ($excludeId) {
                $sql .= " AND id_sala != :exclude_id";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":num_sala", $numSala, PDO::PARAM_INT);
            $stmt->bindParam(":id_sede", $idSede, PDO::PARAM_INT);
            if ($excludeId) {
                $stmt->bindParam(":exclude_id", $excludeId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Crear sala
    public function create($data) {
        try {
            // Validar que no exista la sala en esa sede
            if ($this->existsByNumeroYSede($data['num_sala'], $data['id_sede'])) {
                return [
                    "success" => false,
                    "message" => "Ya existe una sala con ese número en esta sede"
                ];
            }

            $sql = "INSERT INTO sala (num_sala, id_sede, estado) VALUES (:num_sala, :id_sede, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":num_sala", $data['num_sala'], PDO::PARAM_INT);
            $stmt->bindParam(":id_sede", $data['id_sede'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return [
                    "success" => true,
                    "message" => "Sala creada exitosamente",
                    "id" => $this->conn->lastInsertId()
                ];
            }
            
            return ["success" => false, "message" => "Error al crear la sala"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Obtener sala por ID
    public function getById($id) {
        try {
            $sql = "SELECT s.*, sd.nombre as sede_nombre, sd.id_ciudad, c.nombre as ciudad_nombre 
                    FROM sala s
                    INNER JOIN sede sd ON s.id_sede = sd.id_sede
                    INNER JOIN ciudad c ON sd.id_ciudad = c.id_ciudad
                    WHERE s.id_sala = :id_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id_sala", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // Obtener todas las salas
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT s.*, sd.nombre as sede_nombre, c.nombre as ciudad_nombre
                    FROM sala s
                    INNER JOIN sede sd ON s.id_sede = sd.id_sede
                    INNER JOIN ciudad c ON sd.id_ciudad = c.id_ciudad";
            
            if ($soloActivos) {
                $sql .= " WHERE s.estado = 1";
            }
            
            $sql .= " ORDER BY sd.nombre, s.num_sala";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // Actualizar sala
    public function update($id, $data) {
        try {
            // Validar que no exista otra sala con el mismo número en la misma sede
            if ($this->existsByNumeroYSede($data['num_sala'], $data['id_sede'], $id)) {
                return [
                    "success" => false,
                    "message" => "Ya existe otra sala con ese número en esta sede"
                ];
            }

            $sql = "UPDATE sala SET num_sala = :num_sala, id_sede = :id_sede, estado = :estado 
                    WHERE id_sala = :id_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":num_sala", $data['num_sala'], PDO::PARAM_INT);
            $stmt->bindParam(":id_sede", $data['id_sede'], PDO::PARAM_INT);
            $stmt->bindParam(":estado", $data['estado'], PDO::PARAM_INT);
            $stmt->bindParam(":id_sala", $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ["success" => true, "message" => "Sala actualizada exitosamente"];
            }
            
            return ["success" => false, "message" => "Error al actualizar la sala"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Eliminar sala (verificar dependencias)
    public function delete($id) {
        try {
            // Verificar si tiene asientos
            $sqlAsientos = "SELECT COUNT(*) FROM asiento WHERE id_sala = :id_sala";
            $stmtAsientos = $this->conn->prepare($sqlAsientos);
            $stmtAsientos->bindParam(":id_sala", $id, PDO::PARAM_INT);
            $stmtAsientos->execute();
            
            if ($stmtAsientos->fetchColumn() > 0) {
                return [
                    "success" => false,
                    "message" => "No se puede eliminar. La sala tiene asientos registrados"
                ];
            }

            // Verificar si tiene funciones
            $sqlFunciones = "SELECT COUNT(*) FROM funcion WHERE id_sala = :id_sala";
            $stmtFunciones = $this->conn->prepare($sqlFunciones);
            $stmtFunciones->bindParam(":id_sala", $id, PDO::PARAM_INT);
            $stmtFunciones->execute();
            
            if ($stmtFunciones->fetchColumn() > 0) {
                return [
                    "success" => false,
                    "message" => "No se puede eliminar. La sala tiene funciones registradas"
                ];
            }

            // Si no tiene dependencias, eliminar
            $sql = "DELETE FROM sala WHERE id_sala = :id_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id_sala", $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ["success" => true, "message" => "Sala eliminada exitosamente"];
            }
            
            return ["success" => false, "message" => "Error al eliminar la sala"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Cambiar estado
    public function toggleEstado($id, $estado) {
        try {
            $sql = "UPDATE sala SET estado = :estado WHERE id_sala = :id_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":estado", $estado, PDO::PARAM_INT);
            $stmt->bindParam(":id_sala", $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $mensaje = $estado == 1 ? "Sala activada exitosamente" : "Sala desactivada exitosamente";
                return ["success" => true, "message" => $mensaje];
            }
            
            return ["success" => false, "message" => "Error al cambiar el estado"];
        } catch (PDOException $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // Contar salas
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) FROM sala";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Obtener salas por sede
    public function getBySede($idSede) {
        try {
            $sql = "SELECT s.*, sd.nombre as sede_nombre 
                    FROM sala s
                    INNER JOIN sede sd ON s.id_sede = sd.id_sede
                    WHERE s.id_sede = :id_sede
                    ORDER BY s.num_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_sede" => $idSede]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
