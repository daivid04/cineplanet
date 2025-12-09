<?php
require_once __DIR__ . "/../models/SalaModel.php";

class SalaController {
    private $salaModel;

    public function __construct($conn) {
        $this->salaModel = new SalaModel($conn);
    }

    public function create($data) {
        try {
            return $this->salaModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $sala = $this->salaModel->getById($id);
            
            if (!$sala) {
                return ["success" => false, "message" => "Sala no encontrada"];
            }

            return ["success" => true, "data" => $sala];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $salas = $this->salaModel->getAll($soloActivos);
            return ["success" => true, "data" => $salas, "total" => count($salas)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getBySede($idSede, $soloActivos = false) {
        try {
            $salas = $this->salaModel->getBySede($idSede, $soloActivos);
            return ["success" => true, "data" => $salas, "total" => count($salas)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->salaModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->salaModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->salaModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->salaModel->count($soloActivos);
    }

    public function countBySede($idSede, $soloActivos = false) {
        return $this->salaModel->countBySede($idSede, $soloActivos);
    }
}
