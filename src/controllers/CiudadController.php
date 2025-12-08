<?php
require_once __DIR__ . "/../models/CiudadModel.php";

class CiudadController {
    private $ciudadModel;

    public function __construct($conn) {
        $this->ciudadModel = new CiudadModel($conn);
    }

    // -------------------------------------------------
    // CREAR CIUDAD
    // -------------------------------------------------
    public function create($data) {
        try {
            $result = $this->ciudadModel->create($data);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER CIUDAD POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $ciudad = $this->ciudadModel->getById($id);
            
            if (!$ciudad) {
                return [
                    "success" => false,
                    "message" => "Ciudad no encontrada"
                ];
            }

            return [
                "success" => true,
                "data" => $ciudad
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS CIUDADES
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $ciudades = $this->ciudadModel->getAll($soloActivos);

            return [
                "success" => true,
                "data" => $ciudades,
                "total" => count($ciudades)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR CIUDAD
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $result = $this->ciudadModel->update($id, $data);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ELIMINAR CIUDAD (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        try {
            $result = $this->ciudadModel->delete($id);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        try {
            $result = $this->ciudadModel->toggleEstado($id, $estado);
            return $result;
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CONTAR CIUDADES
    // -------------------------------------------------
    public function count($soloActivos = false) {
        return $this->ciudadModel->count($soloActivos);
    }
}
?>
