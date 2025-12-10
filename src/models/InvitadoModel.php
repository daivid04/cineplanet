<?php
require_once __DIR__ . "/UsuarioModel.php";

class InvitadoModel extends UsuarioModel {

    // -------------------------------------------------
    // VALIDAR DATOS ESPECÍFICOS DE INVITADO
    // -------------------------------------------------
    private function validarInvitado($data, $modo = "insertar") {
        
        // Campos obligatorios para insertar
        $requeridos = ["nombre"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        // Validar que el nombre no esté vacío en actualizar
        if (isset($data["nombre"]) && trim($data["nombre"]) === "") {
            throw new Exception("El campo 'nombre' no puede estar vacío.");
        }

        return true;
    }

    // -------------------------------------------------
    // INSERTAR INVITADO
    // -------------------------------------------------
    public function create($data) {
        $this->validarBasico($data, "insertar");
        $this->validarInvitado($data, "insertar");

        $sql = "INSERT INTO invitado (nombre, correo) VALUES (:nombre, :correo)";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt->execute([
            ":nombre" => trim($data["nombre"]),
            ":correo" => $data["correo"] ?? null
        ])) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al insertar el invitado: " . implode(", ", $errorInfo));
        }

        return true;
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS INVITADOS
    // -------------------------------------------------
    public function getAll() {
        $sql = "SELECT * FROM invitado ORDER BY id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER INVITADO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $sql = "SELECT * FROM invitado WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // ACTUALIZAR INVITADO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $this->validarBasico($data, "actualizar");
        $this->validarInvitado($data, "actualizar");

        $sql = "UPDATE invitado SET nombre = :nombre, correo = :correo WHERE id = :id";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":nombre" => trim($data["nombre"]),
            ":correo" => $data["correo"] ?? null,
            ":id" => $id
        ]);
    }

    // -------------------------------------------------
    // ELIMINAR INVITADO
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $sql = "DELETE FROM invitado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>