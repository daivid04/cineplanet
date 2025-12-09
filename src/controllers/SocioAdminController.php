<?php
/**
 * Controlador de administración para Socios
 */
require_once __DIR__ . "/../models/SocioAdminModel.php";
require_once __DIR__ . "/../services/conexion.php";

class SocioAdminController {
    private $model;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        $this->model = new SocioAdminModel($this->conn);
    }

    // Obtener todos los socios
    public function getAll($soloActivos = false, $filtroTipo = null, $busqueda = null) {
        return $this->model->getAll($soloActivos, $filtroTipo, $busqueda);
    }

    // Obtener socio por ID
    public function getById($id) {
        return $this->model->getById($id);
    }

    // Crear socio
    public function create($data) {
        return $this->model->create($data);
    }

    // Actualizar socio
    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    // Toggle estado
    public function toggleEstado($id) {
        return $this->model->toggleEstado($id);
    }

    // Eliminar socio
    public function delete($id) {
        return $this->model->delete($id);
    }

    // Reset contraseña
    public function resetPassword($id, $nuevaContrasena) {
        return $this->model->resetPassword($id, $nuevaContrasena);
    }

    // Obtener tipos de socio
    public function getTiposSocio($soloActivos = true) {
        return $this->model->getTiposSocio($soloActivos);
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
}
