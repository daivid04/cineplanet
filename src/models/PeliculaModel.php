<?php 

class PeliculaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDACIONES
    // -------------------------------------------------
    
    /**
     * Verifica si ya existe una pelicula con el mismo nombre
     */
    private function existsByNombre($nombre, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM pelicula WHERE LOWER(nombre) = LOWER(:nombre)";
        if ($excludeId) {
            $sql .= " AND id_pelicula != :excludeId";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        if ($excludeId) {
            $stmt->bindParam(':excludeId', $excludeId, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    // -------------------------------------------------
    // CREAR PELÍCULA
    // -------------------------------------------------
    public function create($data) {
        // Validaciones
        if (!isset($data['nombre']) || trim($data['nombre']) === '') {
            return ['success' => false, 'message' => 'El nombre de la pelicula es requerido'];
        }

        if (!isset($data['duracion']) || !is_numeric($data['duracion']) || $data['duracion'] <= 0) {
            return ['success' => false, 'message' => 'La duracion es requerida y debe ser mayor a 0 minutos'];
        }

        if (!isset($data['url_imagen']) || trim($data['url_imagen']) === '') {
            return ['success' => false, 'message' => 'La URL de la imagen es requerida'];
        }

        if (!isset($data['sinopsis']) || trim($data['sinopsis']) === '') {
            return ['success' => false, 'message' => 'La sinopsis es requerida'];
        }

        if ($this->existsByNombre($data['nombre'])) {
            return ['success' => false, 'message' => 'Ya existe una pelicula con ese nombre'];
        }

        try {
            $sql = "INSERT INTO pelicula (duracion, url_imagen, nombre, sinopsis, estado) 
                    VALUES (:duracion, :url_imagen, :nombre, :sinopsis, 1)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':duracion' => intval($data['duracion']),
                ':url_imagen' => trim($data['url_imagen']),
                ':nombre' => trim($data['nombre']),
                ':sinopsis' => trim($data['sinopsis'])
            ]);

            $idPelicula = $this->conn->lastInsertId();

            // Guardar idiomas si se proporcionan
            if (isset($data['idiomas']) && is_array($data['idiomas'])) {
                $this->syncIdiomas($idPelicula, $data['idiomas']);
            }

            // Guardar formatos si se proporcionan
            if (isset($data['formatos']) && is_array($data['formatos'])) {
                $this->syncFormatos($idPelicula, $data['formatos']);
            }
            
            return [
                'success' => true,
                'message' => 'Pelicula creada exitosamente',
                'id' => $idPelicula
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al crear la pelicula: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // SINCRONIZAR IDIOMAS
    // -------------------------------------------------
    private function syncIdiomas($idPelicula, $idiomas) {
        // Eliminar idiomas existentes
        $sqlDelete = "DELETE FROM idiomas_pelicula WHERE id_pelicula = :id";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->execute([':id' => $idPelicula]);

        // Insertar nuevos idiomas
        if (!empty($idiomas)) {
            $sqlInsert = "INSERT INTO idiomas_pelicula (id_pelicula, id_idioma) VALUES (:id_pelicula, :id_idioma)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            
            foreach ($idiomas as $idIdioma) {
                $stmtInsert->execute([
                    ':id_pelicula' => $idPelicula,
                    ':id_idioma' => intval($idIdioma)
                ]);
            }
        }
    }

    // -------------------------------------------------
    // SINCRONIZAR FORMATOS
    // -------------------------------------------------
    private function syncFormatos($idPelicula, $formatos) {
        // Eliminar formatos existentes
        $sqlDelete = "DELETE FROM formato_pelicula WHERE id_pelicula = :id";
        $stmtDelete = $this->conn->prepare($sqlDelete);
        $stmtDelete->execute([':id' => $idPelicula]);

        // Insertar nuevos formatos
        if (!empty($formatos)) {
            $sqlInsert = "INSERT INTO formato_pelicula (id_pelicula, id_formato) VALUES (:id_pelicula, :id_formato)";
            $stmtInsert = $this->conn->prepare($sqlInsert);
            
            foreach ($formatos as $idFormato) {
                $stmtInsert->execute([
                    ':id_pelicula' => $idPelicula,
                    ':id_formato' => intval($idFormato)
                ]);
            }
        }
    }

    // -------------------------------------------------
    // OBTENER PELÍCULA POR ID (Simple)
    // -------------------------------------------------
    public function getById($id) {
        try {
            $sql = "SELECT * FROM pelicula WHERE id_pelicula = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            $pelicula = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($pelicula) {
                // Obtener idiomas
                $pelicula['idiomas'] = $this->getIdiomasByPelicula($id);
                $pelicula['idiomas_ids'] = array_column($pelicula['idiomas'], 'id_idioma');
                
                // Obtener formatos
                $pelicula['formatos'] = $this->getFormatosByPelicula($id);
                $pelicula['formatos_ids'] = array_column($pelicula['formatos'], 'id_formato');
            }

            return $pelicula;
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER PELÍCULA CON DETALLES (Para listados)
    // -------------------------------------------------
    public function getByIdWithDetails($id) {
        try {
            $sql = "SELECT 
                        p.id_pelicula, 
                        p.duracion, 
                        p.url_imagen, 
                        p.nombre, 
                        p.sinopsis,
                        p.estado,
                        GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas_texto,
                        GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos_texto
                    FROM pelicula p
                    LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                    LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                    LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                    LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                    WHERE p.id_pelicula = :id
                    GROUP BY p.id_pelicula";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }

    // -------------------------------------------------
    // OBTENER IDIOMAS DE UNA PELÍCULA
    // -------------------------------------------------
    public function getIdiomasByPelicula($idPelicula) {
        $sql = "SELECT i.id_idioma, i.idioma 
                FROM idiomas_pelicula ip
                INNER JOIN idioma i ON ip.id_idioma = i.id_idioma
                WHERE ip.id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idPelicula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER FORMATOS DE UNA PELÍCULA
    // -------------------------------------------------
    public function getFormatosByPelicula($idPelicula) {
        $sql = "SELECT f.id_formato, f.nombre 
                FROM formato_pelicula fp
                INNER JOIN formato f ON fp.id_formato = f.id_formato
                WHERE fp.id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $idPelicula]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS PELÍCULAS
    // -------------------------------------------------
    public function getAll($soloActivos = false) {
        try {
            $sql = "SELECT 
                        p.id_pelicula, 
                        p.duracion, 
                        p.url_imagen, 
                        p.nombre, 
                        p.sinopsis,
                        p.estado,
                        GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas_texto,
                        GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos_texto
                    FROM pelicula p
                    LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                    LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                    LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                    LEFT JOIN formato fo ON fo.id_formato = f.id_formato";
            
            if ($soloActivos) {
                $sql .= " WHERE p.estado = 1";
            }
            
            $sql .= " GROUP BY p.id_pelicula ORDER BY p.nombre ASC";

            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // BUSCAR PELÍCULAS POR NOMBRE
    // -------------------------------------------------
    public function getByName($name) {
        try {
            $sql = "SELECT 
                        p.id_pelicula, 
                        p.duracion, 
                        p.url_imagen, 
                        p.nombre, 
                        p.sinopsis,
                        p.estado,
                        GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas_texto,
                        GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos_texto
                    FROM pelicula p
                    LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                    LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                    LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                    LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                    WHERE p.nombre LIKE :name
                    GROUP BY p.id_pelicula";

            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':name' => '%' . $name . '%']);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR PELÍCULA
    // -------------------------------------------------
    public function update($id, $data) {
        $pelicula = $this->getById($id);
        if (!$pelicula) {
            return ['success' => false, 'message' => 'Pelicula no encontrada'];
        }

        // Validaciones
        if (isset($data['nombre']) && trim($data['nombre']) === '') {
            return ['success' => false, 'message' => 'El nombre no puede estar vacio'];
        }

        if (isset($data['nombre']) && $this->existsByNombre($data['nombre'], $id)) {
            return ['success' => false, 'message' => 'Ya existe otra pelicula con ese nombre'];
        }

        if (isset($data['duracion']) && (!is_numeric($data['duracion']) || $data['duracion'] <= 0)) {
            return ['success' => false, 'message' => 'La duracion debe ser mayor a 0 minutos'];
        }

        try {
            $fields = [];
            $params = [':id' => $id];

            if (isset($data['duracion'])) {
                $fields[] = "duracion = :duracion";
                $params[':duracion'] = intval($data['duracion']);
            }
            if (isset($data['url_imagen'])) {
                $fields[] = "url_imagen = :url_imagen";
                $params[':url_imagen'] = trim($data['url_imagen']);
            }
            if (isset($data['nombre'])) {
                $fields[] = "nombre = :nombre";
                $params[':nombre'] = trim($data['nombre']);
            }
            if (isset($data['sinopsis'])) {
                $fields[] = "sinopsis = :sinopsis";
                $params[':sinopsis'] = trim($data['sinopsis']);
            }
            if (isset($data['estado'])) {
                $fields[] = "estado = :estado";
                $params[':estado'] = intval($data['estado']);
            }

            if (!empty($fields)) {
                $sql = "UPDATE pelicula SET " . implode(", ", $fields) . " WHERE id_pelicula = :id";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
            }

            // Sincronizar idiomas si se proporcionan
            if (isset($data['idiomas']) && is_array($data['idiomas'])) {
                $this->syncIdiomas($id, $data['idiomas']);
            }

            // Sincronizar formatos si se proporcionan
            if (isset($data['formatos']) && is_array($data['formatos'])) {
                $this->syncFormatos($id, $data['formatos']);
            }
            
            return [
                'success' => true,
                'message' => 'Pelicula actualizada exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al actualizar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // ELIMINAR PELÍCULA
    // -------------------------------------------------
    public function delete($id) {
        $pelicula = $this->getById($id);
        if (!$pelicula) {
            return ['success' => false, 'message' => 'Pelicula no encontrada'];
        }

        // Verificar si tiene funciones asociadas
        $sqlFunciones = "SELECT COUNT(*) as count FROM funcion WHERE id_pelicula = :id";
        $stmtFunciones = $this->conn->prepare($sqlFunciones);
        $stmtFunciones->execute([':id' => $id]);
        $funciones = $stmtFunciones->fetch(PDO::FETCH_ASSOC);

        if ($funciones['count'] > 0) {
            return [
                'success' => false,
                'message' => 'No se puede eliminar la pelicula porque tiene ' . $funciones['count'] . ' funcion(es) asociada(s). Desactivela en su lugar.'
            ];
        }

        try {
            // Las tablas pivote tienen ON DELETE CASCADE, se eliminaran automaticamente
            $sql = "DELETE FROM pelicula WHERE id_pelicula = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return [
                'success' => true,
                'message' => 'Pelicula "' . $pelicula['nombre'] . '" eliminada exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al eliminar: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // TOGGLE ESTADO
    // -------------------------------------------------
    public function toggleEstado($id, $estado) {
        $pelicula = $this->getById($id);
        if (!$pelicula) {
            return ['success' => false, 'message' => 'Pelicula no encontrada'];
        }

        // Si se va a desactivar, verificar funciones futuras
        if ($estado == 0) {
            $sqlFunciones = "SELECT COUNT(*) as count FROM funcion 
                             WHERE id_pelicula = :id AND fecha >= CURDATE() AND estado = 1";
            $stmtFunciones = $this->conn->prepare($sqlFunciones);
            $stmtFunciones->execute([':id' => $id]);
            $funciones = $stmtFunciones->fetch(PDO::FETCH_ASSOC);

            if ($funciones['count'] > 0) {
                return [
                    'success' => false,
                    'message' => 'No se puede desactivar la pelicula porque tiene ' . $funciones['count'] . ' funcion(es) programada(s). Cancele primero las funciones.'
                ];
            }
        }

        try {
            $sql = "UPDATE pelicula SET estado = :estado WHERE id_pelicula = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':estado' => intval($estado),
                ':id' => $id
            ]);

            $estadoTexto = $estado == 1 ? 'activada' : 'desactivada';
            return [
                'success' => true,
                'message' => 'Pelicula "' . $pelicula['nombre'] . '" ' . $estadoTexto . ' exitosamente'
            ];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error al cambiar el estado: ' . $e->getMessage()];
        }
    }

    // -------------------------------------------------
    // CONTAR PELÍCULAS
    // -------------------------------------------------
    public function count($soloActivos = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM pelicula";
            if ($soloActivos) {
                $sql .= " WHERE estado = 1";
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    // -------------------------------------------------
    // FORMATEAR DURACION (Helper)
    // -------------------------------------------------
    public static function formatDuration($minutos) {
        $horas = floor($minutos / 60);
        $mins = $minutos % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $mins . 'min';
        }
        return $mins . ' min';
    }
}