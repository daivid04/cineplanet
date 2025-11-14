<?php 

class PeliculaModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Obtener película por ID
    public function getById($id) {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis, 
                    idio.idioma, 
                    fo.nombre AS formato
                FROM pelicula p
                INNER JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                INNER JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                INNER JOIN idioma idio ON idio.id_idioma = i.id_idioma
                INNER JOIN formato fo ON fo.id_formato = f.id_formato
                WHERE p.id_pelicula = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar películas por nombre
    public function getByName($name) {
        $sql = "SELECT 
                    p.id_pelicula, 
                    p.duracion, 
                    p.url_imagen, 
                    p.nombre, 
                    p.sinopsis, 
                    idio.idioma, 
                    fo.nombre AS formato
                FROM pelicula p
                INNER JOIN idiomas_pelicula i ON p.id_pelicula = i.id_pelicula
                INNER JOIN formato_pelicula f ON p.id_pelicula = f.id_pelicula
                INNER JOIN idioma idio ON idio.id_idioma = i.id_idioma
                INNER JOIN formato fo ON fo.id_formato = f.id_formato
                WHERE p.nombre LIKE :name";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':name' => '%' . $name . '%']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>