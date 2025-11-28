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
            $id = $this->ciudadModel->create($data);
            
            return [
                "success" => true,
                "message" => "Ciudad creada exitosamente",
                "id_ciudad" => $id
            ];
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
    public function getAll() {
        try {
            $ciudades = $this->ciudadModel->getAll();

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
            $resultado = $this->ciudadModel->update($id, $data);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Ciudad actualizada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo actualizar la ciudad o no se encontró"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ELIMINAR CIUDAD
    // -------------------------------------------------
    public function delete($id) {
        try {
            $resultado = $this->ciudadModel->delete($id);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Ciudad eliminada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo eliminar la ciudad o no se encontró"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
?>
