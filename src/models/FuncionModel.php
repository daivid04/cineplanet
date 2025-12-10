<?php
/**
 * Modelo para la tabla funcion
 * 
 * Tabla: funcion
 * Campos: id_funcion, fecha, hora, id_pelicula, id_sala, estado
 * 
 * Relaciones:
 * - Pertenece a: pelicula, sala
 * - Referenciada por: compra_boleto (no eliminar si hay boletos vendidos)
 */

class FuncionModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    
    /**
     * Verifica si una película existe
     */
    public function peliculaExiste($idPelicula) {
        $sql = "SELECT id_pelicula, nombre, duracion, estado FROM pelicula WHERE id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idPelicula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si una sala existe
     */
    public function salaExiste($idSala) {
        $sql = "SELECT s.id_sala, s.num_sala, s.estado, se.nombre as sede_nombre, se.id_sede
                FROM sala s
                INNER JOIN sede se ON s.id_sede = se.id_sede
                WHERE s.id_sala = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idSala]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta los boletos vendidos para una función
     */
    public function contarBoletosVendidos($idFuncion) {
        $sql = "SELECT COUNT(*) as total FROM compra_boleto WHERE id_funcion = :id_funcion";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_funcion' => $idFuncion]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return intval($result['total']);
    }

    /**
     * Verifica si la función ya pasó
     */
    public function funcionPasada($fecha, $hora) {
        $fechaHoraFuncion = strtotime("$fecha $hora");
        return $fechaHoraFuncion < time();
    }

    /**
     * Verifica conflicto de horarios en la misma sala
     */
    public function tieneConflictoHorario($idSala, $fecha, $hora, $duracionPelicula, $excludeId = null) {
        $horaFin = date('H:i:s', strtotime($hora) + ($duracionPelicula * 60));

        $sql = "SELECT f.id_funcion, f.hora, p.duracion, p.nombre as pelicula_nombre,
                       TIME_FORMAT(ADDTIME(f.hora, SEC_TO_TIME(p.duracion * 60)), '%H:%i') AS hora_fin
                FROM funcion f
                INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                WHERE f.id_sala = :id_sala AND f.fecha = :fecha AND f.estado = 1";

        if ($excludeId) {
            $sql .= " AND f.id_funcion != :exclude_id";
        }

        $stmt = $this->conn->prepare($sql);
        $params = [':id_sala' => $idSala, ':fecha' => $fecha];
        if ($excludeId) {
            $params[':exclude_id'] = $excludeId;
        }
        $stmt->execute($params);
        $funcionesExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($funcionesExistentes as $funcion) {
            $horaInicioExistente = $funcion['hora'];
            $horaFinExistente = date('H:i:s', strtotime($funcion['hora']) + ($funcion['duracion'] * 60));

            // Verificar solapamiento
            if (
                ($hora >= $horaInicioExistente && $hora < $horaFinExistente) ||
                ($horaFin > $horaInicioExistente && $horaFin <= $horaFinExistente) ||
                ($hora <= $horaInicioExistente && $horaFin >= $horaFinExistente)
            ) {
                return [
                    'conflicto' => true,
                    'mensaje' => 'Conflicto con "' . $funcion['pelicula_nombre'] . '" de ' . 
                                 date('H:i', strtotime($horaInicioExistente)) . ' a ' . $funcion['hora_fin']
                ];
            }
        }

        return ['conflicto' => false];
    }

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

        // Validar película
        $pelicula = $this->peliculaExiste($data['id_pelicula']);
        if (!$pelicula) {
            return ['success' => false, 'message' => 'La película seleccionada no existe'];
        }
        if ($pelicula['estado'] == 0) {
            return ['success' => false, 'message' => 'La película seleccionada está inactiva'];
        }

        // Validar sala
        $sala = $this->salaExiste($data['id_sala']);
        if (!$sala) {
            return ['success' => false, 'message' => 'La sala seleccionada no existe'];
        }
        if ($sala['estado'] == 0) {
            return ['success' => false, 'message' => 'La sala seleccionada está inactiva'];
        }

        // Validar conflicto de horarios
        $conflicto = $this->tieneConflictoHorario(
            $data['id_sala'], 
            $data['fecha'], 
            $data['hora'], 
            $pelicula['duracion']
        );
        if ($conflicto['conflicto']) {
            return ['success' => false, 'message' => $conflicto['mensaje']];
        }

        try {
            $sql = "INSERT INTO funcion (fecha, hora, id_pelicula, id_sala, estado) 
                    VALUES (:fecha, :hora, :id_pelicula, :id_sala, 1)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":fecha" => $data["fecha"],
                ":hora" => $data["hora"],
                ":id_pelicula" => $data["id_pelicula"],
                ":id_sala" => $data["id_sala"]
            ]);

            return [
                'success' => true,
                'message' => 'Función creada exitosamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear la función: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER FUNCIÓN POR ID
    // -------------------------------------------------
public function getById($id) {
    if (!is_numeric($id)) {
        return null;
    }

    try {
        $sql = "SELECT 
                    f.id_funcion,
                    f.fecha,
                    f.hora,
                    f.estado,
                    f.id_pelicula,
                    f.id_sala,
                    p.nombre AS pelicula_nombre,
                    p.duracion AS pelicula_duracion,
                    p.url_imagen AS pelicula_imagen,
                    s.num_sala AS numero_sala,
                    se.nombre AS sede_nombre,
                    se.id_sede,
                    c.nombre AS ciudad_nombre,
                    -- Subconsulta para contar boletos (se mantiene igual)
                    (SELECT COUNT(*) FROM compra_boleto cb WHERE cb.id_funcion = f.id_funcion) as boletos_vendidos,
                    -- Nueva lógica para concatenar formatos (Ej: '2D, 3D, IMAX')
                    GROUP_CONCAT(DISTINCT forma.nombre SEPARATOR ', ') AS formatos
                FROM funcion f
                INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                INNER JOIN sala s ON f.id_sala = s.id_sala
                INNER JOIN sede se ON s.id_sede = se.id_sede
                INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                -- Joins adicionales para llegar al nombre del formato
                LEFT JOIN formato_pelicula fo ON fo.id_pelicula = p.id_pelicula
                LEFT JOIN formato forma ON forma.id_formato = fo.id_formato
                WHERE f.id_funcion = :id
                GROUP BY f.id_funcion";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        // Es buena práctica loguear el error real en un archivo de logs si es posible
        // error_log($e->getMessage());
        return null;
    }
}
    // -------------------------------------------------
    // OBTENER TODAS LAS FUNCIONES
    // -------------------------------------------------
    public function getAll($soloActivos = false, $soloFuturas = false) {
        try {
            $sql = "SELECT 
                        f.id_funcion,
                        f.fecha,
                        f.hora,
                        f.estado,
                        f.id_pelicula,
                        f.id_sala,
                        p.nombre AS pelicula_nombre,
                        p.duracion AS pelicula_duracion,
                        p.url_imagen AS pelicula_imagen,
                        s.num_sala AS numero_sala,
                        se.nombre AS sede_nombre,
                        se.id_sede,
                        c.nombre AS ciudad_nombre,
                        (SELECT COUNT(*) FROM compra_boleto cb WHERE cb.id_funcion = f.id_funcion) as boletos_vendidos
                    FROM funcion f
                    INNER JOIN pelicula p ON f.id_pelicula = p.id_pelicula
                    INNER JOIN sala s ON f.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    INNER JOIN ciudad c ON se.id_ciudad = c.id_ciudad
                    WHERE 1=1";
            
            if ($soloActivos) {
                $sql .= " AND f.estado = 1";
            }
            
            if ($soloFuturas) {
                $sql .= " AND CONCAT(f.fecha, ' ', f.hora) >= NOW()";
            }
            
            $sql .= " ORDER BY f.fecha DESC, f.hora DESC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
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
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $funcion = $this->getById($id);
        if (!$funcion) {
            return ['success' => false, 'message' => 'Función no encontrada'];
        }

        // Verificar si tiene boletos vendidos
        $boletos = $this->contarBoletosVendidos($id);
        if ($boletos > 0) {
            // Campos restringidos si hay boletos
            $camposRestringidos = ['fecha', 'hora', 'id_sala'];
            foreach ($camposRestringidos as $campo) {
                if (isset($data[$campo]) && $data[$campo] != $funcion[$campo]) {
                    return [
                        'success' => false, 
                        'message' => "No se puede modificar $campo porque ya hay $boletos boletos vendidos"
                    ];
                }
            }
        }

        $this->validarFuncion($data, "actualizar");

        // Validar película si se cambia
        $idPelicula = isset($data['id_pelicula']) ? $data['id_pelicula'] : $funcion['id_pelicula'];
        $pelicula = $this->peliculaExiste($idPelicula);
        if (!$pelicula) {
            return ['success' => false, 'message' => 'La película no existe'];
        }

        // Validar conflicto si cambia fecha, hora o sala
        $fecha = isset($data['fecha']) ? $data['fecha'] : $funcion['fecha'];
        $hora = isset($data['hora']) ? $data['hora'] : $funcion['hora'];
        $idSala = isset($data['id_sala']) ? $data['id_sala'] : $funcion['id_sala'];

        if (isset($data['fecha']) || isset($data['hora']) || isset($data['id_sala'])) {
            $conflicto = $this->tieneConflictoHorario($idSala, $fecha, $hora, $pelicula['duracion'], $id);
            if ($conflicto['conflicto']) {
                return ['success' => false, 'message' => $conflicto['mensaje']];
            }
        }

        try {
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
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }

            $sql = "UPDATE funcion SET " . implode(", ", $campos) . " WHERE id_funcion = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'message' => 'Función actualizada exitosamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR FUNCIÓN
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $funcion = $this->getById($id);
        if (!$funcion) {
            return ['success' => false, 'message' => 'Función no encontrada'];
        }

        // Verificar boletos vendidos
        $boletos = $this->contarBoletosVendidos($id);
        if ($boletos > 0) {
            return [
                'success' => false, 
                'message' => "No se puede eliminar la función porque tiene $boletos boletos vendidos"
            ];
        }

        try {
            $sql = "DELETE FROM funcion WHERE id_funcion = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return ['success' => true, 'message' => 'Función eliminada exitosamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO (TOGGLE)
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        $funcion = $this->getById($id);
        if (!$funcion) {
            return ['success' => false, 'message' => 'Función no encontrada'];
        }

        // No desactivar si tiene boletos vendidos y es futura
        if ($estado == 0) {
            $boletos = $this->contarBoletosVendidos($id);
            if ($boletos > 0 && !$this->funcionPasada($funcion['fecha'], $funcion['hora'])) {
                return [
                    'success' => false, 
                    'message' => "No se puede desactivar porque tiene $boletos boletos vendidos"
                ];
            }
        }

        try {
            $sql = "UPDATE funcion SET estado = :estado WHERE id_funcion = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':estado' => $estado, ':id' => $id]);

            $mensaje = $estado == 1 ? 'Función activada' : 'Función desactivada';
            return ['success' => true, 'message' => $mensaje];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al cambiar estado: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR FUNCIONES
    // -------------------------------------------------
    public function count($soloActivos = false, $soloFuturas = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM funcion WHERE 1=1";
            
            if ($soloActivos) {
                $sql .= " AND estado = 1";
            }
            
            if ($soloFuturas) {
                $sql .= " AND CONCAT(fecha, ' ', hora) >= NOW()";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($result['total']);
        } catch (PDOException $e) {
            return 0;
        }
    }

    // -------------------------------------------------
    // HELPER: Formatear duración
    // -------------------------------------------------
    public static function formatDuration($minutos) {
        $horas = floor($minutos / 60);
        $mins = $minutos % 60;
        
        if ($horas > 0 && $mins > 0) {
            return "{$horas}h {$mins}min";
        } else if ($horas > 0) {
            return "{$horas}h";
        } else {
            return "{$mins}min";
        }
    }

    // -------------------------------------------------
    // HELPER: Calcular hora fin
    // -------------------------------------------------
    public static function calcularHoraFin($hora, $duracionMinutos) {
        $timestamp = strtotime($hora) + ($duracionMinutos * 60);
        return date('H:i', $timestamp);
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
