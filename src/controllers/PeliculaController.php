<?php
require_once __DIR__ . "/../models/PeliculaModel.php";

class PeliculaController {
    private $peliculaModel;

    public function __construct($conn) {
        $this->peliculaModel = new PeliculaModel($conn);
    }

    public function create($data) {
        try {
            return $this->peliculaModel->create($data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getById($id) {
        try {
            $pelicula = $this->peliculaModel->getById($id);
            
            if (!$pelicula) {
                return ["success" => false, "message" => "Pelicula no encontrada"];
            }

            return ["success" => true, "data" => $pelicula];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function getAll($soloActivos = false) {
        try {
            $peliculas = $this->peliculaModel->getAll($soloActivos);
            return ["success" => true, "data" => $peliculas, "total" => count($peliculas)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function search($query) {
        try {
            $peliculas = $this->peliculaModel->getByName($query);
            return ["success" => true, "data" => $peliculas, "total" => count($peliculas)];
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function update($id, $data) {
        try {
            return $this->peliculaModel->update($id, $data);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function delete($id) {
        try {
            return $this->peliculaModel->delete($id);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function toggleEstado($id, $estado) {
        try {
            return $this->peliculaModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return ["success" => false, "message" => $e->getMessage()];
        }
    }

    public function count($soloActivos = false) {
        return $this->peliculaModel->count($soloActivos);
    }
}