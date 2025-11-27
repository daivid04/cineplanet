<?php

class SedeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function validarSede($data, $modo = "insertar") {
        // Campos obligatorios para insertar
        $requeridos = ["nombre", "id_ciudad"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        // Validar que id_ciudad sea numérico
        if (isset($data["id_ciudad"]) && !is_numeric($data["id_ciudad"])) {
            throw new Exception("ID de ciudad inválido.");
        }

        return true;
    }

    // -------------------------------------------------
    // CREAR SEDE
    // -------------------------------------------------
    public function create($data) {
        $this->validarSede($data, "insertar");

        try {
            $sql = "INSERT INTO sede (nombre, id_ciudad, estado) 
                    VALUES (:nombre, :id_ciudad, :estado)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"],
                ":id_ciudad" => $data["id_ciudad"],
                ":estado" => isset($data["estado"]) ? $data["estado"] : 1
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear la sede: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER SEDE POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            $sql = "SELECT 
                        s.id_sede,
                        s.nombre,
                        s.id_ciudad,
                        s.estado,
                        c.nombre AS ciudad_nombre
                    FROM sede s
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    WHERE s.id_sede = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la sede: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS SEDES
    // -------------------------------------------------
    public function getAll() {
        try {
            $sql = "SELECT 
                        s.id_sede,
                        s.nombre,
                        s.id_ciudad,
                        s.estado,
                        c.nombre AS ciudad_nombre
                    FROM sede s
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    ORDER BY s.nombre ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las sedes: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR SEDE
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $this->validarSede($data, "actualizar");

        try {
            // Construir la consulta dinámicamente según los campos proporcionados
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
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
                throw new Exception("No se proporcionaron campos para actualizar.");
            }

            $sql = "UPDATE sede SET " . implode(", ", $campos) . " WHERE id_sede = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la sede: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ELIMINAR SEDE
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            // Primero verificamos si tiene salas asociadas para evitar errores de integridad referencial
            // O podríamos hacer un soft delete cambiando el estado a 0
            
            // Opción 1: Soft Delete (Recomendado)
            $sql = "UPDATE sede SET estado = 0 WHERE id_sede = :id";
            
            // Opción 2: Hard Delete (Si se prefiere borrar físicamente)
            // $sql = "DELETE FROM sede WHERE id_sede = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la sede: " . $e->getMessage());
        }
    }
}
?>
