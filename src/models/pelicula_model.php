<?php 

class PeliculaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // CREAR PELÍCULA
    // -------------------------------------------------
    public function create($data) {
        $sql = "INSERT INTO pelicula (duracion, url_imagen, nombre, sinopsis, estado) 
                VALUES (:duracion, :url_imagen, :nombre, :sinopsis, :estado)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':duracion' => $data['duracion'],
            ':url_imagen' => $data['url_imagen'],
            ':nombre' => $data['nombre'],
            ':sinopsis' => $data['sinopsis'],
            ':estado' => $data['estado'] ?? 1
        ]);
        
        return $this->conn->lastInsertId();
    }

    // -------------------------------------------------
    // OBTENER PELÍCULA POR ID
    // -------------------------------------------------
    public function getById($id) {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis,
                    p.estado,
                    GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas,
                    GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos,
                    GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') AS generos
                FROM pelicula p
                LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                LEFT JOIN genero_pelicula gp ON p.id_pelicula = gp.id_pelicula
                LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                LEFT JOIN genero g ON g.id_genero = gp.id_genero
                WHERE p.id_pelicula = :id
                GROUP BY p.id_pelicula";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS PELÍCULAS
    // -------------------------------------------------
    public function getAll() {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis,
                    p.estado,
                    GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas,
                    GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos,
                    GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') AS generos
                FROM pelicula p
                LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                LEFT JOIN genero_pelicula gp ON p.id_pelicula = gp.id_pelicula
                LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                LEFT JOIN genero g ON g.id_genero = gp.id_genero
                GROUP BY p.id_pelicula
                ORDER BY p.id_pelicula DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // BUSCAR PELÍCULAS POR NOMBRE
    // -------------------------------------------------
    public function getByName($name) {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis,
                    p.estado,
                    GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas,
                    GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos,
                    GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') AS generos
                FROM pelicula p
                LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                LEFT JOIN genero_pelicula gp ON p.id_pelicula = gp.id_pelicula
                LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                LEFT JOIN genero g ON g.id_genero = gp.id_genero
                WHERE p.nombre LIKE :name
                GROUP BY p.id_pelicula";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':name' => '%' . $name . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER PELÍCULAS ACTIVAS
    // -------------------------------------------------
    public function getActivas() {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis,
                    p.estado,
                    GROUP_CONCAT(DISTINCT idio.idioma SEPARATOR ', ') AS idiomas,
                    GROUP_CONCAT(DISTINCT fo.nombre SEPARATOR ', ') AS formatos,
                    GROUP_CONCAT(DISTINCT g.nombre SEPARATOR ', ') AS generos
                FROM pelicula p
                LEFT JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                LEFT JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                LEFT JOIN genero_pelicula gp ON p.id_pelicula = gp.id_pelicula
                LEFT JOIN idioma idio ON idio.id_idioma = i.id_idioma
                LEFT JOIN formato fo ON fo.id_formato = f.id_formato
                LEFT JOIN genero g ON g.id_genero = gp.id_genero
                WHERE p.estado = 1
                GROUP BY p.id_pelicula
                ORDER BY p.nombre";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // ACTUALIZAR PELÍCULA
    // -------------------------------------------------
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        if (isset($data['duracion'])) {
            $fields[] = "duracion = :duracion";
            $params[':duracion'] = $data['duracion'];
        }
        if (isset($data['url_imagen'])) {
            $fields[] = "url_imagen = :url_imagen";
            $params[':url_imagen'] = $data['url_imagen'];
        }
        if (isset($data['nombre'])) {
            $fields[] = "nombre = :nombre";
            $params[':nombre'] = $data['nombre'];
        }
        if (isset($data['sinopsis'])) {
            $fields[] = "sinopsis = :sinopsis";
            $params[':sinopsis'] = $data['sinopsis'];
        }
        if (isset($data['estado'])) {
            $fields[] = "estado = :estado";
            $params[':estado'] = $data['estado'];
        }

        if (empty($fields)) {
            throw new Exception("No hay campos para actualizar");
        }

        $sql = "UPDATE pelicula SET " . implode(", ", $fields) . " WHERE id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount() > 0;
    }

    // -------------------------------------------------
    // ELIMINAR PELÍCULA
    // -------------------------------------------------
    public function delete($id) {
        $sql = "DELETE FROM pelicula WHERE id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    // -------------------------------------------------
    // CAMBIAR ESTADO DE PELÍCULA
    // -------------------------------------------------
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE pelicula SET estado = :estado WHERE id_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':estado' => $estado
        ]);
        
        return $stmt->rowCount() > 0;
    }

    public function getCartelera ($limite) {
      try{
        $sql = "SELECT * FROM pelicula_cartelera LIMIT :limite";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
        if(!$stmt->execute()){
          throw new Exception("No se pudo ejecutar la consulta de cartelera");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
      } catch (PDOException $error) {
        throw new Exception('Error en la obtencion de cartelera' . $error->getMessage());
      }
    }
}
?>