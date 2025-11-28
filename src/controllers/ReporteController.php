<?php
require_once __DIR__ . '/../models/ReporteModel.php';

class ReporteController {
    private $reporteModel;

    public function __construct($conn) {
        $this->reporteModel = new ReporteModel($conn);
    }

    public function index($search = '') {
        try {
            // Por defecto, mes y año actuales
            $month = date('m');
            $year = date('Y');

            // Obtener datos
            $hoursBySede = $this->reporteModel->getHoursBySede($month, $year);
            $kpis = $this->reporteModel->getKPIs();
            $workerStats = $this->reporteModel->getWorkerStats($search);

            // Preparar datos para Chart.js
            $chartLabels = [];
            $chartData = [];

            foreach ($hoursBySede as $row) {
                $chartLabels[] = $row['sede'];
                $chartData[] = $row['total_horas'];
            }

            return [
                "success" => true,
                "data" => [
                    "chart" => [
                        "labels" => $chartLabels,
                        "data" => $chartData
                    ],
                    "kpis" => $kpis,
                    "workers" => $workerStats
                ]
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
        try {
            $workerStats = $this->reporteModel->getWorkerStats($search);

            // Configurar headers para descarga
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=reporte_empleados.csv');

            // Crear puntero de salida
            $output = fopen('php://output', 'w');

            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados de columnas
            fputcsv($output, ['Empleado', 'DNI', 'Sede', 'Horas Trabajadas', '% Inasistencia', 'Tardanzas']);

            // Datos
            foreach ($workerStats as $row) {
                fputcsv($output, [
                    $row['nombre'] . ' ' . $row['apellido'],
                    $row['dni'],
                    $row['sede'],
                    $row['horas_trabajadas'],
                    $row['inasistencia'] . '%',
                    $row['tardanzas']
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
