<?php
require_once __DIR__ . "/../models/HorarioModel.php";

class HorarioController {
    private $horarioModel;

    public function __construct($conn) {
        $this->horarioModel = new HorarioModel($conn);
    }

    public function index($search = '') {
        // Definir rango de fechas (Semana del 14 al 20 de Octubre 2024 según mockup/datos)
        // Definir rango de fechas (Semana actual)
        $startDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));

        try {
            // Obtener todos los trabajadores
            $workers = $this->horarioModel->getAllWorkers($search);
            
            // Obtener horarios
            $schedules = $this->horarioModel->getWeeklySchedules($startDate . ' 00:00:00', $endDate . ' 23:59:59', $search);

            // Organizar datos para la vista: [id_trabajador => [info => ..., dias => [fecha => [turnos]]]]
            $organizedData = [];

            // Inicializar estructura para todos los trabajadores
            foreach ($workers as $worker) {
                $organizedData[$worker['id_trabajador']] = [
                    'info' => $worker,
                    'dias' => []
                ];
            }

            // Llenar con horarios
            foreach ($schedules as $schedule) {
                $workerId = $schedule['id_trabajador'];
                $date = $schedule['fecha'];
                
                if (!isset($organizedData[$workerId]['dias'][$date])) {
                    $organizedData[$workerId]['dias'][$date] = [];
                }

                $organizedData[$workerId]['dias'][$date][] = [
                    'tipo' => $schedule['tipo_turno'],
                    'entrada' => date('g:ia', strtotime($schedule['entrada'])),
                    'salida' => date('g:ia', strtotime($schedule['salida'])),
                    'color_class' => $this->getColorClass($schedule['tipo_turno'])
                ];
            }

            return [
                "success" => true,
                "data" => $organizedData,
                "week_range" => date('M d', strtotime($startDate)) . " - " . date('M d, Y', strtotime($endDate)),
                "dates" => $this->getDatesFromRange($startDate, $endDate)
            ];

        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    private function getColorClass($type) {
        switch ($type) {
            case 'Apertura': return 'bg-blue-100 text-blue-800 border-blue-200';
            case 'Intermedio': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
            case 'Cierre': return 'bg-purple-100 text-purple-800 border-purple-200';
            default: return 'bg-gray-100 text-gray-800 border-gray-200';
        }
    }

    private function getDatesFromRange($start, $end) {
        $dates = [];
        $current = strtotime($start);
        $last = strtotime($end);

        while ($current <= $last) {
            $dates[] = [
                'full' => date('Y-m-d', $current),
                'day_name' => $this->translateDay(date('l', $current)),
                'day_num' => date('j', $current)
            ];
            $current = strtotime('+1 day', $current);
        }
        return $dates;
    }

    private function translateDay($day) {
        $days = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes',
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        return $days[$day] ?? $day;
    }

    public function create() {
        try {
            $workers = $this->horarioModel->getAllWorkers();
            return [
                "success" => true,
                "workers" => $workers
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function store($data) {
        try {
            // Validar datos básicos
            if (empty($data['id_trabajador']) || empty($data['fecha']) || empty($data['hora_inicio']) || empty($data['hora_fin']) || empty($data['tipo_turno'])) {
                throw new Exception("Todos los campos son obligatorios.");
            }

            $this->horarioModel->assignShift(
                $data['id_trabajador'],
                $data['fecha'],
                $data['hora_inicio'],
                $data['hora_fin'],
                $data['tipo_turno'],
                $data['notas'] ?? ''
            );

            return [
                "success" => true,
                "message" => "Turno asignado exitosamente."
            ];

        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // EXPORTAR A CSV
    // -------------------------------------------------
    public function export($search = '') {
        // Definir rango de fechas (Semana del 14 al 20 de Octubre 2024 según mockup/datos)
        $startDate = date('Y-m-d', strtotime('monday this week'));
        $endDate = date('Y-m-d', strtotime('sunday this week'));

        try {
            $schedules = $this->horarioModel->getWeeklySchedules($startDate . ' 00:00:00', $endDate . ' 23:59:59', $search);

            // Configurar headers para descarga
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=horarios.csv');

            // Crear puntero de salida
            $output = fopen('php://output', 'w');

            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados de columnas
            fputcsv($output, ['Empleado', 'Apellido', 'Fecha', 'Día', 'Turno', 'Entrada', 'Salida']);

            // Datos
            foreach ($schedules as $row) {
                fputcsv($output, [
                    $row['nombre'],
                    $row['apellido'],
                    $row['fecha'],
                    $this->translateDay($row['dia_semana']),
                    $row['tipo_turno'],
                    date('H:i', strtotime($row['entrada'])),
                    date('H:i', strtotime($row['salida']))
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            die("Error al exportar: " . $e->getMessage());
        }
    }
}
?>
