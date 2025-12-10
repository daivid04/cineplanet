<?php
/**
 * Modelo de administración para Invitados
 * Gestiona operaciones CRUD completas con validaciones
 */

class InvitadoAdminModel {
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
     * Valida formato de correo
     */
    private function validarCorreo($correo) {
        if (empty($correo)) {
            return ['valid' => true]; // Correo opcional para invitados
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'El formato del correo no es válido'];
        }
        return ['valid' => true];
    }

    /**
     * Valida nombre
     */
    private function validarNombre($nombre) {
        if (empty(trim($nombre))) {
            return ['valid' => false, 'message' => 'El nombre es requerido'];
        }
        if (strlen(trim($nombre)) < 2) {
            return ['valid' => false, 'message' => 'El nombre debe tener al menos 2 caracteres'];
        }
        if (strlen(trim($nombre)) > 100) {
            return ['valid' => false, 'message' => 'El nombre no puede exceder 100 caracteres'];
        }
        return ['valid' => true];
    }

    // -------------------------------------------------
    // VERIFICAR DEPENDENCIAS (para eliminar)
    // -------------------------------------------------
    
    /**
     * Cuenta compras del invitado
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
    // CREAR INVITADO (usuario + invitado en transacción)
    // -------------------------------------------------
    public function create($data) {
        // Validar nombre
        $validNombre = $this->validarNombre($data['nombre'] ?? '');
        if (!$validNombre['valid']) {
            return ['success' => false, 'message' => $validNombre['message']];
        }

        // El correo es opcional para invitados, pero si se proporciona debe ser válido
        $correo = isset($data['correo']) && !empty(trim($data['correo'])) ? trim($data['correo']) : null;
        
        if ($correo) {
            $validCorreo = $this->validarCorreo($correo);
            if (!$validCorreo['valid']) {
                return ['success' => false, 'message' => $validCorreo['message']];
            }
            if ($this->existsByCorreo($correo)) {
                return ['success' => false, 'message' => 'Ya existe un usuario con ese correo'];
            }
        }

        try {
            $this->conn->beginTransaction();

            // 1. Insertar en usuario
            $sqlUsuario = "INSERT INTO usuario (correo, estado) VALUES (:correo, 1)";
            $stmtUsuario = $this->conn->prepare($sqlUsuario);
            $stmtUsuario->execute([":correo" => $correo]);
            $idUsuario = $this->conn->lastInsertId();

            // 2. Insertar en invitado
            $sqlInvitado = "INSERT INTO invitado (id_usuario, nombre) VALUES (:id_usuario, :nombre)";
            $stmtInvitado = $this->conn->prepare($sqlInvitado);
            $stmtInvitado->execute([
                ":id_usuario" => $idUsuario,
                ":nombre" => trim($data['nombre'])
            ]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Invitado creado correctamente',
                'id' => $idUsuario
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al crear invitado: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER INVITADO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT i.id_usuario, i.nombre,
                           u.correo, u.estado
                    FROM invitado i
                    INNER JOIN usuario u ON i.id_usuario = u.id_usuario
                    WHERE i.id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS INVITADOS
    // -------------------------------------------------
    public function getAll($soloActivos = false, $busqueda = null) {
        try {
            $sql = "SELECT i.id_usuario, i.nombre,
                           u.correo, u.estado
                    FROM invitado i
                    INNER JOIN usuario u ON i.id_usuario = u.id_usuario";
            
            $conditions = [];
            $params = [];

            if ($soloActivos) {
                $conditions[] = "u.estado = 1";
            }

            if ($busqueda) {
                $conditions[] = "(i.nombre LIKE :busqueda OR u.correo LIKE :busqueda)";
                $params[':busqueda'] = "%$busqueda%";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " ORDER BY i.id_usuario DESC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR INVITADO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        // Verificar que existe
        $invitadoActual = $this->getById($id);
        if (!$invitadoActual) {
            return ['success' => false, 'message' => 'No se encontró el invitado'];
        }

        // Validar nombre si se proporciona
        if (isset($data['nombre'])) {
            $validNombre = $this->validarNombre($data['nombre']);
            if (!$validNombre['valid']) {
                return ['success' => false, 'message' => $validNombre['message']];
            }
        }

        // Validar correo si se proporciona
        $correo = isset($data['correo']) ? trim($data['correo']) : null;
        if ($correo && !empty($correo)) {
            $validCorreo = $this->validarCorreo($correo);
            if (!$validCorreo['valid']) {
                return ['success' => false, 'message' => $validCorreo['message']];
            }
            if ($this->existsByCorreo($correo, $id)) {
                return ['success' => false, 'message' => 'Ya existe otro usuario con ese correo'];
            }
        }

        try {
            $this->conn->beginTransaction();

            // Actualizar usuario (correo)
            if (isset($data['correo'])) {
                $sqlUsuario = "UPDATE usuario SET correo = :correo WHERE id_usuario = :id";
                $stmtUsuario = $this->conn->prepare($sqlUsuario);
                $stmtUsuario->execute([
                    ":correo" => empty($correo) ? null : $correo,
                    ":id" => $id
                ]);
            }

            // Actualizar invitado (nombre)
            if (isset($data['nombre']) && !empty(trim($data['nombre']))) {
                $sqlInvitado = "UPDATE invitado SET nombre = :nombre WHERE id_usuario = :id";
                $stmtInvitado = $this->conn->prepare($sqlInvitado);
                $stmtInvitado->execute([
                    ":nombre" => trim($data['nombre']),
                    ":id" => $id
                ]);
            }

            $this->conn->commit();

            return ['success' => true, 'message' => 'Invitado actualizado correctamente'];
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

        $invitado = $this->getById($id);
        if (!$invitado) {
            return ['success' => false, 'message' => 'No se encontró el invitado'];
        }

        $nuevoEstado = $invitado['estado'] == 1 ? 0 : 1;

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
                'message' => "Invitado $estadoTexto correctamente",
                'nuevoEstado' => $nuevoEstado
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR INVITADO
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $invitado = $this->getById($id);
        if (!$invitado) {
            return ['success' => false, 'message' => 'No se encontró el invitado'];
        }

        // Verificar compras
        $compras = $this->countCompras($id);
        if ($compras > 0) {
            return [
                'success' => false,
                'message' => "No se puede eliminar: El invitado tiene $compras compra(s) registrada(s). Considere desactivarlo en lugar de eliminarlo."
            ];
        }

        try {
            // Eliminar usuario (CASCADE eliminará el invitado)
            $sql = "DELETE FROM usuario WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return [
                'success' => true,
                'message' => 'Invitado eliminado correctamente'
            ];
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                return [
                    'success' => false,
                    'message' => 'No se puede eliminar: El invitado tiene registros relacionados'
                ];
            }
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ESTADÍSTICAS
    // -------------------------------------------------
    public function getEstadisticas() {
        try {
            $stats = [];

            // Total invitados
            $sql = "SELECT COUNT(*) as total FROM invitado";
            $stmt = $this->conn->query($sql);
            $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Invitados activos
            $sql = "SELECT COUNT(*) as activos FROM invitado i 
                    INNER JOIN usuario u ON i.id_usuario = u.id_usuario 
                    WHERE u.estado = 1";
            $stmt = $this->conn->query($sql);
            $stats['activos'] = $stmt->fetch(PDO::FETCH_ASSOC)['activos'];

            // Con correo registrado
            $sql = "SELECT COUNT(*) as con_correo FROM invitado i 
                    INNER JOIN usuario u ON i.id_usuario = u.id_usuario 
                    WHERE u.correo IS NOT NULL AND u.correo != ''";
            $stmt = $this->conn->query($sql);
            $stats['con_correo'] = $stmt->fetch(PDO::FETCH_ASSOC)['con_correo'];

            // Con compras
            $sql = "SELECT COUNT(DISTINCT i.id_usuario) as con_compras 
                    FROM invitado i 
                    INNER JOIN compra c ON i.id_usuario = c.id_usuario";
            $stmt = $this->conn->query($sql);
            $stats['con_compras'] = $stmt->fetch(PDO::FETCH_ASSOC)['con_compras'];

            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // CONVERTIR A SOCIO
    // -------------------------------------------------
    public function convertirASocio($id, $datosSocio) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $invitado = $this->getById($id);
        if (!$invitado) {
            return ['success' => false, 'message' => 'No se encontró el invitado'];
        }

        // Validar datos necesarios para socio
        $camposRequeridos = ['apellido', 'genero', 'fecha_nacimiento', 'documento', 'id_tipo_socio', 'contrasena'];
        foreach ($camposRequeridos as $campo) {
            if (!isset($datosSocio[$campo]) || trim($datosSocio[$campo]) === "") {
                return ['success' => false, 'message' => "El campo '$campo' es requerido para la conversión"];
            }
        }

        try {
            $this->conn->beginTransaction();

            // 1. Eliminar de invitado
            $sqlDelete = "DELETE FROM invitado WHERE id_usuario = :id";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([":id" => $id]);

            // 2. Hashear contraseña
            $contrasenaHash = password_hash($datosSocio['contrasena'], PASSWORD_DEFAULT);

            // 3. Insertar en socio
            $sqlSocio = "INSERT INTO socio (id_usuario, nombre, apellido, genero, fecha_nacimiento, documento, id_tipo_socio, contrasena)
                         VALUES (:id_usuario, :nombre, :apellido, :genero, :fecha_nacimiento, :documento, :id_tipo_socio, :contrasena)";
            $stmtSocio = $this->conn->prepare($sqlSocio);
            $stmtSocio->execute([
                ":id_usuario" => $id,
                ":nombre" => $invitado['nombre'],
                ":apellido" => trim($datosSocio['apellido']),
                ":genero" => $datosSocio['genero'],
                ":fecha_nacimiento" => $datosSocio['fecha_nacimiento'],
                ":documento" => $datosSocio['documento'],
                ":id_tipo_socio" => $datosSocio['id_tipo_socio'],
                ":contrasena" => $contrasenaHash
            ]);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Invitado convertido a socio correctamente'
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error al convertir: ' . $e->getMessage()];
        }
    }
}
