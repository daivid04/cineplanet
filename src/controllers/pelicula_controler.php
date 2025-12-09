<?php
require_once __DIR__ . "/../models/pelicula_model.php";

class PeliculaController {
    private $peliculaModel;

    public function __construct($conn) {
        $this->peliculaModel = new PeliculaModel($conn);
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS PELÍCULAS
    // -------------------------------------------------
    public function getAll() {
        try {
            return $this->peliculaModel->getAll();
        } catch (Exception $e) {
            throw new Exception("Error al obtener películas: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER PELÍCULA POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("ID inválido.");
            }
            return $this->peliculaModel->getById($id);
        } catch (Exception $e) {
            throw new Exception("Error al obtener película: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // BUSCAR PELÍCULAS POR NOMBRE
    // -------------------------------------------------
    public function searchByName($name) {
        try {
            if (empty($name)) {
                throw new Exception("El nombre es requerido para la búsqueda.");
            }
            return $this->peliculaModel->getByName($name);
        } catch (Exception $e) {
            throw new Exception("Error al buscar películas: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // CREAR UNA NUEVA PELÍCULA
    // -------------------------------------------------
    public function create($data) {
        try {
            return $this->peliculaModel->create($data);
        } catch (Exception $e) {
            throw new Exception("Error al crear película: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR UNA PELÍCULA
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("ID inválido.");
            }
            return $this->peliculaModel->update($id, $data);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar película: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ELIMINAR UNA PELÍCULA
    // -------------------------------------------------
    public function delete($id) {
        try {
            if (!is_numeric($id)) {
                throw new Exception("ID inválido.");
            }
            return $this->peliculaModel->delete($id);
        } catch (Exception $e) {
            throw new Exception("Error al eliminar película: " . $e->getMessage());
        }
    }

    public function getCartelera($limite){
      try{
        $limit = (int)$limite; 
        if(!is_int($limit) ){
          throw new Exception("Limite inválido.");
        }
        return $this->peliculaModel->getCartelera($limit);
      } catch(Exception $error) {
        throw new Exception("Error en obtención de peliculas: " . $error->getMessage());
      }
    }
}
?>