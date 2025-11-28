<?php
class UsuarioModel {
    protected $conn;
    protected $correo;
    protected $id;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES BÁSICAS PARA TODOS LOS USUARIOS
    // -------------------------------------------------
    protected function validarBasico($data, $modo = "insertar") {
        
        // Validar correo si está presente
        if (isset($data["correo"]) && !filter_var($data["correo"], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo electrónico inválido.");
        }

        // Validar que no esté vacío en modo insertar
        if ($modo === "insertar" && isset($data["correo"]) && trim($data["correo"]) === "") {
            throw new Exception("El campo 'correo' es obligatorio.");
        }

        return true;
    }

    // -------------------------------------------------
    // GETTERS Y SETTERS
    // -------------------------------------------------
    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    // -------------------------------------------------
    // MÉTODOS COMUNES (pueden ser sobrescritos)
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }
        
        // Este método debe ser implementado en las clases hijas
        throw new Exception("Método getById debe ser implementado en la clase hija");
    }

    public function getAll() {
        // Este método debe ser implementado en las clases hijas
        throw new Exception("Método getAll debe ser implementado en la clase hija");
    }
}
?>