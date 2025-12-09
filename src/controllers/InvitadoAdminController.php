<?php
/**
 * Controlador de administración para Invitados
 */
require_once __DIR__ . "/../models/InvitadoAdminModel.php";
require_once __DIR__ . "/../services/conexion.php";

class InvitadoAdminController {
    private $model;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->model = new InvitadoAdminModel($this->conn);
    }

    // Obtener todos los invitados
    public function getAll($soloActivos = false, $busqueda = null) {
        return $this->model->getAll($soloActivos, $busqueda);
    }

    // Obtener invitado por ID
    public function getById($id) {
        return $this->model->getById($id);
    }

    // Crear invitado
    public function create($data) {
        return $this->model->create($data);
    }

    // Actualizar invitado
    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    // Toggle estado
    public function toggleEstado($id) {
        return $this->model->toggleEstado($id);
    }

    // Eliminar invitado
    public function delete($id) {
        return $this->model->delete($id);
    }

    // Contar compras
    public function countCompras($id) {
        return $this->model->countCompras($id);
    }

    // Info de compras
    public function getComprasInfo($id) {
        return $this->model->getComprasInfo($id);
    }

    // Estadísticas
    public function getEstadisticas() {
        return $this->model->getEstadisticas();
    }

    // Convertir a socio
    public function convertirASocio($id, $datosSocio) {
        return $this->model->convertirASocio($id, $datosSocio);
    }
}
