<?php
require_once __DIR__ . "/../models/EmpleadoModel.php";
require_once __DIR__ . "/../models/SedeModelFixed.php";

class EmpleadoController {
    private $empleadoModel;
    private $sedeModel;

    public function __construct($conn) {
        $this->empleadoModel = new EmpleadoModel($conn);
        $this->sedeModel = new SedeModel($conn);
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
    // CREAR EMPLEADO
    // -------------------------------------------------
    public function create($data) {
        try {
            $id = $this->empleadoModel->create($data);
            
            return [
                "success" => true,
                "message" => "Empleado creado exitosamente",
                "id_empleado" => $id
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
}
?>
