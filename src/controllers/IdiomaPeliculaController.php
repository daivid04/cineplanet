<?php 

  require_once __DIR__ . '/../models/IdiomaPeliculaModel.php';
  class IdiomaPeliculaControler {
    private $model;
    
    private function __construct($conn)
    {
      $this->model = new IdiomasPeliculaModel($conn);
    }
    public function getById ($id) {
      return $this->model->getById($id);
    }

    public function create ($data) {
      //return $this->model->create($data);
    }

    public function update ($id, $data) {
      //return $this->model->update($id,$data);
    }

    public function delete ($id) {  
      //return $this->model->delete($id);
    }
  }
?>