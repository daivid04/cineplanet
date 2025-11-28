<?php

class FuncionModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function validarFuncion($data, $modo = "insertar") {
        // Campos obligatorios para insertar
        $requeridos = ["fecha", "hora", "id_pelicula", "id_sala"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        // Validar formato de fecha (YYYY-MM-DD)
        if (isset($data["fecha"])) {
            $fecha = $data["fecha"];
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha) || !strtotime($fecha)) {
                throw new Exception("Fecha inválida. Use el formato YYYY-MM-DD.");
            }
        }

        // Validar formato de hora (HH:MM:SS o HH:MM)
        if (isset($data["hora"])) {
            $hora = $data["hora"];
            if (!preg_match("/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/", $hora)) {
                throw new Exception("Hora inválida. Use el formato HH:MM o HH:MM:SS.");
            }
        }

        // Validar que id_pelicula y id_sala sean numéricos
        if (isset($data["id_pelicula"]) && !is_numeric($data["id_pelicula"])) {
            throw new Exception("ID de película inválido.");
        }

        if (isset($data["id_sala"]) && !is_numeric($data["id_sala"])) {
            throw new Exception("ID de sala inválido.");
        }

        return true;
    }

    // -------------------------------------------------
    // CREAR FUNCIÓN
    // -------------------------------------------------
    public function create($data) {
        $this->validarFuncion($data, "insertar");

        try {
            $sql = "INSERT INTO funcion (fecha, hora, id_pelicula, id_sala) 
                    VALUES (:fecha, :hora, :id_pelicula, :id_sala)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":fecha" => $data["fecha"],
                ":hora" => $data["hora"],
                ":id_pelicula" => $data["id_pelicula"],
                ":id_sala" => $data["id_sala"]
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear la función: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIÓN POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    WHERE f.id_funcion = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la función: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS FUNCIONES
    // -------------------------------------------------
    public function getAll() {
        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    ORDER BY f.fecha ASC, f.hora ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las funciones: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR PELÍCULA
    // -------------------------------------------------
    public function getByPelicula($id_pelicula) {
        if (!is_numeric($id_pelicula)) {
            throw new Exception("ID de película inválido.");
        }

        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    WHERE f.id_pelicula = :id_pelicula
                    ORDER BY f.fecha ASC, f.hora ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_pelicula" => $id_pelicula]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las funciones de la película: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR SEDE
    // -------------------------------------------------
    public function getBySede($id_sede) {
        if (!is_numeric($id_sede)) {
            throw new Exception("ID de sede inválido.");
        }

        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    WHERE s.id_sede = :id_sede
                    ORDER BY f.fecha ASC, f.hora ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_sede" => $id_sede]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las funciones de la sede: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES POR FECHA
    // -------------------------------------------------
    public function getByFecha($fecha) {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha) || !strtotime($fecha)) {
            throw new Exception("Fecha inválida. Use el formato YYYY-MM-DD.");
        }

        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    WHERE f.fecha = :fecha
                    ORDER BY f.hora ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":fecha" => $fecha]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las funciones por fecha: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIONES CON FILTROS COMBINADOS
    // -------------------------------------------------
    public function getByFilters($filters = []) {
        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre,
                        c.id_ciudad
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    WHERE 1=1";

            $params = [];

            // Filtro por película
            if (isset($filters["id_pelicula"]) && is_numeric($filters["id_pelicula"])) {
                $sql .= " AND f.id_pelicula = :id_pelicula";
                $params[":id_pelicula"] = $filters["id_pelicula"];
            }

            // Filtro por sede
            if (isset($filters["id_sede"]) && is_numeric($filters["id_sede"])) {
                $sql .= " AND se.id_sede = :id_sede";
                $params[":id_sede"] = $filters["id_sede"];
            }

            // Filtro por ciudad
            if (isset($filters["id_ciudad"]) && is_numeric($filters["id_ciudad"])) {
                $sql .= " AND c.id_ciudad = :id_ciudad";
                $params[":id_ciudad"] = $filters["id_ciudad"];
            }

            // Filtro por fecha
            if (isset($filters["fecha"])) {
                $sql .= " AND f.fecha = :fecha";
                $params[":fecha"] = $filters["fecha"];
            }

            // Filtro por rango de fechas
            if (isset($filters["fecha_desde"])) {
                $sql .= " AND f.fecha >= :fecha_desde";
                $params[":fecha_desde"] = $filters["fecha_desde"];
            }

            if (isset($filters["fecha_hasta"])) {
                $sql .= " AND f.fecha <= :fecha_hasta";
                $params[":fecha_hasta"] = $filters["fecha_hasta"];
            }

            $sql .= " ORDER BY f.fecha ASC, f.hora ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las funciones con filtros: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR FUNCIÓN
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $this->validarFuncion($data, "actualizar");

        try {
            // Construir la consulta dinámicamente según los campos proporcionados
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["fecha"])) {
                $campos[] = "fecha = :fecha";
                $params[":fecha"] = $data["fecha"];
            }

            if (isset($data["hora"])) {
                $campos[] = "hora = :hora";
                $params[":hora"] = $data["hora"];
            }

            if (isset($data["id_pelicula"])) {
                $campos[] = "id_pelicula = :id_pelicula";
                $params[":id_pelicula"] = $data["id_pelicula"];
            }

            if (isset($data["id_sala"])) {
                $campos[] = "id_sala = :id_sala";
                $params[":id_sala"] = $data["id_sala"];
            }

            if (empty($campos)) {
                throw new Exception("No se proporcionaron campos para actualizar.");
            }

            $sql = "UPDATE funcion SET " . implode(", ", $campos) . " WHERE id_funcion = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la función: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ELIMINAR FUNCIÓN
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            $sql = "DELETE FROM funcion WHERE id_funcion = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar la función: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // VERIFICAR DISPONIBILIDAD DE SALA
    // -------------------------------------------------
    public function verificarDisponibilidadSala($id_sala, $fecha, $hora, $duracion_pelicula, $id_funcion_excluir = null) {
        try {
            // Convertir duración de minutos a formato de tiempo
            $hora_fin = date('H:i:s', strtotime($hora) + ($duracion_pelicula * 60));

            $sql = "SELECT 
                        f.id_funcion,
                        f.hora,
                        p.duracion,
                        TIME_FORMAT(ADDTIME(f.hora, SEC_TO_TIME(p.duracion * 60)), '%H:%i:%s') AS hora_fin
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    WHERE f.id_sala = :id_sala 
                    AND f.fecha = :fecha";

            if ($id_funcion_excluir !== null) {
                $sql .= " AND f.id_funcion != :id_funcion_excluir";
            }

            $stmt = $this->conn->prepare($sql);
            $params = [
                ":id_sala" => $id_sala,
                ":fecha" => $fecha
            ];

            if ($id_funcion_excluir !== null) {
                $params[":id_funcion_excluir"] = $id_funcion_excluir;
            }

            $stmt->execute($params);
            $funciones_existentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Verificar si hay conflictos de horario
            foreach ($funciones_existentes as $funcion) {
                $hora_inicio_existente = $funcion["hora"];
                $hora_fin_existente = $funcion["hora_fin"];

                // Verificar si hay solapamiento
                if (
                    ($hora >= $hora_inicio_existente && $hora < $hora_fin_existente) ||
                    ($hora_fin > $hora_inicio_existente && $hora_fin <= $hora_fin_existente) ||
                    ($hora <= $hora_inicio_existente && $hora_fin >= $hora_fin_existente)
                ) {
                    return false; // Hay conflicto
                }
            }

            return true; // No hay conflicto
        } catch (PDOException $e) {
            throw new Exception("Error al verificar disponibilidad de sala: " . $e->getMessage());
        }
    }
}
?>
