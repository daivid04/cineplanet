<?php
require_once __DIR__ . "/../models/invitado_model.php";

class InvitadoController {
    private $model;

    public function __construct($conn) {
        $this->model = new InvitadoModel($conn);
    }

    public function get() {
        return $this->model->get();
    }

    public function getId($id) {
        return $this->model->getId($id);
    }

    public function create($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        return $this->model->update($id, $data);
    }

    public function delete($id) {
        return $this->model->delete($id);
    }
}
?>