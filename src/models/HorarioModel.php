<?php

class HorarioModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Obtener horarios para una semana específica (o todos por ahora)
    public function getWeeklySchedules($startDate, $endDate, $search = '') {
        try {
            // Esta consulta une trabajadores con sus días laborables y horarios asignados
            // Filtra por rango de fechas en horarios_de_trabajo
            $sql = "SELECT 
                        t.id_trabajador,
                        t.nombre,
                        t.apellido,
                        ht.horario AS tipo_turno, -- 'Apertura', 'Cierre', etc.
                        hdt.entrada,
                        hdt.salida,
                        DAYNAME(hdt.entrada) as dia_semana,
                        DATE(hdt.entrada) as fecha
                    FROM trabajador t
                    INNER JOIN dia_laborable dl ON t.id_trabajador = dl.id_trabajador
                    INNER JOIN horario_trabajador ht ON dl.id_dia_laborable = ht.id_dia_laborable
                    INNER JOIN horarios_de_trabajo hdt ON ht.id_h_trabajo = hdt.id_h_trabajo
                    WHERE hdt.entrada BETWEEN :startDate AND :endDate";

            $params = [
                ':startDate' => $startDate,
                ':endDate' => $endDate
            ];

            if (!empty($search)) {
                $sql .= " AND (t.nombre LIKE :search OR t.apellido LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY t.nombre, hdt.entrada";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener horarios: " . $e->getMessage());
        }
    }

    // Obtener todos los trabajadores (para mostrar filas vacías si no tienen turno)
    public function getAllWorkers($search = '') {
        try {
            $sql = "SELECT id_trabajador, nombre, apellido, tipo FROM trabajador WHERE estado = 1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search OR apellido LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY nombre";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener trabajadores: " . $e->getMessage());
        }
    }

    public function assignShift($workerId, $date, $startTime, $endTime, $shiftType, $notes = '') {
        try {
            $this->conn->beginTransaction();

            // 1. Insertar en horarios_de_trabajo
            $startDateTime = $date . ' ' . $startTime;
            $endDateTime = $date . ' ' . $endTime;
            
            $sql1 = "INSERT INTO horarios_de_trabajo (entrada, salida) VALUES (:entrada, :salida)";
            $stmt1 = $this->conn->prepare($sql1);
            $stmt1->execute([':entrada' => $startDateTime, ':salida' => $endDateTime]);
            $idHorarioTrabajo = $this->conn->lastInsertId();

            // 2. Insertar en dia_laborable
            // Nota: 'turno' en dia_laborable parece ser genérico (Mañana, Tarde, Noche). 
            // Usaremos el shiftType o inferiremos. Por ahora usaremos shiftType.
            $sql2 = "INSERT INTO dia_laborable (turno, id_trabajador) VALUES (:turno, :id_trabajador)";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->execute([':turno' => $shiftType, ':id_trabajador' => $workerId]);
            $idDiaLaborable = $this->conn->lastInsertId();

            // 3. Insertar en horario_trabajador
            $sql3 = "INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES (:horario, :id_h_trabajo, :id_dia_laborable)";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->execute([
                ':horario' => $shiftType, // 'Apertura', 'Cierre', etc.
                ':id_h_trabajo' => $idHorarioTrabajo,
                ':id_dia_laborable' => $idDiaLaborable
            ]);

            $this->conn->commit();
            return true;

        } catch (PDOException $e) {
            $this->conn->rollBack();
            throw new Exception("Error al asignar turno: " . $e->getMessage());
        }
    }
}
?>
