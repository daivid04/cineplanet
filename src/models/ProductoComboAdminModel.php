<?php
/**
 * Modelo de administración para Producto_Combo
 * Gestiona la relación entre combos y productos de sede (inventario)
 * Un combo puede contener múltiples productos
 */

class ProductoComboAdminModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    
    /**
     * Verifica si el combo existe
     */
    private function comboExists($idCombo) {
        $sql = "SELECT id_combo, nombre, estado FROM combos WHERE id_combo = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $idCombo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si el producto_sede existe
     */
    private function productoSedeExists($idProductoSede) {
        $sql = "SELECT ps.id_producto_sede, p.nombre as producto_nombre, s.nombre as sede_nombre, ps.stock
                FROM producto_sede ps
                INNER JOIN producto p ON ps.id_producto = p.id_producto
                INNER JOIN sede s ON ps.id_sede = s.id_sede
                WHERE ps.id_producto_sede = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $idProductoSede]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si ya existe la relación combo-producto_sede
     */
    private function relacionExists($idCombo, $idProductoSede, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM producto_combo 
                WHERE id_combo = :id_combo AND id_producto_sede = :id_producto_sede";
        if ($excludeId) {
            $sql .= " AND id_productos_combos != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $params = [
            ':id_combo' => $idCombo,
            ':id_producto_sede' => $idProductoSede
        ];
        if ($excludeId) {
            $params[':excludeId'] = $excludeId;
        }
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    // -------------------------------------------------
    // CREAR RELACIÓN PRODUCTO-COMBO
    // -------------------------------------------------
    public function create($data) {
        // Validar campos requeridos
        if (!isset($data['id_combo']) || empty($data['id_combo'])) {
            return ['success' => false, 'message' => 'El combo es requerido'];
        }
        
        if (!isset($data['id_producto_sede']) || empty($data['id_producto_sede'])) {
            return ['success' => false, 'message' => 'El producto es requerido'];
        }

        // Validar que el combo existe
        $combo = $this->comboExists($data['id_combo']);
        if (!$combo) {
            return ['success' => false, 'message' => 'El combo seleccionado no existe'];
        }

        // Validar que el producto_sede existe
        $productoSede = $this->productoSedeExists($data['id_producto_sede']);
        if (!$productoSede) {
            return ['success' => false, 'message' => 'El producto seleccionado no existe en el inventario'];
        }

        // Verificar si ya existe la relación (opcional: permitir duplicados para cantidad)
        // En este caso, la BD permite duplicados (ej: 2 gaseosas en un combo)
        // Si no se quiere duplicados, descomentar:
        /*
        if ($this->relacionExists($data['id_combo'], $data['id_producto_sede'])) {
            return ['success' => false, 'message' => 'Este producto ya está asignado a este combo'];
        }
        */

        try {
            $sql = "INSERT INTO producto_combo (id_combo, id_producto_sede) VALUES (:id_combo, :id_producto_sede)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id_combo' => $data['id_combo'],
                ':id_producto_sede' => $data['id_producto_sede']
            ]);

            return [
                'success' => true,
                'message' => 'Producto agregado al combo correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CREAR MÚLTIPLES RELACIONES (asignar varios productos a un combo)
    // -------------------------------------------------
    public function createMultiple($idCombo, $productosSedeIds) {
        if (!is_array($productosSedeIds) || empty($productosSedeIds)) {
            return ['success' => false, 'message' => 'Debe seleccionar al menos un producto'];
        }

        $combo = $this->comboExists($idCombo);
        if (!$combo) {
            return ['success' => false, 'message' => 'El combo seleccionado no existe'];
        }

        $insertados = 0;
        $errores = [];

        try {
            $this->conn->beginTransaction();

            foreach ($productosSedeIds as $idProductoSede) {
                $productoSede = $this->productoSedeExists($idProductoSede);
                if (!$productoSede) {
                    $errores[] = "Producto ID $idProductoSede no encontrado";
                    continue;
                }

                $sql = "INSERT INTO producto_combo (id_combo, id_producto_sede) VALUES (:id_combo, :id_producto_sede)";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([
                    ':id_combo' => $idCombo,
                    ':id_producto_sede' => $idProductoSede
                ]);
                $insertados++;
            }

            $this->conn->commit();

            if ($insertados > 0) {
                $msg = "$insertados producto(s) agregado(s) al combo";
                if (!empty($errores)) {
                    $msg .= ". Errores: " . implode(", ", $errores);
                }
                return ['success' => true, 'message' => $msg, 'insertados' => $insertados];
            } else {
                return ['success' => false, 'message' => 'No se pudo agregar ningún producto'];
            }
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER RELACIÓN POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT pc.id_productos_combos, pc.id_combo, pc.id_producto_sede,
                           c.nombre as combo_nombre, c.precio as combo_precio,
                           p.nombre as producto_nombre, p.precio_unitario,
                           s.nombre as sede_nombre, ps.stock
                    FROM producto_combo pc
                    INNER JOIN combos c ON pc.id_combo = c.id_combo
                    INNER JOIN producto_sede ps ON pc.id_producto_sede = ps.id_producto_sede
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    WHERE pc.id_productos_combos = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER PRODUCTOS DE UN COMBO
    // -------------------------------------------------
    public function getProductosByCombo($idCombo) {
        try {
            $sql = "SELECT pc.id_productos_combos, pc.id_combo, pc.id_producto_sede,
                           p.nombre as producto_nombre, p.precio_unitario,
                           s.nombre as sede_nombre, ps.stock,
                           p.id_producto
                    FROM producto_combo pc
                    INNER JOIN producto_sede ps ON pc.id_producto_sede = ps.id_producto_sede
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    WHERE pc.id_combo = :id_combo
                    ORDER BY p.nombre ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_combo" => $idCombo]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS (agrupados por combo)
    // -------------------------------------------------
    public function getAll($filtroCombo = null, $filtroSede = null) {
        try {
            $sql = "SELECT pc.id_productos_combos, pc.id_combo, pc.id_producto_sede,
                           c.nombre as combo_nombre, c.precio as combo_precio, c.estado as combo_estado,
                           p.nombre as producto_nombre, p.precio_unitario,
                           s.nombre as sede_nombre, s.id_sede, ps.stock
                    FROM producto_combo pc
                    INNER JOIN combos c ON pc.id_combo = c.id_combo
                    INNER JOIN producto_sede ps ON pc.id_producto_sede = ps.id_producto_sede
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede";
            
            $conditions = [];
            $params = [];

            if ($filtroCombo && is_numeric($filtroCombo)) {
                $conditions[] = "pc.id_combo = :filtroCombo";
                $params[':filtroCombo'] = $filtroCombo;
            }

            if ($filtroSede && is_numeric($filtroSede)) {
                $conditions[] = "s.id_sede = :filtroSede";
                $params[':filtroSede'] = $filtroSede;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " ORDER BY c.nombre ASC, p.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS AGRUPADOS POR COMBO (para vista resumen)
    // -------------------------------------------------
    public function getAllGroupedByCombo() {
        try {
            // Obtener todos los combos
            $sqlCombos = "SELECT c.id_combo, c.nombre, c.precio, c.estado,
                                 (SELECT COUNT(*) FROM producto_combo pc WHERE pc.id_combo = c.id_combo) as total_productos
                          FROM combos c
                          ORDER BY c.nombre ASC";
            $stmtCombos = $this->conn->query($sqlCombos);
            $combos = $stmtCombos->fetchAll(PDO::FETCH_ASSOC);

            // Para cada combo, obtener sus productos
            foreach ($combos as &$combo) {
                $combo['productos'] = $this->getProductosByCombo($combo['id_combo']);
            }

            return $combos;
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER COMBOS (para selects)
    // -------------------------------------------------
    public function getCombos($soloActivos = true) {
        try {
            $sql = "SELECT id_combo, nombre, precio, estado FROM combos";
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
    // OBTENER PRODUCTOS DE SEDE (para selects)
    // -------------------------------------------------
    public function getProductosSede($filtroSede = null) {
        try {
            $sql = "SELECT ps.id_producto_sede, p.nombre as producto_nombre, 
                           s.nombre as sede_nombre, ps.stock, p.precio_unitario,
                           p.id_producto, s.id_sede
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    WHERE p.estado = 1";
            
            $params = [];
            if ($filtroSede && is_numeric($filtroSede)) {
                $sql .= " AND s.id_sede = :sede";
                $params[':sede'] = $filtroSede;
            }
            
            $sql .= " ORDER BY s.nombre ASC, p.nombre ASC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER SEDES (para filtros)
    // -------------------------------------------------
    public function getSedes() {
        try {
            $sql = "SELECT id_sede, nombre FROM sede WHERE estado = 1 ORDER BY nombre ASC";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR RELACIÓN
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $actual = $this->getById($id);
        if (!$actual) {
            return ['success' => false, 'message' => 'No se encontró el registro'];
        }

        // Validar nuevo combo si se proporciona
        if (isset($data['id_combo']) && !empty($data['id_combo'])) {
            $combo = $this->comboExists($data['id_combo']);
            if (!$combo) {
                return ['success' => false, 'message' => 'El combo seleccionado no existe'];
            }
        }

        // Validar nuevo producto_sede si se proporciona
        if (isset($data['id_producto_sede']) && !empty($data['id_producto_sede'])) {
            $productoSede = $this->productoSedeExists($data['id_producto_sede']);
            if (!$productoSede) {
                return ['success' => false, 'message' => 'El producto seleccionado no existe'];
            }
        }

        try {
            $campos = [];
            $params = [':id' => $id];

            if (isset($data['id_combo'])) {
                $campos[] = "id_combo = :id_combo";
                $params[':id_combo'] = $data['id_combo'];
            }

            if (isset($data['id_producto_sede'])) {
                $campos[] = "id_producto_sede = :id_producto_sede";
                $params[':id_producto_sede'] = $data['id_producto_sede'];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No hay campos para actualizar'];
            }

            $sql = "UPDATE producto_combo SET " . implode(", ", $campos) . " WHERE id_productos_combos = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            return ['success' => true, 'message' => 'Registro actualizado correctamente'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR RELACIÓN
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $actual = $this->getById($id);
        if (!$actual) {
            return ['success' => false, 'message' => 'No se encontró el registro'];
        }

        try {
            $sql = "DELETE FROM producto_combo WHERE id_productos_combos = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            return [
                'success' => true,
                'message' => 'Producto removido del combo correctamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR TODOS LOS PRODUCTOS DE UN COMBO
    // -------------------------------------------------
    public function deleteByCombo($idCombo) {
        if (!is_numeric($idCombo)) {
            return ['success' => false, 'message' => 'ID de combo inválido'];
        }

        try {
            $sql = "DELETE FROM producto_combo WHERE id_combo = :id_combo";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id_combo" => $idCombo]);
            
            $eliminados = $stmt->rowCount();

            return [
                'success' => true,
                'message' => "$eliminados producto(s) removido(s) del combo",
                'eliminados' => $eliminados
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // REEMPLAZAR PRODUCTOS DE UN COMBO (eliminar todos y agregar nuevos)
    // -------------------------------------------------
    public function replaceProductosCombo($idCombo, $productosSedeIds) {
        if (!is_array($productosSedeIds)) {
            return ['success' => false, 'message' => 'Lista de productos inválida'];
        }

        $combo = $this->comboExists($idCombo);
        if (!$combo) {
            return ['success' => false, 'message' => 'El combo no existe'];
        }

        try {
            $this->conn->beginTransaction();

            // 1. Eliminar todos los productos actuales del combo
            $sqlDelete = "DELETE FROM producto_combo WHERE id_combo = :id_combo";
            $stmtDelete = $this->conn->prepare($sqlDelete);
            $stmtDelete->execute([":id_combo" => $idCombo]);

            // 2. Insertar los nuevos productos
            $insertados = 0;
            foreach ($productosSedeIds as $idProductoSede) {
                if (!empty($idProductoSede) && is_numeric($idProductoSede)) {
                    $sql = "INSERT INTO producto_combo (id_combo, id_producto_sede) VALUES (:id_combo, :id_producto_sede)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute([
                        ':id_combo' => $idCombo,
                        ':id_producto_sede' => $idProductoSede
                    ]);
                    $insertados++;
                }
            }

            $this->conn->commit();

            return [
                'success' => true,
                'message' => "Combo actualizado con $insertados producto(s)",
                'insertados' => $insertados
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ESTADÍSTICAS
    // -------------------------------------------------
    public function getEstadisticas() {
        try {
            $stats = [];

            // Total de relaciones
            $sql = "SELECT COUNT(*) as total FROM producto_combo";
            $stmt = $this->conn->query($sql);
            $stats['total_relaciones'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Combos con productos
            $sql = "SELECT COUNT(DISTINCT id_combo) as total FROM producto_combo";
            $stmt = $this->conn->query($sql);
            $stats['combos_con_productos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Total combos
            $sql = "SELECT COUNT(*) as total FROM combos";
            $stmt = $this->conn->query($sql);
            $stats['total_combos'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Combos sin productos
            $stats['combos_sin_productos'] = $stats['total_combos'] - $stats['combos_con_productos'];

            // Productos más usados en combos
            $sql = "SELECT p.nombre, COUNT(*) as veces_usado
                    FROM producto_combo pc
                    INNER JOIN producto_sede ps ON pc.id_producto_sede = ps.id_producto_sede
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    GROUP BY p.id_producto, p.nombre
                    ORDER BY veces_usado DESC
                    LIMIT 5";
            $stmt = $this->conn->query($sql);
            $stats['productos_mas_usados'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $stats;
        } catch (PDOException $e) {
            return [];
        }
    }
}
