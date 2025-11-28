<?php

class ReporteModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // OBTENER HORAS TRABAJADAS POR SEDE
    // -------------------------------------------------
    public function getHoursBySede($month, $year) {
        try {
            // Calculamos la diferencia en horas entre salida y entrada
            // Unimos tablas para llegar desde horario hasta sede
            $sql = "SELECT 
                        s.nombre AS sede,
                        SUM(TIMESTAMPDIFF(HOUR, hdt.entrada, hdt.salida)) as total_horas
                    FROM horarios_de_trabajo hdt
                    INNER JOIN horario_trabajador ht ON hdt.id_h_trabajo = ht.id_h_trabajo
                    INNER JOIN dia_laborable dl ON ht.id_dia_laborable = dl.id_dia_laborable
                    INNER JOIN trabajador t ON dl.id_trabajador = t.id_trabajador
                    INNER JOIN sede s ON t.id_sede = s.id_sede
                    WHERE MONTH(hdt.entrada) = :month AND YEAR(hdt.entrada) = :year
                    GROUP BY s.id_sede, s.nombre
                    ORDER BY total_horas DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':month' => $month,
                ':year' => $year
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener horas por sede: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER KPIS (ALGUNOS REALES, OTROS SIMULADOS)
    // -------------------------------------------------
    public function getKPIs() {
        try {
            $kpis = [];

            // 1. Total Horas Extras (Simulado: Asumimos que cualquier turno > 8 horas tiene extras, o simplemente sumamos todo por ahora)
            // Para este ejemplo, sumaremos todas las horas del mes actual como dato real
            $sqlHoras = "SELECT SUM(TIMESTAMPDIFF(HOUR, entrada, salida)) as total FROM horarios_de_trabajo WHERE MONTH(entrada) = MONTH(CURRENT_DATE())";
            $stmtHoras = $this->conn->query($sqlHoras);
            $totalHoras = $stmtHoras->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
            $kpis['horas_totales'] = $totalHoras;

            // 2. Tasa de Ausentismo (Simulado)
            $kpis['ausentismo'] = "2.5%";

            // 3. Rotación de Personal (Simulado)
            $kpis['rotacion'] = "8.1%";

            // 4. Satisfacción (Simulado)
            $kpis['satisfaccion'] = "4.2 / 5.0";

            return $kpis;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener KPIs: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER ESTADÍSTICAS POR TRABAJADOR
    // -------------------------------------------------
    public function getWorkerStats($search = '') {
        try {
            $params = [];
            $sql = "SELECT 
                        t.id_trabajador,
                        t.nombre,
                        t.apellido,
                        t.dni,
                        s.nombre AS sede,
                        COALESCE(SUM(TIMESTAMPDIFF(HOUR, hdt.entrada, hdt.salida)), 0) as horas_trabajadas
                    FROM trabajador t
                    INNER JOIN sede s ON t.id_sede = s.id_sede
                    LEFT JOIN dia_laborable dl ON t.id_trabajador = dl.id_trabajador
                    LEFT JOIN horario_trabajador ht ON dl.id_dia_laborable = ht.id_dia_laborable
                    LEFT JOIN horarios_de_trabajo hdt ON ht.id_h_trabajo = hdt.id_h_trabajo
                    WHERE t.estado = 1";

            if (!empty($search)) {
                $sql .= " AND (t.nombre LIKE :search OR t.apellido LIKE :search OR t.dni LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " GROUP BY t.id_trabajador, t.nombre, t.apellido, t.dni, s.nombre
                      ORDER BY t.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Enriquecer con datos simulados de inasistencia y tardanzas
            foreach ($workers as &$worker) {
                // Simulación: Generar un % aleatorio de inasistencia entre 0 y 5%
                $worker['inasistencia'] = rand(0, 50) / 10; // 0.0 a 5.0
                
                // Simulación: Generar un número aleatorio de tardanzas entre 0 y 10
                $worker['tardanzas'] = rand(0, 10);
            }

            return $workers;

        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas de trabajadores: " . $e->getMessage());
        }
    }
}
?>
