<?php

class UsuarioAdminModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
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

    private function validarCorreo($correo) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'message' => 'El formato del correo no es válido'];
        }
        return ['valid' => true];
    }

    // -------------------------------------------------
    // VERIFICAR DEPENDENCIAS (para eliminar)
    // -------------------------------------------------
    public function countCompras($id) {
        try {
            $sql = "SELECT COUNT(*) FROM compra WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function esSocio($id) {
        try {
            $sql = "SELECT COUNT(*) FROM socio WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function esInvitado($id) {
        try {
            $sql = "SELECT COUNT(*) FROM invitado WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // -------------------------------------------------
    // OBTENER TIPO DE USUARIO
    // -------------------------------------------------
    public function getTipoUsuario($id) {
        if ($this->esSocio($id)) {
            return 'Socio';
        } elseif ($this->esInvitado($id)) {
            return 'Invitado';
        }
        return 'Sin tipo';
    }

    // -------------------------------------------------
    // CREAR USUARIO
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["correo"]) || trim($data["correo"]) === "") {
            return ['success' => false, 'message' => 'El correo es requerido'];
        }

        $validacion = $this->validarCorreo($data["correo"]);
        if (!$validacion['valid']) {
            return ['success' => false, 'message' => $validacion['message']];
        }

        if ($this->existsByCorreo($data["correo"])) {
            return ['success' => false, 'message' => 'Ya existe un usuario con ese correo'];
        }

        try {
            $sql = "INSERT INTO usuario (correo, estado) VALUES (:correo, 1)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":correo" => $data["correo"]
            ]);

            return [
                'success' => true,
                'message' => 'Usuario creado correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER USUARIO POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT u.id_usuario, u.correo, u.estado,
                           CASE 
                               WHEN s.id_usuario IS NOT NULL THEN 'Socio'
                               WHEN i.id_usuario IS NOT NULL THEN 'Invitado'
                               ELSE 'Sin tipo'
                           END as tipo_usuario,
                           s.nombre as socio_nombre, s.apellido as socio_apellido,
                           i.nombre as invitado_nombre,
                           ts.nombre as tipo_socio_nombre
                    FROM usuario u
                    LEFT JOIN socio s ON u.id_usuario = s.id_usuario
                    LEFT JOIN invitado i ON u.id_usuario = i.id_usuario
                    LEFT JOIN tipo_socio ts ON s.id_tipo_socio = ts.id_socio
                    WHERE u.id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS USUARIOS
    // -------------------------------------------------
    public function getAll($soloActivos = false, $filtroTipo = null) {
        try {
            $sql = "SELECT u.id_usuario, u.correo, u.estado,
                           CASE 
                               WHEN s.id_usuario IS NOT NULL THEN 'Socio'
                               WHEN i.id_usuario IS NOT NULL THEN 'Invitado'
                               ELSE 'Sin tipo'
                           END as tipo_usuario,
                           COALESCE(CONCAT(s.nombre, ' ', s.apellido), i.nombre, '-') as nombre_completo,
                           ts.nombre as tipo_socio_nombre
                    FROM usuario u
                    LEFT JOIN socio s ON u.id_usuario = s.id_usuario
                    LEFT JOIN invitado i ON u.id_usuario = i.id_usuario
                    LEFT JOIN tipo_socio ts ON s.id_tipo_socio = ts.id_socio";
            
            $conditions = [];
            $params = [];

            if ($soloActivos) {
                $conditions[] = "u.estado = 1";
            }

            if ($filtroTipo === 'socio') {
                $conditions[] = "s.id_usuario IS NOT NULL";
            } elseif ($filtroTipo === 'invitado') {
                $conditions[] = "i.id_usuario IS NOT NULL";
            } elseif ($filtroTipo === 'sin_tipo') {
                $conditions[] = "s.id_usuario IS NULL AND i.id_usuario IS NULL";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " ORDER BY u.id_usuario DESC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR USUARIO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        if (isset($data["correo"])) {
            $validacion = $this->validarCorreo($data["correo"]);
            if (!$validacion['valid']) {
                return ['success' => false, 'message' => $validacion['message']];
            }

            if ($this->existsByCorreo($data["correo"], $id)) {
                return ['success' => false, 'message' => 'Ya existe otro usuario con ese correo'];
            }
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["correo"])) {
                $campos[] = "correo = :correo";
                $params[":correo"] = $data["correo"];
            }
            
            if (isset($data["estado"])) {
                $campos[] = "estado = :estado";
                $params[":estado"] = $data["estado"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE usuario SET " . implode(", ", $campos) . " WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el usuario o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR USUARIO (Soft Delete)
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        try {
            // Verificar si tiene compras
            $compras = $this->countCompras($id);
            if ($compras > 0) {
                return [
                    'success' => false, 
                    'message' => "No se puede eliminar el usuario porque tiene {$compras} compra(s) registrada(s)"
                ];
            }

            // Soft delete
            $sql = "UPDATE usuario SET estado = 0 WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Usuario eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el usuario'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        try {
            // Si se va a desactivar, verificar compras pendientes
            if ($estado == 0) {
                $compras = $this->countCompras($id);
                if ($compras > 0) {
                    return [
                        'success' => true, 
                        'message' => "Usuario desactivado. Nota: tiene {$compras} compra(s) registrada(s)",
                        'warning' => true
                    ];
                }
            }

            $sql = "UPDATE usuario SET estado = :estado WHERE id_usuario = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":estado" => $estado]);

            $estadoTexto = $estado == 1 ? 'activado' : 'desactivado';
            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Usuario {$estadoTexto} correctamente"];
            } else {
                return ['success' => false, 'message' => 'No se encontró el usuario'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR USUARIOS
    // -------------------------------------------------
    public function count($soloActivos = false, $filtroTipo = null) {
        try {
            $sql = "SELECT COUNT(*) FROM usuario u
                    LEFT JOIN socio s ON u.id_usuario = s.id_usuario
                    LEFT JOIN invitado i ON u.id_usuario = i.id_usuario";
            
            $conditions = [];
            
            if ($soloActivos) {
                $conditions[] = "u.estado = 1";
            }

            if ($filtroTipo === 'socio') {
                $conditions[] = "s.id_usuario IS NOT NULL";
            } elseif ($filtroTipo === 'invitado') {
                $conditions[] = "i.id_usuario IS NOT NULL";
            } elseif ($filtroTipo === 'sin_tipo') {
                $conditions[] = "s.id_usuario IS NULL AND i.id_usuario IS NULL";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $stmt = $this->conn->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // -------------------------------------------------
    // BUSCAR USUARIOS
    // -------------------------------------------------
    public function search($termino) {
        try {
            $sql = "SELECT u.id_usuario, u.correo, u.estado,
                           CASE 
                               WHEN s.id_usuario IS NOT NULL THEN 'Socio'
                               WHEN i.id_usuario IS NOT NULL THEN 'Invitado'
                               ELSE 'Sin tipo'
                           END as tipo_usuario,
                           COALESCE(CONCAT(s.nombre, ' ', s.apellido), i.nombre, '-') as nombre_completo
                    FROM usuario u
                    LEFT JOIN socio s ON u.id_usuario = s.id_usuario
                    LEFT JOIN invitado i ON u.id_usuario = i.id_usuario
                    WHERE u.correo LIKE :termino
                       OR s.nombre LIKE :termino
                       OR s.apellido LIKE :termino
                       OR i.nombre LIKE :termino
                    ORDER BY u.id_usuario DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":termino" => "%{$termino}%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER DETALLES COMPLETOS (para editar)
    // -------------------------------------------------
    public function getDetallesCompletos($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $usuario = $this->getById($id);
            if (!$usuario) {
                return null;
            }

            // Agregar conteo de compras
            $usuario['total_compras'] = $this->countCompras($id);

            // Si es socio, obtener más detalles
            if ($usuario['tipo_usuario'] === 'Socio') {
                $sql = "SELECT s.*, ts.nombre as tipo_socio_nombre, 
                               ts.desc_dulces, ts.desc_boleto, ts.puntos_por_sol
                        FROM socio s
                        INNER JOIN tipo_socio ts ON s.id_tipo_socio = ts.id_socio
                        WHERE s.id_usuario = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([":id" => $id]);
                $usuario['socio'] = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Si es invitado, obtener detalles
            if ($usuario['tipo_usuario'] === 'Invitado') {
                $sql = "SELECT * FROM invitado WHERE id_usuario = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([":id" => $id]);
                $usuario['invitado'] = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return $usuario;
        } catch (PDOException $e) {
            return null;
        }
    }
}
