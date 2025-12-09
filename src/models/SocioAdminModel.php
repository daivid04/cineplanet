<?php
/**
 * Modelo de administración para Socios
 * Gestiona operaciones CRUD completas con validaciones
 */

class SocioAdminModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    
    /**
     * Verifica si existe un correo (en usuario)
     */
    private function existsByCorreo($correo, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM usuario WHERE LOWER(correo) = LOWER(:correo)";
        if ($excludeId) {
            $sql .= " AND id_usuario != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Verifica si existe un documento
     */
    private function existsByDocumento($documento, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM socio WHERE documento = :documento";
        if ($excludeId) {
            $sql .= " AND id_usuario != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':documento', $documento, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Valida formato de correo
     */
    private function validarCorreo($correo) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'El formato del correo no es válido'];
        }
        return ['valid' => true];
    }

    /**
     * Valida documento (DNI peruano: 8 dígitos)
     */
    private function validarDocumento($documento) {
        if (!preg_match("/^[0-9]{8}$/", $documento)) {
            return ['valid' => false, 'message' => 'El documento debe tener exactamente 8 dígitos numéricos'];
        }
        return ['valid' => true];
    }

    /**
     * Valida que el tipo de socio exista y esté activo
     */
    private function validarTipoSocio($idTipoSocio) {
        $sql = "SELECT id_socio, nombre, estado FROM tipo_socio WHERE id_socio = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $idTipoSocio]);
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tipo) {
            return ['valid' => false, 'message' => 'El tipo de socio seleccionado no existe'];
        }
        
        if ($tipo['estado'] != 1) {
            return ['valid' => false, 'message' => 'El tipo de socio "' . $tipo['nombre'] . '" está inactivo'];
        }
        
        return ['valid' => true, 'tipo' => $tipo];
    }

    /**
     * Valida la contraseña
     */
    private function validarContrasena($contrasena) {
        if (strlen($contrasena) < 6) {
            return ['valid' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
        }
        return ['valid' => true];
    }

    /**
     * Valida género
     */
    private function validarGenero($genero) {
        $generosValidos = ['Masculino', 'Femenino', 'Otro'];
        if (!in_array($genero, $generosValidos)) {
            return ['valid' => false, 'message' => 'El género debe ser: Masculino, Femenino u Otro'];
        }
        return ['valid' => true];
    }

    /**
     * Valida fecha de nacimiento
     */
    private function validarFechaNacimiento($fecha) {
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $fecha)) {
            return ['valid' => false, 'message' => 'Formato de fecha inválido (use YYYY-MM-DD)'];
        }
        
        $fechaNac = strtotime($fecha);
        if (!$fechaNac) {
            return ['valid' => false, 'message' => 'La fecha de nacimiento no es válida'];
        }
        
        $hoy = strtotime('today');
        $edad = floor(($hoy - $fechaNac) / (365.25 * 24 * 60 * 60));
        
        if ($fechaNac > $hoy) {
            return ['valid' => false, 'message' => 'La fecha de nacimiento no puede ser futura'];
        }
        
        if ($edad < 13) {
            return ['valid' => false, 'message' => 'El socio debe tener al menos 13 años'];
        }
        
        if ($edad > 120) {
            return ['valid' => false, 'message' => 'La fecha de nacimiento parece incorrecta'];
        }
        
        return ['valid' => true, 'edad' => $edad];
    }

    // -------------------------------------------------
    // VERIFICAR DEPENDENCIAS (para eliminar)
    // -------------------------------------------------
    
    /**
     * Cuenta compras del socio
     */
    public function countCompras($idUsuario) {
        try {
            $sql = "SELECT COUNT(*) FROM compra WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $idUsuario]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Obtiene info de compras para mostrar
     */
    public function getComprasInfo($idUsuario) {
        try {
            $sql = "SELECT COUNT(*) as total, 
                           SUM(total) as monto_total,
                           MIN(fecha) as primera_compra,
                           MAX(fecha) as ultima_compra
                    FROM compra WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $idUsuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // CREAR SOCIO (usuario + socio en transacción)
    // -------------------------------------------------
    public function create($data) {
        // Validar campos requeridos
        $camposRequeridos = ['correo', 'nombre', 'apellido', 'genero', 'fecha_nacimiento', 'documento', 'id_tipo_socio', 'contrasena'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                return ['success' => false, 'message' => "El campo '$campo' es requerido"];
            }
        }

        // Validar correo
        $validCorreo = $this->validarCorreo($data['correo']);
        if (!$validCorreo['valid']) {
            return ['success' => false, 'message' => $validCorreo['message']];
        }

        if ($this->existsByCorreo($data['correo'])) {
            return ['success' => false, 'message' => 'Ya existe un usuario con ese correo'];
        }

        // Validar documento
        $validDoc = $this->validarDocumento($data['documento']);
        if (!$validDoc['valid']) {
            return ['success' => false, 'message' => $validDoc['message']];
        }

        if ($this->existsByDocumento($data['documento'])) {
            return ['success' => false, 'message' => 'Ya existe un socio con ese documento (DNI)'];
        }

        // Validar tipo de socio
        $validTipo = $this->validarTipoSocio($data['id_tipo_socio']);
        if (!$validTipo['valid']) {
            return ['success' => false, 'message' => $validTipo['message']];
        }

        // Validar contraseña
        $validPass = $this->validarContrasena($data['contrasena']);
        if (!$validPass['valid']) {
            return ['success' => false, 'message' => $validPass['message']];
        }

        // Validar género
        $validGenero = $this->validarGenero($data['genero']);
        if (!$validGenero['valid']) {
            return ['success' => false, 'message' => $validGenero['message']];
        }

        // Validar fecha de nacimiento
        $validFecha = $this->validarFechaNacimiento($data['fecha_nacimiento']);
        if (!$validFecha['valid']) {
            return ['success' => false, 'message' => $validFecha['message']];
        }

        // Validar nombre y apellido
        $nombre = trim($data['nombre']);
        $apellido = trim($data['apellido']);
        
        if (strlen($nombre) < 2 || strlen($nombre) > 50) {
            return ['success' => false, 'message' => 'El nombre debe tener entre 2 y 50 caracteres'];
        }
        
        if (strlen($apellido) < 2 || strlen($apellido) > 50) {
            return ['success' => false, 'message' => 'El apellido debe tener entre 2 y 50 caracteres'];
        }

        try {
            $this->conn->beginTransaction();

            // 1. Insertar en usuario
            $sqlUsuario = "INSERT INTO usuario (correo, estado) VALUES (:correo, 1)";
            $stmtUsuario = $this->conn->prepare($sqlUsuario);
            $stmtUsuario->execute([":correo" => trim($data['correo'])]);
            $idUsuario = $this->conn->lastInsertId();

            // 2. Hashear contraseña
            $contrasenaHash = password_hash($data['contrasena'], PASSWORD_DEFAULT);

            // 3. Insertar en socio
            $sqlSocio = "INSERT INTO socio (id_usuario, nombre, apellido, genero, fecha_nacimiento, documento, id_tipo_socio, contrasena)
                         VALUES (:id_usuario, :nombre, :apellido, :genero, :fecha_nacimiento, :documento, :id_tipo_socio, :contrasena)";
            $stmtSocio = $this->conn->prepare($sqlSocio);
            $stmtSocio->execute([
                ":id_usuario" => $idUsuario,
                ":nombre" => $nombre,
                ":apellido" => $apellido,
                ":genero" => $data['genero'],
                ":fecha_nacimiento" => $data['fecha_nacimiento'],
                ":documento" => $data['documento'],
                ":id_tipo_socio" => $data['id_tipo_socio'],
                ":contrasena" => $contrasenaHash
            ]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Socio creado correctamente',
                'id' => $idUsuario
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al crear socio: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER SOCIO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT s.id_usuario, s.nombre, s.apellido, s.genero, s.fecha_nacimiento, 
                           s.documento, s.id_tipo_socio, 
                           u.correo, u.estado,
                           ts.nombre as tipo_socio_nombre,
                           ts.desc_dulces, ts.desc_boleto, ts.puntos_por_sol
                    FROM socio s
                    INNER JOIN usuario u ON s.id_usuario = u.id_usuario
                    INNER JOIN tipo_socio ts ON s.id_tipo_socio = ts.id_socio
                    WHERE s.id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            $socio = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($socio) {
                // Calcular edad
                $fechaNac = strtotime($socio['fecha_nacimiento']);
                $hoy = strtotime('today');
                $socio['edad'] = floor(($hoy - $fechaNac) / (365.25 * 24 * 60 * 60));
            }
            
            return $socio;
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS SOCIOS
    // -------------------------------------------------
    public function getAll($soloActivos = false, $filtroTipo = null, $busqueda = null) {
        try {
            $sql = "SELECT s.id_usuario, s.nombre, s.apellido, s.genero, s.fecha_nacimiento, 
                           s.documento, s.id_tipo_socio,
                           u.correo, u.estado,
                           ts.nombre as tipo_socio_nombre
                    FROM socio s
                    INNER JOIN usuario u ON s.id_usuario = u.id_usuario
                    INNER JOIN tipo_socio ts ON s.id_tipo_socio = ts.id_socio";
            
            $conditions = [];
            $params = [];

            if ($soloActivos) {
                $conditions[] = "u.estado = 1";
            }

            if ($filtroTipo && is_numeric($filtroTipo)) {
                $conditions[] = "s.id_tipo_socio = :filtroTipo";
                $params[':filtroTipo'] = $filtroTipo;
            }

            if ($busqueda) {
                $conditions[] = "(s.nombre LIKE :busqueda OR s.apellido LIKE :busqueda OR s.documento LIKE :busqueda OR u.correo LIKE :busqueda)";
                $params[':busqueda'] = "%$busqueda%";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " ORDER BY s.id_usuario DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER TIPOS DE SOCIO (para selects)
    // -------------------------------------------------
    public function getTiposSocio($soloActivos = true) {
        try {
            $sql = "SELECT id_socio, nombre, desc_dulces, desc_boleto, puntos_por_sol, estado 
                    FROM tipo_socio";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $sql .= " ORDER BY nombre ASC";
            
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR SOCIO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        // Verificar que existe
        $socioActual = $this->getById($id);
        if (!$socioActual) {
            return ['success' => false, 'message' => 'No se encontró el socio'];
        }

        // Validar correo si se proporciona
        if (isset($data['correo']) && trim($data['correo']) !== "") {
            $validCorreo = $this->validarCorreo($data['correo']);
            if (!$validCorreo['valid']) {
                return ['success' => false, 'message' => $validCorreo['message']];
            }
            if ($this->existsByCorreo($data['correo'], $id)) {
                return ['success' => false, 'message' => 'Ya existe otro usuario con ese correo'];
            }
        }

        // Validar documento si se proporciona
        if (isset($data['documento']) && trim($data['documento']) !== "") {
            $validDoc = $this->validarDocumento($data['documento']);
            if (!$validDoc['valid']) {
                return ['success' => false, 'message' => $validDoc['message']];
            }
            if ($this->existsByDocumento($data['documento'], $id)) {
                return ['success' => false, 'message' => 'Ya existe otro socio con ese documento'];
            }
        }

        // Validar tipo de socio si se proporciona
        if (isset($data['id_tipo_socio']) && !empty($data['id_tipo_socio'])) {
            $validTipo = $this->validarTipoSocio($data['id_tipo_socio']);
            if (!$validTipo['valid']) {
                return ['success' => false, 'message' => $validTipo['message']];
            }
        }

        // Validar género si se proporciona
        if (isset($data['genero']) && !empty($data['genero'])) {
            $validGenero = $this->validarGenero($data['genero']);
            if (!$validGenero['valid']) {
                return ['success' => false, 'message' => $validGenero['message']];
            }
        }

        // Validar fecha de nacimiento si se proporciona
        if (isset($data['fecha_nacimiento']) && !empty($data['fecha_nacimiento'])) {
            $validFecha = $this->validarFechaNacimiento($data['fecha_nacimiento']);
            if (!$validFecha['valid']) {
                return ['success' => false, 'message' => $validFecha['message']];
            }
        }

        // Validar contraseña si se proporciona (opcional en update)
        if (isset($data['contrasena']) && trim($data['contrasena']) !== "") {
            $validPass = $this->validarContrasena($data['contrasena']);
            if (!$validPass['valid']) {
                return ['success' => false, 'message' => $validPass['message']];
            }
        }

        try {
            $this->conn->beginTransaction();

            // Actualizar usuario (correo)
            if (isset($data['correo']) && trim($data['correo']) !== "") {
                $sqlUsuario = "UPDATE usuario SET correo = :correo WHERE id_usuario = :id";
                $stmtUsuario = $this->conn->prepare($sqlUsuario);
                $stmtUsuario->execute([
                    ":correo" => trim($data['correo']),
                    ":id" => $id
                ]);
            }

            // Construir SQL de actualización para socio
            $campos = [];
            $params = [':id' => $id];

            if (isset($data['nombre']) && trim($data['nombre']) !== "") {
                $campos[] = "nombre = :nombre";
                $params[':nombre'] = trim($data['nombre']);
            }

            if (isset($data['apellido']) && trim($data['apellido']) !== "") {
                $campos[] = "apellido = :apellido";
                $params[':apellido'] = trim($data['apellido']);
            }

            if (isset($data['genero']) && !empty($data['genero'])) {
                $campos[] = "genero = :genero";
                $params[':genero'] = $data['genero'];
            }

            if (isset($data['fecha_nacimiento']) && !empty($data['fecha_nacimiento'])) {
                $campos[] = "fecha_nacimiento = :fecha_nacimiento";
                $params[':fecha_nacimiento'] = $data['fecha_nacimiento'];
            }

            if (isset($data['documento']) && !empty($data['documento'])) {
                $campos[] = "documento = :documento";
                $params[':documento'] = $data['documento'];
            }

            if (isset($data['id_tipo_socio']) && !empty($data['id_tipo_socio'])) {
                $campos[] = "id_tipo_socio = :id_tipo_socio";
                $params[':id_tipo_socio'] = $data['id_tipo_socio'];
            }

            if (isset($data['contrasena']) && trim($data['contrasena']) !== "") {
                $campos[] = "contrasena = :contrasena";
                $params[':contrasena'] = password_hash($data['contrasena'], PASSWORD_DEFAULT);
            }

            if (!empty($campos)) {
                $sqlSocio = "UPDATE socio SET " . implode(", ", $campos) . " WHERE id_usuario = :id";
                $stmtSocio = $this->conn->prepare($sqlSocio);
                $stmtSocio->execute($params);
            }

            $this->conn->commit();

            return ['success' => true, 'message' => 'Socio actualizado correctamente'];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO DEL USUARIO
    // -------------------------------------------------
    public function toggleEstado($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $socio = $this->getById($id);
        if (!$socio) {
            return ['success' => false, 'message' => 'No se encontró el socio'];
        }

        $nuevoEstado = $socio['estado'] == 1 ? 0 : 1;

        try {
            $sql = "UPDATE usuario SET estado = :estado WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":estado" => $nuevoEstado,
                ":id" => $id
            ]);

            $estadoTexto = $nuevoEstado == 1 ? 'activado' : 'desactivado';
            return [
                'success' => true,
                'message' => "Socio $estadoTexto correctamente",
                'nuevoEstado' => $nuevoEstado
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR SOCIO
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $socio = $this->getById($id);
        if (!$socio) {
            return ['success' => false, 'message' => 'No se encontró el socio'];
        }

        // Verificar compras
        $compras = $this->countCompras($id);
        if ($compras > 0) {
            return [
                'success' => false,
                'message' => "No se puede eliminar: El socio tiene $compras compra(s) registrada(s). Considere desactivarlo en lugar de eliminarlo."
            ];
        }

        try {
            // Eliminar socio (CASCADE eliminará el usuario)
            // O podemos eliminar el usuario y CASCADE eliminará el socio
            $sql = "DELETE FROM usuario WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return [
                'success' => true,
                'message' => 'Socio eliminado correctamente'
            ];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar: El socio tiene registros relacionados'
                ];
            }
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // RESETEAR CONTRASEÑA
    // -------------------------------------------------
    public function resetPassword($id, $nuevaContrasena) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $validPass = $this->validarContrasena($nuevaContrasena);
        if (!$validPass['valid']) {
            return ['success' => false, 'message' => $validPass['message']];
        }

        try {
            $contrasenaHash = password_hash($nuevaContrasena, PASSWORD_DEFAULT);
            $sql = "UPDATE socio SET contrasena = :contrasena WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":contrasena" => $contrasenaHash,
                ":id" => $id
            ]);

            return ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ESTADÍSTICAS
    // -------------------------------------------------
    public function getEstadisticas() {
        try {
            $stats = [];

            // Total socios
            $sql = "SELECT COUNT(*) as total FROM socio";
            $stmt = $this->conn->query($sql);
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Socios activos
            $sql = "SELECT COUNT(*) as activos FROM socio s 
                    INNER JOIN usuario u ON s.id_usuario = u.id_usuario 
                    WHERE u.estado = 1";
            $stmt = $this->conn->query($sql);
            $stats['activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['activos'];

            // Por tipo de socio
            $sql = "SELECT ts.nombre, COUNT(s.id_usuario) as cantidad 
                    FROM tipo_socio ts 
                    LEFT JOIN socio s ON ts.id_socio = s.id_tipo_socio 
                    GROUP BY ts.id_socio, ts.nombre";
            $stmt = $this->conn->query($sql);
            $stats['por_tipo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Por género
            $sql = "SELECT genero, COUNT(*) as cantidad FROM socio GROUP BY genero";
            $stmt = $this->conn->query($sql);
            $stats['por_genero'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
}
