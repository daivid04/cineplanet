<?php
require_once __DIR__ . "/../models/pelicula_model.php";

class PeliculaController {
    private $peliculaModel;

    public function __construct($conn) {
        $this->peliculaModel = new PeliculaModel($conn);
    }

    // -------------------------------------------------
    // CREAR PELÍCULA
    // -------------------------------------------------
    public function create($data) {
        try {
            $id = $this->peliculaModel->create($data);
            
            return [
                "success" => true,
                "message" => "Película creada exitosamente",
                "id_pelicula" => $id
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER PELÍCULA POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $pelicula = $this->peliculaModel->getById($id);
            
            if (!$pelicula) {
                return [
                    "success" => false,
                    "message" => "Película no encontrada"
                ];
            }

            return [
                "success" => true,
                "data" => $pelicula
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS PELÍCULAS
    // -------------------------------------------------
    public function getAll() {
        try {
            $peliculas = $this->peliculaModel->getAll();

            return [
                "success" => true,
                "data" => $peliculas,
                "total" => count($peliculas)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // BUSCAR PELÍCULAS POR NOMBRE
    // -------------------------------------------------
    public function searchByName($name) {
        try {
            if (empty($name)) {
                return [
                    "success" => false,
                    "message" => "El nombre es requerido para la búsqueda"
                ];
            }
            
            $peliculas = $this->peliculaModel->getByName($name);

            return [
                "success" => true,
                "data" => $peliculas,
                "total" => count($peliculas)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER PELÍCULAS ACTIVAS
    // -------------------------------------------------
    public function getActivas() {
        try {
            $peliculas = $this->peliculaModel->getActivas();

            return [
                "success" => true,
                "data" => $peliculas,
                "total" => count($peliculas)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR PELÍCULA
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $resultado = $this->peliculaModel->update($id, $data);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Película actualizada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo actualizar la película o no se encontró"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ELIMINAR PELÍCULA
    // -------------------------------------------------
    public function delete($id) {
        try {
            $resultado = $this->peliculaModel->delete($id);

            if ($resultado) {
                return [
                    "success" => true,
                    "message" => "Película eliminada exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo eliminar la película o no se encontró"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO DE PELÍCULA
    // -------------------------------------------------
    public function cambiarEstado($id, $estado) {
        try {
            $resultado = $this->peliculaModel->cambiarEstado($id, $estado);

            if ($resultado) {
                $estadoTexto = $estado == 1 ? "activada" : "desactivada";
                return [
                    "success" => true,
                    "message" => "Película {$estadoTexto} exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se pudo cambiar el estado de la película o no se encontró"
                ];
            }
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
?>
