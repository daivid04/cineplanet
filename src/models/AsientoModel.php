<?php

class AsientoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    
    /**
     * Verifica si ya existe un asiento con la misma fila y columna en la sala
     */
    private function existsByFilaColumna($fila, $columna, $idSala, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM asiento 
                WHERE fila_asiento = :fila AND columna_asiento = :columna AND id_sala = :id_sala";
        if ($excludeId) {
            $sql .= " AND id_asiento != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':fila', $fila, PDO::PARAM_STR);
        $stmt->bindParam(':columna', $columna, PDO::PARAM_STR);
        $stmt->bindParam(':id_sala', $idSala, PDO::PARAM_INT);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Verifica si una sala existe
     */
    private function salaExiste($idSala) {
        $sql = "SELECT COUNT(*) as count FROM sala WHERE id_sala = :id_sala";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id_sala' => $idSala]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    // -------------------------------------------------
    // GENERAR ASIENTOS PARA UNA SALA
    // -------------------------------------------------
    public function generarAsientos($idSala, $filas, $columnas) {
        if (!$this->salaExiste($idSala)) {
            return ['success' => false, 'message' => 'La sala no existe'];
        }

        if ($filas < 1 || $filas > 26) {
            return ['success' => false, 'message' => 'El numero de filas debe estar entre 1 y 26 (A-Z)'];
        }

        if ($columnas < 1 || $columnas > 50) {
            return ['success' => false, 'message' => 'El numero de columnas debe estar entre 1 y 50'];
        }

        // Verificar si ya tiene asientos
        $existentes = $this->countBySala($idSala);
        if ($existentes > 0) {
            return [
                'success' => false, 
                'message' => 'La sala ya tiene ' . $existentes . ' asientos configurados. Eliminalos primero si deseas regenerarlos.'
            ];
        }

        try {
            $this->conn->beginTransaction();

            $letras = range('A', 'Z');
            $sql = "INSERT INTO asiento (fila_asiento, columna_asiento, estado, id_sala) 
                    VALUES (:fila, :columna, 1, :id_sala)";
            $stmt = $this->conn->prepare($sql);

            $asientosCreados = 0;
            for ($f = 0; $f < $filas; $f++) {
                for ($c = 1; $c <= $columnas; $c++) {
                    $stmt->execute([
                        ':fila' => $letras[$f],
                        ':columna' => (string)$c,
                        ':id_sala' => $idSala
                    ]);
                    $asientosCreados++;
                }
            }

            $this->conn->commit();

            return [
                'success' => true,
                'message' => $asientosCreados . ' asientos generados exitosamente',
                'total' => $asientosCreados
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al generar asientos: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CREAR ASIENTO INDIVIDUAL
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["fila_asiento"]) || trim($data["fila_asiento"]) === "") {
            return ['success' => false, 'message' => 'La fila del asiento es requerida'];
        }

        if (!isset($data["columna_asiento"]) || trim($data["columna_asiento"]) === "") {
            return ['success' => false, 'message' => 'La columna del asiento es requerida'];
        }

        if (!isset($data["id_sala"]) || !is_numeric($data["id_sala"])) {
            return ['success' => false, 'message' => 'La sala es requerida'];
        }

        if (!$this->salaExiste($data["id_sala"])) {
            return ['success' => false, 'message' => 'La sala seleccionada no existe'];
        }

        if ($this->existsByFilaColumna($data["fila_asiento"], $data["columna_asiento"], $data["id_sala"])) {
            return ['success' => false, 'message' => 'Ya existe el asiento ' . $data["fila_asiento"] . $data["columna_asiento"] . ' en esta sala'];
        }

        try {
            $sql = "INSERT INTO asiento (fila_asiento, columna_asiento, estado, id_sala) 
                    VALUES (:fila, :columna, 1, :id_sala)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':fila' => trim($data["fila_asiento"]),
                ':columna' => trim($data["columna_asiento"]),
                ':id_sala' => intval($data["id_sala"])
            ]);

            return [
                'success' => true,
                'message' => 'Asiento ' . $data["fila_asiento"] . $data["columna_asiento"] . ' creado exitosamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear el asiento: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER ASIENTO POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $sql = "SELECT a.*, s.num_sala, se.nombre as sede_nombre
                    FROM asiento a
                    INNER JOIN sala s ON a.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    WHERE a.id_asiento = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS ASIENTOS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT a.*, s.num_sala, se.nombre as sede_nombre, se.id_sede
                    FROM asiento a
                    INNER JOIN sala s ON a.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede";
            
            if ($soloActivos) {
                $sql .= " WHERE a.estado = 1";
            }
            
            $sql .= " ORDER BY se.nombre, s.num_sala, a.fila_asiento, CAST(a.columna_asiento AS UNSIGNED)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER ASIENTOS POR SALA
    // -------------------------------------------------
    public function getBySala($idSala, $soloActivos = false) {
        try {
            $sql = "SELECT a.*, s.num_sala, se.nombre as sede_nombre
                    FROM asiento a
                    INNER JOIN sala s ON a.id_sala = s.id_sala
                    INNER JOIN sede se ON s.id_sede = se.id_sede
                    WHERE a.id_sala = :id_sala";
            
            if ($soloActivos) {
                $sql .= " AND a.estado = 1";
            }
            
            $sql .= " ORDER BY a.fila_asiento, CAST(a.columna_asiento AS UNSIGNED)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_sala' => $idSala]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER MAPA DE ASIENTOS (Para visualización en grid)
    // -------------------------------------------------
    public function getMapaBySala($idSala) {
        $asientos = $this->getBySala($idSala);
        
        if (empty($asientos)) {
            return ['success' => false, 'message' => 'No hay asientos configurados'];
        }

        // Organizar en matriz
        $mapa = [];
        $filas = [];
        $maxColumna = 0;

        foreach ($asientos as $asiento) {
            $fila = $asiento['fila_asiento'];
            $columna = intval($asiento['columna_asiento']);
            
            if (!in_array($fila, $filas)) {
                $filas[] = $fila;
            }
            
            if ($columna > $maxColumna) {
                $maxColumna = $columna;
            }

            if (!isset($mapa[$fila])) {
                $mapa[$fila] = [];
            }

            $mapa[$fila][$columna] = [
                'id' => $asiento['id_asiento'],
                'estado' => $asiento['estado'],
                'es_silla_ruedas' => isset($asiento['es_silla_ruedas']) ? (bool)$asiento['es_silla_ruedas'] : false,
                'codigo' => $fila . $columna
            ];
        }

        return [
            'mapa' => $mapa,
            'filas' => $filas,
            'columnas' => $maxColumna,
            'total' => count($asientos)
        ];
    }

    // -------------------------------------------------
    // OBTENER ASIENTOS OCUPADOS POR FUNCIÓN
    // -------------------------------------------------
    public function getOcupadosByFuncion($idFuncion) {
        try {
            $sql = "SELECT 
                        a.id_asiento, 
                        a.fila_asiento, 
                        a.columna_asiento,
                        CONCAT(a.fila_asiento, a.columna_asiento) as codigo
                    FROM asiento a
                    INNER JOIN descripcion_asiento da ON a.id_asiento = da.id_asiento
                    INNER JOIN compra_boleto cb ON da.id_compra_boleto = cb.id_compra_boleto
                    WHERE cb.id_funcion = :id_funcion";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_funcion' => $idFuncion]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR ASIENTO
    // -------------------------------------------------
    public function update($id, $data) {
        $asiento = $this->getById($id);
        if (!$asiento) {
            return ['success' => false, 'message' => 'Asiento no encontrado'];
        }

        if (isset($data["fila_asiento"]) && isset($data["columna_asiento"]) && isset($data["id_sala"])) {
            if ($this->existsByFilaColumna($data["fila_asiento"], $data["columna_asiento"], $data["id_sala"], $id)) {
                return ['success' => false, 'message' => 'Ya existe un asiento en esa posición'];
            }
        }

        try {
            $fields = [];
            $params = [':id' => $id];

            if (isset($data["fila_asiento"])) {
                $fields[] = "fila_asiento = :fila";
                $params[':fila'] = trim($data["fila_asiento"]);
            }
            if (isset($data["columna_asiento"])) {
                $fields[] = "columna_asiento = :columna";
                $params[':columna'] = trim($data["columna_asiento"]);
            }
            if (isset($data["estado"])) {
                $fields[] = "estado = :estado";
                $params[':estado'] = intval($data["estado"]);
            }

            if (!empty($fields)) {
                $sql = "UPDATE asiento SET " . implode(", ", $fields) . " WHERE id_asiento = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
            }

            return [
                'success' => true,
                'message' => 'Asiento actualizado exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR ASIENTO
    // -------------------------------------------------
    public function delete($id) {
        $asiento = $this->getById($id);
        if (!$asiento) {
            return ['success' => false, 'message' => 'Asiento no encontrado'];
        }

        // Verificar si tiene reservas asociadas
        $sqlReservas = "SELECT COUNT(*) as count FROM descripcion_asiento WHERE id_asiento = :id";
        $stmtReservas = $this->conn->prepare($sqlReservas);
        $stmtReservas->execute([':id' => $id]);
        $reservas = $stmtReservas->fetch(PDO::FETCH_ASSOC);

        if ($reservas['count'] > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar el asiento porque tiene ' . $reservas['count'] . ' reserva(s) asociada(s). Desactivelo en su lugar.'
            ];
        }

        try {
            $sql = "DELETE FROM asiento WHERE id_asiento = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return [
                'success' => true,
                'message' => 'Asiento ' . $asiento['fila_asiento'] . $asiento['columna_asiento'] . ' eliminado exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR TODOS LOS ASIENTOS DE UNA SALA
    // -------------------------------------------------
    public function deleteAllBySala($idSala) {
        // Verificar si algún asiento tiene reservas
        $sqlReservas = "SELECT COUNT(*) as count FROM descripcion_asiento da
                        INNER JOIN asiento a ON da.id_asiento = a.id_asiento
                        WHERE a.id_sala = :id_sala";
        $stmtReservas = $this->conn->prepare($sqlReservas);
        $stmtReservas->execute([':id_sala' => $idSala]);
        $reservas = $stmtReservas->fetch(PDO::FETCH_ASSOC);

        if ($reservas['count'] > 0) {
            return [
                'success' => false,
                'message' => 'No se pueden eliminar los asientos porque hay ' . $reservas['count'] . ' reserva(s) asociada(s).'
            ];
        }

        try {
            $total = $this->countBySala($idSala);
            
            $sql = "DELETE FROM asiento WHERE id_sala = :id_sala";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_sala' => $idSala]);

            return [
                'success' => true,
                'message' => $total . ' asientos eliminados exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // TOGGLE ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        $asiento = $this->getById($id);
        if (!$asiento) {
            return ['success' => false, 'message' => 'Asiento no encontrado'];
        }

        try {
            $sql = "UPDATE asiento SET estado = :estado WHERE id_asiento = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':estado' => intval($estado),
                ':id' => $id
            ]);

            $estadoTexto = $estado == 1 ? 'habilitado' : 'deshabilitado';
            return [
                'success' => true,
                'message' => 'Asiento ' . $asiento['fila_asiento'] . $asiento['columna_asiento'] . ' ' . $estadoTexto . ' exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al cambiar el estado: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR ASIENTOS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM asiento";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    // -------------------------------------------------
    // CONTAR ASIENTOS POR SALA
    // -------------------------------------------------
    public function countBySala($idSala, $soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM asiento WHERE id_sala = :id_sala";
            if ($soloActivos) {
                $sql .= " AND estado = 1";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id_sala' => $idSala]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
