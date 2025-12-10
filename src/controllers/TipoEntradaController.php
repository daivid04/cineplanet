<?php
require_once __DIR__ . "/../models/TipoEntradaModel.php";

class TipoEntradaController {
    private $model;

    public function __construct($conn) {
        $this->model = new TipoEntradaModel($conn);
    }

    public function getAll($soloActivos = true) {
        try {
            $tipos = $this->model->getAll($soloActivos);
            return ["success" => true, "data" => $tipos, "total" => count($tipos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $tipo = $this->model->getById($id);
            if (!$tipo) {
                return ["success" => false, "message" => "Tipo de entrada no encontrado"];
            }
            return ["success" => true, "data" => $tipo];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getByCategoria($categoria) {
        try {
            $tipos = $this->model->getByCategoria($categoria);
            return ["success" => true, "data" => $tipos];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    public function toggleEstado($id, $estado) {
        return $this->model->toggleEstado($id, $estado);
    }
}
