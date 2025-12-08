<?php
require_once __DIR__ . "/../models/TipoSocioModel.php";

class TipoSocioController {
    private $tipoSocioModel;

    public function __construct($conn) {
        $this->tipoSocioModel = new TipoSocioModel($conn);
    }

    public function create($data) {
        try {
            return $this->tipoSocioModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $tipoSocio = $this->tipoSocioModel->getById($id);
            
            if (!$tipoSocio) {
                return ["success" => false, "message" => "Tipo de socio no encontrado"];
            }

            return ["success" => true, "data" => $tipoSocio];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $tiposSocio = $this->tipoSocioModel->getAll($soloActivos);
            return ["success" => true, "data" => $tiposSocio, "total" => count($tiposSocio)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->tipoSocioModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->tipoSocioModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->tipoSocioModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->tipoSocioModel->count($soloActivos);
    }
}
?>
