<?php
require_once __DIR__ . "/../models/AsientoModel.php";

class AsientoController {
    private $asientoModel;

    public function __construct($conn) {
        $this->asientoModel = new AsientoModel($conn);
    }

    public function generarAsientos($idSala, $filas, $columnas) {
        try {
            return $this->asientoModel->generarAsientos($idSala, $filas, $columnas);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function create($data) {
        try {
            return $this->asientoModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $asiento = $this->asientoModel->getById($id);
            
            if (!$asiento) {
                return ["success" => false, "message" => "Asiento no encontrado"];
            }

            return ["success" => true, "data" => $asiento];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $asientos = $this->asientoModel->getAll($soloActivos);
            return ["success" => true, "data" => $asientos, "total" => count($asientos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getBySala($idSala, $soloActivos = false) {
        try {
            $asientos = $this->asientoModel->getBySala($idSala, $soloActivos);
            return ["success" => true, "data" => $asientos, "total" => count($asientos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getMapaBySala($idSala) {
        try {
            $resultado = $this->asientoModel->getMapaBySala($idSala);
            
            // Si el modelo ya retorna success=false, devolverlo tal cual
            if (isset($resultado['success']) && !$resultado['success']) {
                return $resultado;
            }
            
            // Si tiene éxito, envolver en formato estándar
            return [
                "success" => true,
                "data" => $resultado
            ];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->asientoModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->asientoModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function deleteAllBySala($idSala) {
        try {
            return $this->asientoModel->deleteAllBySala($idSala);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->asientoModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->asientoModel->count($soloActivos);
    }

    public function countBySala($idSala, $soloActivos = false) {
        return $this->asientoModel->countBySala($idSala, $soloActivos);
    }
}
