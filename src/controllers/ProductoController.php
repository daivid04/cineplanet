<?php
require_once __DIR__ . "/../models/ProductoModel.php";

class ProductoController {
    private $productoModel;

    public function __construct($conn) {
        $this->productoModel = new ProductoModel($conn);
    }

    public function create($data) {
        try {
            return $this->productoModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $producto = $this->productoModel->getById($id);
            
            if (!$producto) {
                return ["success" => false, "message" => "Producto no encontrado"];
            }

            return ["success" => true, "data" => $producto];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $productos = $this->productoModel->getAll($soloActivos);
            return ["success" => true, "data" => $productos, "total" => count($productos)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->productoModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->productoModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->productoModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->productoModel->count($soloActivos);
    }
}
?>
