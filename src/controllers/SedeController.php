<?php
require_once __DIR__ . "/../models/SedeModel.php";

class SedeController {
    private $sedeModel;

    public function __construct($conn) {
        $this->sedeModel = new SedeModel($conn);
    }

    // -------------------------------------------------
    // CREAR SEDE
    // -------------------------------------------------
    public function create($data) {
        try {
            $id = $this->sedeModel->create($data);
            
            return [
                "success" => true,
                "message" => "Sede creada exitosamente",
                "id_sede" => $id
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER SEDE POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $sede = $this->sedeModel->getById($id);
            
            if (!$sede) {
                return [
                    "success" => false,
                    "message" => "Sede no encontrada"
                ];
            }

            return [
                "success" => true,
                "data" => $sede
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS SEDES
    // -------------------------------------------------
    public function getAll() {
        try {
            $sedes = $this->sedeModel->getAll();

            return [
                "success" => true,
                "data" => $sedes,
                "total" => count($sedes)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR SEDE
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $resultado = $this->sedeModel->update($id, $data);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Sede actualizada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo actualizar la sede o no se encontró"
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
    // ELIMINAR SEDE
    // -------------------------------------------------
    public function delete($id) {
        try {
            $resultado = $this->sedeModel->delete($id);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Sede eliminada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo eliminar la sede o no se encontró"
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
