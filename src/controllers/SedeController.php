<?php
require_once __DIR__ . "/../models/SedeModel.php";

class SedeController {
    private $sedeModel;

    public function __construct($conn) {
        $this->sedeModel = new SedeModel($conn);
    }

    public function create($data) {
        try {
            return $this->sedeModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $sede = $this->sedeModel->getById($id);
            
            if (!$sede) {
                return ["success" => false, "message" => "Sede no encontrada"];
            }

            return ["success" => true, "data" => $sede];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $sedes = $this->sedeModel->getAll($soloActivos);
            return ["success" => true, "data" => $sedes, "total" => count($sedes)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->sedeModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->sedeModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->sedeModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->sedeModel->count($soloActivos);
    }

    public function getByCiudad($idCiudad) {
        try {
            $sedes = $this->sedeModel->getByCiudad($idCiudad);
            return ["success" => true, "data" => $sedes];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }
}
