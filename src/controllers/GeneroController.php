<?php
require_once __DIR__ . "/../models/GeneroModel.php";

class GeneroController {
    private $generoModel;

    public function __construct($conn) {
        $this->generoModel = new GeneroModel($conn);
    }

    // -------------------------------------------------
    // CREAR GÉNERO
    // -------------------------------------------------
    public function create($data) {
        try {
            $result = $this->generoModel->create($data);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER GÉNERO POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $genero = $this->generoModel->getById($id);
            
            if (!$genero) {
                return [
                    "success" => false,
                    "message" => "Género no encontrado"
                ];
            }

            return [
                "success" => true,
                "data" => $genero
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS GÉNEROS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $generos = $this->generoModel->getAll($soloActivos);

            return [
                "success" => true,
                "data" => $generos,
                "total" => count($generos)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR GÉNERO
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $result = $this->generoModel->update($id, $data);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // TOGGLE ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        try {
            $result = $this->generoModel->toggleEstado($id, $estado);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ELIMINAR GÉNERO
    // -------------------------------------------------
    public function delete($id) {
        try {
            $result = $this->generoModel->delete($id);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CONTAR GÉNEROS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        return $this->generoModel->count($soloActivos);
    }
}
