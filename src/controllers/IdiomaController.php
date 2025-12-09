<?php
require_once __DIR__ . "/../models/IdiomaModel.php";

class IdiomaController {
    private $idiomaModel;

    public function __construct($conn) {
        $this->idiomaModel = new IdiomaModel($conn);
    }

    // -------------------------------------------------
    // CREAR IDIOMA
    // -------------------------------------------------
    public function create($data) {
        try {
            return $this->idiomaModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER IDIOMA POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $idioma = $this->idiomaModel->getById($id);
            
            if (!$idioma) {
                return ["success" => false, "message" => "Idioma no encontrado"];
            }

            return ["success" => true, "data" => $idioma];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS IDIOMAS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $idiomas = $this->idiomaModel->getAll($soloActivos);
            return ["success" => true, "data" => $idiomas, "total" => count($idiomas)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR IDIOMA
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            return $this->idiomaModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR IDIOMA (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        try {
            return $this->idiomaModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        try {
            return $this->idiomaModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR IDIOMAS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        return $this->idiomaModel->count($soloActivos);
    }
}
?>
