<?php
require_once __DIR__ . "/../models/socio_model.php";

class SocioController {

    private $model;

    public function __construct($conn) {
        $this->model = new SocioModel($conn);
    }

    public function get() {
        return $this->model->get();
    }

    public function getId($id) {
        return $this->model->getById($id);
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
