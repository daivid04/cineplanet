<?php

class EmpleadoModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS EMPLEADOS
    // -------------------------------------------------
    // -------------------------------------------------
    // OBTENER TODOS LOS EMPLEADOS
    // -------------------------------------------------
    public function getAll() {
        try {
            $sql = "SELECT 
                        t.id_trabajador,
                        t.nombre,
                        t.apellido,
                        t.correo,
                        t.tipo AS cargo,
                        t.estado,
                        t.fecha_ingreso,
                        s.nombre AS sede_nombre,
                        c.nombre AS ciudad_nombre
                    FROM trabajador t
                    INNER JOIN sede s ON t.id_sede = s.id_sede
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    ORDER BY t.nombre ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los empleados: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER EMPLEADOS FILTRADOS Y PAGINADOS
    // -------------------------------------------------
    public function getAllFiltered($filters = [], $limit = 10, $offset = 0) {
        try {
            $where = [];
            $params = [];

            if (!empty($filters['sede'])) {
                $where[] = "t.id_sede = :sede";
                $params[':sede'] = $filters['sede'];
            }

            if (!empty($filters['cargo'])) {
                $where[] = "t.tipo = :cargo";
                $params[':cargo'] = $filters['cargo'];
            }

            if (isset($filters['estado']) && $filters['estado'] !== '') {
                $where[] = "t.estado = :estado";
                $params[':estado'] = $filters['estado'];
            }

            if (!empty($filters['search'])) {
                $where[] = "(t.nombre LIKE :search OR t.apellido LIKE :search OR t.dni LIKE :search)";
                $params[':search'] = "%" . $filters['search'] . "%";
            }

            $whereSql = "";
            if (!empty($where)) {
                $whereSql = "WHERE " . implode(" AND ", $where);
            }

            // Query para contar total
            $sqlCount = "SELECT COUNT(*) as total FROM trabajador t $whereSql";
            $stmtCount = $this->conn->prepare($sqlCount);
            $stmtCount->execute($params);
            $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

            // Query para obtener datos
            $sql = "SELECT 
                        t.id_trabajador,
                        t.nombre,
                        t.apellido,
                        t.correo,
                        t.tipo AS cargo,
                        t.estado,
                        t.fecha_ingreso,
                        s.nombre AS sede_nombre,
                        c.nombre AS ciudad_nombre
                    FROM trabajador t
                    INNER JOIN sede s ON t.id_sede = s.id_sede
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    $whereSql
                    ORDER BY t.nombre ASC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();

            return [
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'total' => $total
            ];
        } catch (PDOException $e) {
            throw new Exception("Error al obtener empleados filtrados: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER CARGOS (DISTINCT)
    // -------------------------------------------------
    public function getCargos() {
        try {
            $sql = "SELECT DISTINCT tipo as cargo FROM trabajador ORDER BY tipo ASC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener cargos: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER ESTADÍSTICAS DEL DASHBOARD
    // -------------------------------------------------
    public function getStats() {
        try {
            $stats = [
                "total_empleados" => 0,
                "nuevas_contrataciones" => 0,
                "turnos_activos" => 0,
                "ausencias" => 0
            ];

            // Total Empleados
            $sqlTotal = "SELECT COUNT(*) as total FROM trabajador WHERE estado = 1";
            $stmtTotal = $this->conn->query($sqlTotal);
            $stats["total_empleados"] = $stmtTotal->fetch(PDO::FETCH_ASSOC)["total"];

            // Nuevas Contrataciones (Simulado: últimos 30 días - asumiendo que id más alto es más reciente si no hay fecha_ingreso)
            // Como no hay fecha de ingreso, simularemos un número o usaremos una lógica simple
            $stats["nuevas_contrataciones"] = 5; // Mock data for now as schema lacks hire date

            // Turnos Activos Hoy
            // Unir dia_laborable y horario_trabajador podría ser complejo sin fechas específicas en dia_laborable (parece ser genérico)
            // Asumiremos un count de dia_laborable por ahora
            $sqlTurnos = "SELECT COUNT(*) as total FROM dia_laborable";
            $stmtTurnos = $this->conn->query($sqlTurnos);
            $stats["turnos_activos"] = $stmtTurnos->fetch(PDO::FETCH_ASSOC)["total"];

            // Ausencias (Mock data)
            $stats["ausencias"] = 2;

            return $stats;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function validarEmpleado($data, $modo = "insertar") {
        $requeridos = ["nombre", "apellido", "correo", "dni", "tipo", "id_sede"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        if (isset($data["correo"]) && !filter_var($data["correo"], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Correo electrónico inválido.");
        }

        if (isset($data["dni"]) && !preg_match("/^\d{8}$/", $data["dni"])) {
            throw new Exception("DNI inválido. Debe tener 8 dígitos.");
        }

        return true;
    }

    // -------------------------------------------------
    // CREAR EMPLEADO
    // -------------------------------------------------
    public function create($data) {
        $this->validarEmpleado($data, "insertar");

        try {
            $sql = "INSERT INTO trabajador (nombre, apellido, correo, numero, dni, tipo, id_sede, estado, fecha_ingreso) 
                    VALUES (:nombre, :apellido, :correo, :numero, :dni, :tipo, :id_sede, :estado, :fecha_ingreso)";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":nombre" => $data["nombre"],
                ":apellido" => $data["apellido"],
                ":correo" => $data["correo"],
                ":numero" => $data["numero"] ?? '',
                ":dni" => $data["dni"],
                ":tipo" => $data["tipo"],
                ":id_sede" => $data["id_sede"],
                ":estado" => isset($data["estado"]) ? $data["estado"] : 1,
                ":fecha_ingreso" => !empty($data["fecha_ingreso"]) ? $data["fecha_ingreso"] : date('Y-m-d')
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error al crear el empleado: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR EMPLEADO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }
        $this->validarEmpleado($data, "actualizar");

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["nombre"])) { $campos[] = "nombre = :nombre"; $params[":nombre"] = $data["nombre"]; }
            if (isset($data["apellido"])) { $campos[] = "apellido = :apellido"; $params[":apellido"] = $data["apellido"]; }
            if (isset($data["correo"])) { $campos[] = "correo = :correo"; $params[":correo"] = $data["correo"]; }
            if (isset($data["numero"])) { $campos[] = "numero = :numero"; $params[":numero"] = $data["numero"]; }
            if (isset($data["dni"])) { $campos[] = "dni = :dni"; $params[":dni"] = $data["dni"]; }
            if (isset($data["tipo"])) { $campos[] = "tipo = :tipo"; $params[":tipo"] = $data["tipo"]; }
            if (isset($data["id_sede"])) { $campos[] = "id_sede = :id_sede"; $params[":id_sede"] = $data["id_sede"]; }
            if (isset($data["estado"])) { $campos[] = "estado = :estado"; $params[":estado"] = $data["estado"]; }
            if (isset($data["fecha_ingreso"])) { $campos[] = "fecha_ingreso = :fecha_ingreso"; $params[":fecha_ingreso"] = $data["fecha_ingreso"]; }

            if (empty($campos)) {
                return false; // Nada que actualizar
            }

            $sql = "UPDATE trabajador SET " . implode(", ", $campos) . " WHERE id_trabajador = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el empleado: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER EMPLEADO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        try {
            $sql = "SELECT 
                        t.id_trabajador,
                        t.nombre,
                        t.apellido,
                        t.correo,
                        t.numero,
                        t.dni,
                        t.tipo AS cargo,
                        t.estado,
                        t.fecha_ingreso,
                        t.id_sede,
                        s.nombre AS sede_nombre,
                        c.nombre AS ciudad_nombre
                    FROM trabajador t
                    INNER JOIN sede s ON t.id_sede = s.id_sede
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    WHERE t.id_trabajador = :id";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el empleado: " . $e->getMessage());
        }
    }
}
?>
