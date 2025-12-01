<?php 
  class formatoPeliculaModel {
    private $coon;
    private $id_pelicula;
    private $id_formato;
    
    public function __construct($coon)
    {
      $this->coon = $coon;
    }

    public function getById($idPelicula) {
      $sql = "SELECT id_formato_pelicula, p.id_formato, f.nombre 
              FROM formato_pelicula p 
              INNER JOIN formato f ON p.id_formato = f.id_formato 
              WHERE id_pelicula = :idPelicula";
        $stmt = $this->coon->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }  
  }
?>