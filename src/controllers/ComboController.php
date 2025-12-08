<?php
require_once __DIR__ . "/../models/ComboModel.php";

class ComboController {
    private $comboModel;

    public function __construct($conn) {
        $this->comboModel = new ComboModel($conn);
    }

    public function create($data) {
        try {
            return $this->comboModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $combo = $this->comboModel->getById($id);
            
            if (!$combo) {
                return ["success" => false, "message" => "Combo no encontrado"];
            }

            return ["success" => true, "data" => $combo];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $combos = $this->comboModel->getAll($soloActivos);
            return ["success" => true, "data" => $combos, "total" => count($combos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->comboModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->comboModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->comboModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->comboModel->count($soloActivos);
    }
}
?>
