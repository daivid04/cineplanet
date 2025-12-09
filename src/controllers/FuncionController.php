<?php
require_once __DIR__ . "/../models/FuncionModel.php";

class FuncionController {
    private $funcionModel;

    public function __construct($conn) {
        $this->funcionModel = new FuncionModel($conn);
    }

    // -------------------------------------------------
    // CREAR FUNCIÓN
    // -------------------------------------------------
    public function create($data) {
        try {
            $resultado = $this->funcionModel->create($data);
            return $resultado; // El modelo ya retorna el formato correcto
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIÓN POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $funcion = $this->funcionModel->getById($id);
            
            if (!$funcion) {
                return [
                    "success" => false,
                    "message" => "Función no encontrada"
                ];
            }

            return [
                "success" => true,
                "data" => $funcion
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS FUNCIONES
    // -------------------------------------------------
    public function getAll($soloActivos = false, $soloFuturas = false) {
        try {
            $funciones = $this->funcionModel->getAll($soloActivos, $soloFuturas);

            return [
                "success" => true,
                "data" => $funciones,
                "total" => count($funciones)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR PELÍCULA
    // -------------------------------------------------
    public function getByPelicula($id_pelicula) {
        try {
            $funciones = $this->funcionModel->getByPelicula($id_pelicula);

            return [
                "success" => true,
                "data" => $funciones,
                "total" => count($funciones)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR SEDE
    // -------------------------------------------------
    public function getBySede($id_sede) {
        try {
            $funciones = $this->funcionModel->getBySede($id_sede);

            return [
                "success" => true,
                "data" => $funciones,
                "total" => count($funciones)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR FECHA
    // -------------------------------------------------
    public function getByFecha($fecha) {
        try {
            $funciones = $this->funcionModel->getByFecha($fecha);

            return [
                "success" => true,
                "data" => $funciones,
                "total" => count($funciones)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES CON FILTROS
    // -------------------------------------------------
    public function getByFilters($filters) {
        try {
            $funciones = $this->funcionModel->getByFilters($filters);

            return [
                "success" => true,
                "data" => $funciones,
                "total" => count($funciones),
                "filters_applied" => $filters
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR FUNCIÓN
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $resultado = $this->funcionModel->update($id, $data);
            return $resultado; // El modelo ya retorna el formato correcto
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ELIMINAR FUNCIÓN
    // -------------------------------------------------
    public function delete($id) {
        try {
            $resultado = $this->funcionModel->delete($id);
            return $resultado; // El modelo ya retorna el formato correcto
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO (TOGGLE)
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        try {
            return $this->funcionModel->toggleEstado($id, $estado);
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // CONTAR FUNCIONES
    // -------------------------------------------------
    public function count($soloActivos = false, $soloFuturas = false) {
        return $this->funcionModel->count($soloActivos, $soloFuturas);
    }

    // -------------------------------------------------
    // VERIFICAR DISPONIBILIDAD DE SALA
    // -------------------------------------------------
    public function verificarDisponibilidadSala($id_sala, $fecha, $hora, $duracion_pelicula, $id_funcion_excluir = null) {
        try {
            $disponible = $this->funcionModel->verificarDisponibilidadSala(
                $id_sala,
                $fecha,
                $hora,
                $duracion_pelicula,
                $id_funcion_excluir
            );

            return [
                "success" => true,
                "disponible" => $disponible,
                "message" => $disponible 
                    ? "La sala está disponible para el horario solicitado" 
                    : "La sala no está disponible para el horario solicitado (conflicto de horarios)"
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES AGRUPADAS POR PELÍCULA
    // -------------------------------------------------
    public function getFuncionesAgrupadasPorPelicula($filters = []) {
        try {
            $funciones = $this->funcionModel->getByFilters($filters);

            // Agrupar funciones por película
            $peliculas = [];
            foreach ($funciones as $funcion) {
                $id_pelicula = $funcion["id_pelicula"];

                if (!isset($peliculas[$id_pelicula])) {
                    $peliculas[$id_pelicula] = [
                        "id_pelicula" => $id_pelicula,
                        "nombre" => $funcion["pelicula_nombre"],
                        "duracion" => $funcion["pelicula_duracion"],
                        "url_imagen" => $funcion["pelicula_imagen"],
                        "funciones" => []
                    ];
                }

                $peliculas[$id_pelicula]["funciones"][] = [
                    "id_funcion" => $funcion["id_funcion"],
                    "fecha" => $funcion["fecha"],
                    "hora" => $funcion["hora"],
                    "sala" => $funcion["numero_sala"],
                    "sede" => $funcion["sede_nombre"],
                    "id_sede" => $funcion["id_sede"],
                    "ciudad" => $funcion["ciudad_nombre"]
                ];
            }

            return [
                "success" => true,
                "data" => array_values($peliculas),
                "total_peliculas" => count($peliculas),
                "total_funciones" => count($funciones)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER HORARIOS DISPONIBLES PARA UNA PELÍCULA
    // -------------------------------------------------
    public function getHorariosDisponibles($id_pelicula, $fecha = null, $id_ciudad = null) {
        try {
            $filters = ["id_pelicula" => $id_pelicula];

            if ($fecha) {
                $filters["fecha"] = $fecha;
            } else {
                // Por defecto, mostrar funciones desde hoy
                $filters["fecha_desde"] = date('Y-m-d');
            }

            if ($id_ciudad) {
                $filters["id_ciudad"] = $id_ciudad;
            }

            $funciones = $this->funcionModel->getByFilters($filters);

            // Agrupar por fecha y sede
            $horarios = [];
            foreach ($funciones as $funcion) {
                $fecha_key = $funcion["fecha"];
                $sede_key = $funcion["id_sede"];

                if (!isset($horarios[$fecha_key])) {
                    $horarios[$fecha_key] = [
                        "fecha" => $fecha_key,
                        "sedes" => []
                    ];
                }

                if (!isset($horarios[$fecha_key]["sedes"][$sede_key])) {
                    $horarios[$fecha_key]["sedes"][$sede_key] = [
                        "id_sede" => $sede_key,
                        "nombre_sede" => $funcion["sede_nombre"],
                        "ciudad" => $funcion["ciudad_nombre"],
                        "horarios" => []
                    ];
                }

                $horarios[$fecha_key]["sedes"][$sede_key]["horarios"][] = [
                    "id_funcion" => $funcion["id_funcion"],
                    "hora" => $funcion["hora"],
                    "sala" => $funcion["numero_sala"]
                ];
            }

            // Convertir a array indexado
            $resultado = [];
            foreach ($horarios as $fecha_data) {
                $fecha_data["sedes"] = array_values($fecha_data["sedes"]);
                $resultado[] = $fecha_data;
            }

            return [
                "success" => true,
                "data" => $resultado,
                "total_fechas" => count($resultado)
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
?>
