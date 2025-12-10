<?php
require_once __DIR__ . "/../models/MetodoModel.php";

class MetodoController {
    private $metodoModel;

    public function __construct($conn) {
        $this->metodoModel = new MetodoModel($conn);
    }

    public function create($data) {
        try {
            return $this->metodoModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $metodo = $this->metodoModel->getById($id);
            
            if (!$metodo) {
                return ["success" => false, "message" => "Metodo no encontrado"];
            }

            return ["success" => true, "data" => $metodo];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $metodos = $this->metodoModel->getAll($soloActivos);
            return ["success" => true, "data" => $metodos, "total" => count($metodos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->metodoModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->metodoModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->metodoModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->metodoModel->count($soloActivos);
    }
}
?>
