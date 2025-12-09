<?php
require_once __DIR__ . '/../models/ProductoSedeModel.php';

class ProductoSedeController {
    private $model;

    public function __construct($conn) {
        $this->model = new ProductoSedeModel($conn);
    }

    // Crear
    public function create($data) {
        return $this->model->create($data);
    }

    // Obtener por ID
    public function getById($id) {
        return $this->model->getById($id);
    }

    // Obtener todos
    public function getAll($idSede = null, $idProducto = null, $soloConStock = false) {
        return $this->model->getAll($idSede, $idProducto, $soloConStock);
    }

    // Obtener por sede
    public function getBySede($idSede) {
        return $this->model->getBySede($idSede);
    }

    // Obtener por producto
    public function getByProducto($idProducto) {
        return $this->model->getByProducto($idProducto);
    }

    // Actualizar stock
    public function updateStock($id, $nuevoStock) {
        return $this->model->updateStock($id, $nuevoStock);
    }

    // Incrementar stock
    public function incrementarStock($id, $cantidad) {
        return $this->model->incrementarStock($id, $cantidad);
    }

    // Decrementar stock
    public function decrementarStock($id, $cantidad) {
        return $this->model->decrementarStock($id, $cantidad);
    }

    // Actualizar general
    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    // Eliminar
    public function delete($id) {
        return $this->model->delete($id);
    }

    // Estadísticas por sede
    public function getEstadisticasBySede($idSede) {
        return $this->model->getEstadisticasBySede($idSede);
    }

    // Productos sin stock
    public function getProductosSinStock($idSede = null) {
        return $this->model->getProductosSinStock($idSede);
    }

    // Productos bajo stock
    public function getProductosBajoStock($umbral = 10, $idSede = null) {
        return $this->model->getProductosBajoStock($umbral, $idSede);
    }

    // Asignar a múltiples sedes
    public function asignarAMultiplesSedes($idProducto, $sedes, $stockInicial = 0) {
        return $this->model->asignarAMúltiplesSedes($idProducto, $sedes, $stockInicial);
    }

    // Contar
    public function count($idSede = null) {
        return $this->model->count($idSede);
    }

    // Verificar dependencias
    public function countEnCombos($id) {
        return $this->model->countEnCombos($id);
    }

    public function countEnCompras($id) {
        return $this->model->countEnCompras($id);
    }
}
