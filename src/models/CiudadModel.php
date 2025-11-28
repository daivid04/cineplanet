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
        // Campos obligatorios para insertar
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

    // -------------------------------------------------
    // CREAR CIUDAD
    // -------------------------------------------------
    public function create($data) {
        $this->validarCiudad($data, "insertar");

        try {
            $sql = "INSERT INTO ciudad (nombre) VALUES (:nombre)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"]
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear la ciudad: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER CIUDAD POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            $sql = "SELECT id_ciudad, nombre FROM ciudad WHERE id_ciudad = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la ciudad: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS CIUDADES
    // -------------------------------------------------
    public function getAll() {
        try {
            $sql = "SELECT id_ciudad, nombre FROM ciudad ORDER BY nombre ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las ciudades: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR CIUDAD
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $this->validarCiudad($data, "actualizar");

        try {
            // Construir la consulta dinámicamente según los campos proporcionados
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) {
                $campos[] = "nombre = :nombre";
                $params[":nombre"] = $data["nombre"];
            }

            if (empty($campos)) {
                throw new Exception("No se proporcionaron campos para actualizar.");
            }

            $sql = "UPDATE ciudad SET " . implode(", ", $campos) . " WHERE id_ciudad = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la ciudad: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ELIMINAR CIUDAD
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            // Verificar si hay sedes asociadas antes de eliminar
            $sqlCheck = "SELECT COUNT(*) FROM sede WHERE id_ciudad = :id";
            $stmtCheck = $this->conn->prepare($sqlCheck);
            $stmtCheck->execute([":id" => $id]);
            
            if ($stmtCheck->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar la ciudad porque tiene sedes asociadas.");
            }

            $sql = "DELETE FROM ciudad WHERE id_ciudad = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la ciudad: " . $e->getMessage());
        }
    }
}
?>
