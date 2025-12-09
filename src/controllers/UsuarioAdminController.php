<?php
require_once __DIR__ . '/../models/UsuarioAdminModel.php';

class UsuarioAdminController {
    private $model;

    public function __construct($conn) {
        $this->model = new UsuarioAdminModel($conn);
    }

    // Crear usuario
    public function create($data) {
        return $this->model->create($data);
    }

    // Obtener por ID
    public function getById($id) {
        return $this->model->getById($id);
    }

    // Obtener todos
    public function getAll($soloActivos = false, $filtroTipo = null) {
        return $this->model->getAll($soloActivos, $filtroTipo);
    }

    // Actualizar
    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    // Eliminar
    public function delete($id) {
        return $this->model->delete($id);
    }

    // Cambiar estado
    public function toggleEstado($id, $estado) {
        return $this->model->toggleEstado($id, $estado);
    }

    // Contar
    public function count($soloActivos = false, $filtroTipo = null) {
        return $this->model->count($soloActivos, $filtroTipo);
    }

    // Buscar
    public function search($termino) {
        return $this->model->search($termino);
    }

    // Obtener detalles completos
    public function getDetallesCompletos($id) {
        return $this->model->getDetallesCompletos($id);
    }

    // Verificar dependencias
    public function countCompras($id) {
        return $this->model->countCompras($id);
    }

    public function esSocio($id) {
        return $this->model->esSocio($id);
    }

    public function esInvitado($id) {
        return $this->model->esInvitado($id);
    }

    public function getTipoUsuario($id) {
        return $this->model->getTipoUsuario($id);
    }
}
