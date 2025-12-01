<?php
require_once __DIR__ . "/../models/EmpleadoModel.php";
require_once __DIR__ . "/../models/SedeModelFixed.php";
require_once __DIR__ . "/../models/HorarioModel.php";

class EmpleadoController {
    private $empleadoModel;
    private $sedeModel;
    private $horarioModel;

    public function __construct($conn) {
        $this->empleadoModel = new EmpleadoModel($conn);
        $this->sedeModel = new SedeModel($conn);
        $this->horarioModel = new HorarioModel($conn);
    }

    // -------------------------------------------------
    // OBTENER DATOS PARA EL DASHBOARD
    // -------------------------------------------------
    public function index() {
        try {
            $empleados = $this->empleadoModel->getAll();
            $stats = $this->empleadoModel->getStats();

            return [
                "success" => true,
                "data" => [
                    "empleados" => $empleados,
                    "stats" => $stats
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
    // LISTAR EMPLEADOS CON FILTROS Y PAGINACIÓN
    // -------------------------------------------------
    public function list($page = 1, $filters = []) {
        try {
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $result = $this->empleadoModel->getAllFiltered($filters, $limit, $offset);
            $sedes = $this->sedeModel->getAll();
            $cargos = $this->empleadoModel->getCargos();

            return [
                "success" => true,
                "data" => [
                    "empleados" => $result['data'],
                    "total" => $result['total'],
                    "page" => $page,
                    "limit" => $limit,
                    "sedes" => $sedes,
                    "cargos" => $cargos
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
    // CREAR EMPLEADO
    // -------------------------------------------------
    public function create($data) {
        try {
            // Crear empleado
            $newId = $this->empleadoModel->create($data);

            // Si hay datos de turno, asignar turno
            if (!empty($data['fecha_turno']) && !empty($data['hora_inicio']) && !empty($data['hora_fin']) && !empty($data['tipo_turno'])) {
                $this->horarioModel->assignShift(
                    $newId,
                    $data['fecha_turno'],
                    $data['hora_inicio'],
                    $data['hora_fin'],
                    $data['tipo_turno'],
                    "Turno inicial asignado al crear empleado"
                );
            }

            return [
                "success" => true,
                "message" => "Empleado creado exitosamente",
                "id_empleado" => $newId
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // OBTENER EMPLEADO POR ID
    // -------------------------------------------------
    public function getById($id) {
        try {
            $empleado = $this->empleadoModel->getById($id);
            
            if (!$empleado) {
                return [
                    "success" => false,
                    "message" => "Empleado no encontrado"
                ];
            }

            return [
                "success" => true,
                "data" => $empleado
            ];
        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR EMPLEADO
    // -------------------------------------------------
    public function update($id, $data) {
        try {
            $success = $this->empleadoModel->update($id, $data);
            
            if ($success) {
                return [
                    "success" => true,
                    "message" => "Empleado actualizado exitosamente"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se realizaron cambios o error al actualizar"
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
    // OBTENER SEDES (PARA DROPDOWN)
    // -------------------------------------------------
    public function getSedes() {
        try {
            $sedes = $this->sedeModel->getAll();
            return [
                "success" => true,
                "data" => $sedes
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
    public function export($filters = []) {
        try {
            // Obtener todos los empleados filtrados (sin paginación)
            $result = $this->empleadoModel->getAllFiltered($filters, 100000, 0);
            $empleados = $result['data'];

            // Configurar headers para descarga
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=empleados.csv');

            // Crear puntero de salida
            $output = fopen('php://output', 'w');

            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

            // Encabezados de columnas
            fputcsv($output, ['ID', 'Nombre', 'Apellido', 'DNI', 'Correo', 'Cargo', 'Sede', 'Ciudad', 'Estado', 'Fecha Ingreso']);

            // Datos
            foreach ($empleados as $emp) {
                fputcsv($output, [
                    $emp['id_trabajador'],
                    $emp['nombre'],
                    $emp['apellido'],
                    $emp['dni'] ?? '',
                    $emp['correo'],
                    $emp['cargo'],
                    $emp['sede_nombre'],
                    $emp['ciudad_nombre'],
                    $emp['estado'] == 1 ? 'Activo' : 'Inactivo',
                    $emp['fecha_ingreso']
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            // En caso de error, podría redirigir o mostrar mensaje, pero en exportación directa es complicado.
            // Lo ideal sería loguear el error.
            die("Error al exportar: " . $e->getMessage());
        }
    }
}
?>
