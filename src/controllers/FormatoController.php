<?php
require_once __DIR__ . "/../models/FormatoModel.php";

class FormatoController {
    private $formatoModel;

    public function __construct($conn) {
        $this->formatoModel = new FormatoModel($conn);
    }

    public function create($data) {
        try {
            return $this->formatoModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $formato = $this->formatoModel->getById($id);
            
            if (!$formato) {
                return ["success" => false, "message" => "Formato no encontrado"];
            }

            return ["success" => true, "data" => $formato];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $formatos = $this->formatoModel->getAll($soloActivos);
            return ["success" => true, "data" => $formatos, "total" => count($formatos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->formatoModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->formatoModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->formatoModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->formatoModel->count($soloActivos);
    }
}
?>
