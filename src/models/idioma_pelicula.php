<?php
class IdiomasPeliculaModel {
    private $conn;
    private $id_idiomas_pelicula;
    private $id_pelicula;
    private $id_idioma;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // -------------------------------------------------
    // VALIDAR DATOS
    // -------------------------------------------------
    private function validar($data, $modo = "insertar") {
        
        // Campos obligatorios para insertar
        $requeridos = ["id_pelicula", "id_idioma"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || !is_numeric($data[$campo])) {
                    throw new Exception("El campo '$campo' es obligatorio y debe ser numérico.");
                }
            }
        }

        // Validar que sean números positivos
        if (isset($data["id_pelicula"]) && $data["id_pelicula"] <= 0) {
            throw new Exception("ID de película inválido.");
        }

        if (isset($data["id_idioma"]) && $data["id_idioma"] <= 0) {
            throw new Exception("ID de idioma inválido.");
        }

        return true;
    }

    // -------------------------------------------------
    // VERIFICAR EXISTENCIA DE RELACIONES
    // -------------------------------------------------
    private function verificarExistencia($id_pelicula, $id_idioma) {
        // Verificar que la película existe
        $sqlPelicula = "SELECT id_pelicula FROM pelicula WHERE id_pelicula = :id_pelicula";
        $stmtPelicula = $this->conn->prepare($sqlPelicula);
        $stmtPelicula->execute([":id_pelicula" => $id_pelicula]);
        
        if (!$stmtPelicula->fetch()) {
            throw new Exception("La película especificada no existe.");
        }

        // Verificar que el idioma existe
        $sqlIdioma = "SELECT id_idioma FROM idioma WHERE id_idioma = :id_idioma";
        $stmtIdioma = $this->conn->prepare($sqlIdioma);
        $stmtIdioma->execute([":id_idioma" => $id_idioma]);
        
        if (!$stmtIdioma->fetch()) {
            throw new Exception("El idioma especificado no existe.");
        }

        return true;
    }

    // -------------------------------------------------
    // INSERTAR RELACIÓN IDIOMA-PELÍCULA
    // -------------------------------------------------
    public function create($data) {
        $this->validar($data, "insertar");
        $this->verificarExistencia($data["id_pelicula"], $data["id_idioma"]);

        // Verificar que no exista ya la relación
        $sqlCheck = "SELECT id_idiomas_pelicula FROM idiomas_pelicula 
                     WHERE id_pelicula = :id_pelicula AND id_idioma = :id_idioma";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ":id_pelicula" => $data["id_pelicula"],
            ":id_idioma" => $data["id_idioma"]
        ]);

        if ($stmtCheck->fetch()) {
            throw new Exception("Esta relación idioma-película ya existe.");
        }

        $sql = "INSERT INTO idiomas_pelicula (id_pelicula, id_idioma) 
                VALUES (:id_pelicula, :id_idioma)";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt->execute([
            ":id_pelicula" => $data["id_pelicula"],
            ":id_idioma" => $data["id_idioma"]
        ])) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al insertar la relación idioma-película: " . implode(", ", $errorInfo));
        }

        return true;
    }

    // -------------------------------------------------
    // OBTENER TODAS LAS RELACIONES
    // -------------------------------------------------
    public function get() {
        $sql = "SELECT ip.*, p.nombre as pelicula, i.idioma 
                FROM idiomas_pelicula ip
                INNER JOIN pelicula p ON ip.id_pelicula = p.id_pelicula
                INNER JOIN idioma i ON ip.id_idioma = i.id_idioma
                ORDER BY ip.id_idiomas_pelicula DESC";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER POR ID
    // -------------------------------------------------
    public function getId($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID inválido.");
        }

        $sql = "SELECT ip.*, p.nombre as pelicula, i.idioma 
                FROM idiomas_pelicula ip
                INNER JOIN pelicula p ON ip.id_pelicula = p.id_pelicula
                INNER JOIN idioma i ON ip.id_idioma = i.id_idioma
                WHERE ip.id_idiomas_pelicula = :id 
                LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            throw new Exception("Relación idioma-película no encontrada.");
        }

        return $result;
    }

    // -------------------------------------------------
    // OBTENER IDIOMAS POR PELÍCULA
    // -------------------------------------------------
    public function getIdiomasByPelicula($id_pelicula) {
        if (!is_numeric($id_pelicula) || $id_pelicula <= 0) {
            throw new Exception("ID de película inválido.");
        }

        $sql = "SELECT i.id_idioma, i.idioma 
                FROM idiomas_pelicula ip
                INNER JOIN idioma i ON ip.id_idioma = i.id_idioma
                WHERE ip.id_pelicula = :id_pelicula
                ORDER BY i.idioma";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id_pelicula" => $id_pelicula]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER PELÍCULAS POR IDIOMA
    // -------------------------------------------------
    public function getPeliculasByIdioma($id_idioma) {
        if (!is_numeric($id_idioma) || $id_idioma <= 0) {
            throw new Exception("ID de idioma inválido.");
        }

        $sql = "SELECT p.id_pelicula, p.nombre, p.duracion, p.url_imagen 
                FROM idiomas_pelicula ip
                INNER JOIN pelicula p ON ip.id_pelicula = p.id_pelicula
                WHERE ip.id_idioma = :id_idioma
                ORDER BY p.nombre";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id_idioma" => $id_idioma]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // ACTUALIZAR RELACIÓN
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID inválido.");
        }

        $this->validar($data, "actualizar");
        $this->verificarExistencia($data["id_pelicula"], $data["id_idioma"]);

        // Verificar que no exista duplicado (excluyendo el actual)
        $sqlCheck = "SELECT id_idiomas_pelicula FROM idiomas_pelicula 
                     WHERE id_pelicula = :id_pelicula 
                     AND id_idioma = :id_idioma 
                     AND id_idiomas_pelicula != :id";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([
            ":id_pelicula" => $data["id_pelicula"],
            ":id_idioma" => $data["id_idioma"],
            ":id" => $id
        ]);

        if ($stmtCheck->fetch()) {
            throw new Exception("Esta relación idioma-película ya existe.");
        }

        $sql = "UPDATE idiomas_pelicula 
                SET id_pelicula = :id_pelicula, id_idioma = :id_idioma 
                WHERE id_idiomas_pelicula = :id";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt->execute([
            ":id_pelicula" => $data["id_pelicula"],
            ":id_idioma" => $data["id_idioma"],
            ":id" => $id
        ])) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al actualizar: " . implode(", ", $errorInfo));
        }

        return true;
    }

    // -------------------------------------------------
    // ELIMINAR RELACIÓN
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id) || $id <= 0) {
            throw new Exception("ID inválido.");
        }

        $sql = "DELETE FROM idiomas_pelicula WHERE id_idiomas_pelicula = :id";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt->execute([":id" => $id])) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al eliminar: " . implode(", ", $errorInfo));
        }

        return true;
    }

    // -------------------------------------------------
    // ELIMINAR POR PELÍCULA (útil cuando eliminas una película)
    // -------------------------------------------------
    public function deleteByPelicula($id_pelicula) {
        if (!is_numeric($id_pelicula) || $id_pelicula <= 0) {
            throw new Exception("ID de película inválido.");
        }

        $sql = "DELETE FROM idiomas_pelicula WHERE id_pelicula = :id_pelicula";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt->execute([":id_pelicula" => $id_pelicula])) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al eliminar: " . implode(", ", $errorInfo));
        }

        return true;
    }

    // -------------------------------------------------
    // VERIFICAR SI EXISTE RELACIÓN
    // -------------------------------------------------
    public function existeRelacion($id_pelicula, $id_idioma) {
        if (!is_numeric($id_pelicula) || !is_numeric($id_idioma)) {
            return false;
        }

        $sql = "SELECT id_idiomas_pelicula FROM idiomas_pelicula 
                WHERE id_pelicula = :id_pelicula AND id_idioma = :id_idioma";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ":id_pelicula" => $id_pelicula,
            ":id_idioma" => $id_idioma
        ]);

        return (bool) $stmt->fetch();
    }
}
?>