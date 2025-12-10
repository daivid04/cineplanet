<?php

class ProductoSedeModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    private function productoExiste($idProducto) {
        $sql = "SELECT COUNT(*) FROM producto WHERE id_producto = :id AND estado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $idProducto]);
        return $stmt->fetchColumn() > 0;
    }

    private function sedeExiste($idSede) {
        $sql = "SELECT COUNT(*) FROM sede WHERE id_sede = :id AND estado = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $idSede]);
        return $stmt->fetchColumn() > 0;
    }

    private function existsProductoEnSede($idProducto, $idSede, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM producto_sede 
                WHERE id_producto = :idProducto AND id_sede = :idSede";
        if ($excludeId) {
            $sql .= " AND id_producto_sede != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':idProducto', $idProducto, PDO::PARAM_INT);
        $stmt->bindParam(':idSede', $idSede, PDO::PARAM_INT);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }

    // -------------------------------------------------
    // VERIFICAR DEPENDENCIAS
    // -------------------------------------------------
    public function countEnCombos($id) {
        try {
            $sql = "SELECT COUNT(*) FROM producto_combo WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function countEnCompras($id) {
        try {
            $sql = "SELECT COUNT(*) FROM compra_cliente WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    // -------------------------------------------------
    // CREAR PRODUCTO_SEDE
    // -------------------------------------------------
    public function create($data) {
        if (!isset($data["id_producto"]) || !is_numeric($data["id_producto"])) {
            return ['success' => false, 'message' => 'El producto es requerido'];
        }

        if (!isset($data["id_sede"]) || !is_numeric($data["id_sede"])) {
            return ['success' => false, 'message' => 'La sede es requerida'];
        }

        if (!isset($data["stock"]) || !is_numeric($data["stock"]) || $data["stock"] < 0) {
            return ['success' => false, 'message' => 'El stock debe ser un número mayor o igual a 0'];
        }

        // Validar que producto exista y esté activo
        if (!$this->productoExiste($data["id_producto"])) {
            return ['success' => false, 'message' => 'El producto no existe o está inactivo'];
        }

        // Validar que sede exista y esté activa
        if (!$this->sedeExiste($data["id_sede"])) {
            return ['success' => false, 'message' => 'La sede no existe o está inactiva'];
        }

        // Validar que no exista ya esa combinación
        if ($this->existsProductoEnSede($data["id_producto"], $data["id_sede"])) {
            return ['success' => false, 'message' => 'Este producto ya está asignado a esta sede'];
        }

        try {
            $sql = "INSERT INTO producto_sede (stock, id_producto, id_sede) 
                    VALUES (:stock, :id_producto, :id_sede)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ":stock" => $data["stock"],
                ":id_producto" => $data["id_producto"],
                ":id_sede" => $data["id_sede"]
            ]);

            return [
                'success' => true,
                'message' => 'Producto asignado a sede correctamente',
                'id' => $this->conn->lastInsertId()
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // OBTENER POR ID
    // -------------------------------------------------
    public function getById($id) {
        if (!is_numeric($id)) {
            return null;
        }

        try {
            $sql = "SELECT ps.id_producto_sede, ps.stock, ps.id_producto, ps.id_sede,
                           p.nombre as producto_nombre, p.precio_unitario, p.estado as producto_estado,
                           s.nombre as sede_nombre, s.estado as sede_estado,
                           c.nombre as ciudad_nombre
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad
                    WHERE ps.id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER TODOS
    // -------------------------------------------------
    public function getAll($idSede = null, $idProducto = null, $soloConStock = false) {
        try {
            $sql = "SELECT ps.id_producto_sede, ps.stock, ps.id_producto, ps.id_sede,
                           p.nombre as producto_nombre, p.precio_unitario, p.estado as producto_estado,
                           s.nombre as sede_nombre, s.estado as sede_estado,
                           c.nombre as ciudad_nombre
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    INNER JOIN ciudad c ON s.id_ciudad = c.id_ciudad";
            
            $conditions = [];
            $params = [];

            if ($idSede) {
                $conditions[] = "ps.id_sede = :idSede";
                $params[":idSede"] = $idSede;
            }

            if ($idProducto) {
                $conditions[] = "ps.id_producto = :idProducto";
                $params[":idProducto"] = $idProducto;
            }

            if ($soloConStock) {
                $conditions[] = "ps.stock > 0";
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(" AND ", $conditions);
            }

            $sql .= " ORDER BY c.nombre ASC, s.nombre ASC, p.nombre ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // OBTENER POR SEDE (agrupado)
    // -------------------------------------------------
    public function getBySede($idSede) {
        return $this->getAll($idSede);
    }

    // -------------------------------------------------
    // OBTENER POR PRODUCTO (en qué sedes está)
    // -------------------------------------------------
    public function getByProducto($idProducto) {
        return $this->getAll(null, $idProducto);
    }

    // -------------------------------------------------
    // ACTUALIZAR STOCK
    // -------------------------------------------------
    public function updateStock($id, $nuevoStock) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        if (!is_numeric($nuevoStock) || $nuevoStock < 0) {
            return ['success' => false, 'message' => 'El stock debe ser un número mayor o igual a 0'];
        }

        try {
            $sql = "UPDATE producto_sede SET stock = :stock WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":stock" => $nuevoStock]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Stock actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el registro o no hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // INCREMENTAR STOCK (entrada de inventario)
    // -------------------------------------------------
    public function incrementarStock($id, $cantidad) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a 0'];
        }

        try {
            $sql = "UPDATE producto_sede SET stock = stock + :cantidad WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":cantidad" => $cantidad]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => "Stock incrementado en {$cantidad} unidades"];
            } else {
                return ['success' => false, 'message' => 'No se encontró el registro'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // DECREMENTAR STOCK (venta o salida)
    // -------------------------------------------------
    public function decrementarStock($id, $cantidad) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        if (!is_numeric($cantidad) || $cantidad <= 0) {
            return ['success' => false, 'message' => 'La cantidad debe ser mayor a 0'];
        }

        // Verificar stock actual
        $registro = $this->getById($id);
        if (!$registro) {
            return ['success' => false, 'message' => 'Registro no encontrado'];
        }

        if ($registro['stock'] < $cantidad) {
            return [
                'success' => false, 
                'message' => "Stock insuficiente. Disponible: {$registro['stock']} unidades"
            ];
        }

        try {
            $sql = "UPDATE producto_sede SET stock = stock - :cantidad WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id, ":cantidad" => $cantidad]);

            if ($stmt->rowCount() > 0) {
                $nuevoStock = $registro['stock'] - $cantidad;
                return [
                    'success' => true, 
                    'message' => "Stock decrementado. Nuevo stock: {$nuevoStock} unidades"
                ];
            } else {
                return ['success' => false, 'message' => 'No se encontró el registro'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR GENERAL
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        $registro = $this->getById($id);
        if (!$registro) {
            return ['success' => false, 'message' => 'Registro no encontrado'];
        }

        // Si cambia producto o sede, verificar que no exista la combinación
        $nuevoProducto = $data["id_producto"] ?? $registro['id_producto'];
        $nuevaSede = $data["id_sede"] ?? $registro['id_sede'];

        if ($nuevoProducto != $registro['id_producto'] || $nuevaSede != $registro['id_sede']) {
            if ($this->existsProductoEnSede($nuevoProducto, $nuevaSede, $id)) {
                return ['success' => false, 'message' => 'Ya existe esa combinación producto-sede'];
            }
        }

        // Validar producto si cambia
        if (isset($data["id_producto"]) && !$this->productoExiste($data["id_producto"])) {
            return ['success' => false, 'message' => 'El producto no existe o está inactivo'];
        }

        // Validar sede si cambia
        if (isset($data["id_sede"]) && !$this->sedeExiste($data["id_sede"])) {
            return ['success' => false, 'message' => 'La sede no existe o está inactiva'];
        }

        // Validar stock
        if (isset($data["stock"]) && (!is_numeric($data["stock"]) || $data["stock"] < 0)) {
            return ['success' => false, 'message' => 'El stock debe ser un número mayor o igual a 0'];
        }

        try {
            $campos = [];
            $params = [":id" => $id];

            if (isset($data["stock"])) {
                $campos[] = "stock = :stock";
                $params[":stock"] = $data["stock"];
            }
            
            if (isset($data["id_producto"])) {
                $campos[] = "id_producto = :id_producto";
                $params[":id_producto"] = $data["id_producto"];
            }
            
            if (isset($data["id_sede"])) {
                $campos[] = "id_sede = :id_sede";
                $params[":id_sede"] = $data["id_sede"];
            }

            if (empty($campos)) {
                return ['success' => false, 'message' => 'No se proporcionaron campos para actualizar'];
            }

            $sql = "UPDATE producto_sede SET " . implode(", ", $campos) . " WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Registro actualizado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No hubo cambios'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            return ['success' => false, 'message' => 'ID inválido'];
        }

        // Verificar en combos
        $enCombos = $this->countEnCombos($id);
        if ($enCombos > 0) {
            return [
                'success' => false, 
                'message' => "No se puede eliminar, está asociado a {$enCombos} combo(s)"
            ];
        }

        // Verificar en compras
        $enCompras = $this->countEnCompras($id);
        if ($enCompras > 0) {
            return [
                'success' => false, 
                'message' => "No se puede eliminar, tiene {$enCompras} compra(s) registrada(s)"
            ];
        }

        try {
            $sql = "DELETE FROM producto_sede WHERE id_producto_sede = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            if ($stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Registro eliminado correctamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el registro'];
            }
        } catch (PDOException $e) {
            // FK constraint
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                return ['success' => false, 'message' => 'No se puede eliminar, tiene registros asociados'];
            }
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ESTADÍSTICAS
    // -------------------------------------------------
    public function getEstadisticasBySede($idSede) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_productos,
                        SUM(ps.stock) as total_stock,
                        SUM(ps.stock * p.precio_unitario) as valor_inventario
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    WHERE ps.id_sede = :idSede";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":idSede" => $idSede]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function getProductosSinStock($idSede = null) {
        try {
            $sql = "SELECT ps.*, p.nombre as producto_nombre, s.nombre as sede_nombre
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    WHERE ps.stock = 0";
            
            $params = [];
            if ($idSede) {
                $sql .= " AND ps.id_sede = :idSede";
                $params[":idSede"] = $idSede;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getProductosBajoStock($umbral = 10, $idSede = null) {
        try {
            $sql = "SELECT ps.*, p.nombre as producto_nombre, s.nombre as sede_nombre
                    FROM producto_sede ps
                    INNER JOIN producto p ON ps.id_producto = p.id_producto
                    INNER JOIN sede s ON ps.id_sede = s.id_sede
                    WHERE ps.stock > 0 AND ps.stock <= :umbral";
            
            $params = [":umbral" => $umbral];
            if ($idSede) {
                $sql .= " AND ps.id_sede = :idSede";
                $params[":idSede"] = $idSede;
            }

            $sql .= " ORDER BY ps.stock ASC";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ASIGNACIÓN MASIVA (asignar producto a múltiples sedes)
    // -------------------------------------------------
    public function asignarAMúltiplesSedes($idProducto, $sedes, $stockInicial = 0) {
        if (!$this->productoExiste($idProducto)) {
            return ['success' => false, 'message' => 'El producto no existe o está inactivo'];
        }

        $exitosos = 0;
        $errores = [];

        foreach ($sedes as $idSede) {
            if ($this->existsProductoEnSede($idProducto, $idSede)) {
                $errores[] = "Sede ID {$idSede}: ya tiene asignado el producto";
                continue;
            }

            $result = $this->create([
                'id_producto' => $idProducto,
                'id_sede' => $idSede,
                'stock' => $stockInicial
            ]);

            if ($result['success']) {
                $exitosos++;
            } else {
                $errores[] = "Sede ID {$idSede}: " . $result['message'];
            }
        }

        return [
            'success' => $exitosos > 0,
            'message' => "Producto asignado a {$exitosos} sede(s)",
            'errores' => $errores
        ];
    }

    // -------------------------------------------------
    // CONTAR
    // -------------------------------------------------
    public function count($idSede = null) {
        try {
            $sql = "SELECT COUNT(*) FROM producto_sede";
            $params = [];
            
            if ($idSede) {
                $sql .= " WHERE id_sede = :idSede";
                $params[":idSede"] = $idSede;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }
}
