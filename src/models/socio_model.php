<?php
require_once __DIR__ . "/usuario_model.php";

class SocioModel extends UsuarioModel {
    
    // -------------------------------------------------
    // VALIDAR DATOS ESPECÍFICOS DE SOCIO
    // -------------------------------------------------
    private function validarSocio($data, $modo = "insertar") {
        
        // Campos obligatorios para insertar
        $requeridos = ["nombre", "apellido", "genero", "documento", "fecha_nacimiento", "id_tipo_socio"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        // Documento solo números
        if (isset($data["documento"]) && !preg_match("/^[0-9]{6,15}$/", $data["documento"])) {
            throw new Exception("Documento inválido. Debe tener de 6 a 15 dígitos.");
        }

        // Género permitido
        if (isset($data["genero"]) && !in_array($data["genero"], ["M", "F", "O"])) {
            throw new Exception("Género inválido. Solo se permite M, F u O.");
        }

        // Fecha formato YYYY-MM-DD
        if (isset($data["fecha_nacimiento"])) {
            $f = $data["fecha_nacimiento"];
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $f) || !strtotime($f)) {
                throw new Exception("Fecha de nacimiento inválida (use YYYY-MM-DD).");
            }
        }

        return true;
    }

    // -------------------------------------------------
    // INSERTAR SOCIO
    // -------------------------------------------------
    public function create($data) {
      $this->validarSocio($data, "insertar");

      try {
          // Iniciar una transacción
          $this->conn->beginTransaction();

          // Insertar en la tabla `usuario`
          $sqlUsuario = "INSERT INTO usuario (correo) VALUES (:correo)";
          $stmtUsuario = $this->conn->prepare($sqlUsuario);
          $stmtUsuario->execute([
              ":correo" => $data["correo"] ?? null
          ]);

          // Obtener el ID del usuario recién creado
          $idUsuario = $this->conn->lastInsertId();

          // Insertar en la tabla `socio`
          $sqlSocio = "INSERT INTO socio (id_usuario, nombre, apellido, genero, documento, fecha_nacimiento, id_tipo_socio)
                      VALUES (:id_usuario, :nombre, :apellido, :genero, :documento, :fecha_nacimiento, :id_tipo_socio)";
          $stmtSocio = $this->conn->prepare($sqlSocio);
          $stmtSocio->execute([
              ":id_usuario" => $idUsuario,
              ":nombre" => trim($data["nombre"]),
              ":apellido" => trim($data["apellido"]),
              ":genero" => $data["genero"],
              ":documento" => $data["documento"],
              ":fecha_nacimiento" => $data["fecha_nacimiento"],
              ":id_tipo_socio" => $data["id_tipo_socio"]
          ]);

          // Confirmar la transacción
          $this->conn->commit();

          return $idUsuario; // Retornar el ID del usuario creado
      } catch (Exception $e) {
          // Revertir la transacción en caso de error
          $this->conn->rollBack();
          throw new Exception("Error al crear el socio: " . $e->getMessage());
      }
  }

    // -------------------------------------------------
    // OBTENER TODOS LOS SOCIOS
    // -------------------------------------------------
    public function get() {
        $sql = "SELECT * FROM socio ORDER BY id_usuario DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // -------------------------------------------------
    // OBTENER SOCIO POR ID
    // -------------------------------------------------
public function getById($id) {
    if (!is_numeric($id)) {
        throw new Exception("ID inválido.");
    }

    try {
        $sql = "SELECT * FROM socio WHERE id_usuario = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":id" => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            throw new Exception("No se encontró un socio con el ID proporcionado.");
        }

        // Depuración: Verifica el resultado
        error_log("Resultado de la consulta: " . json_encode($result));

        return $result;
    } catch (PDOException $e) {
        throw new Exception("Error al obtener el socio: " . $e->getMessage());
    }
}

    // -------------------------------------------------
    // ACTUALIZAR SOCIO
    // -------------------------------------------------
    public function update($id, $data) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $this->validarSocio($data, "actualizar");

        $sql = "UPDATE socio 
                SET nombre = :nombre, apellido = :apellido, genero = :genero, 
                    documento = :documento, fecha_nacimiento = :fecha_nacimiento, 
                    id_tipo_socio = :id_tipo_socio, correo = :correo
                WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":nombre" => trim($data["nombre"]),
            ":apellido" => trim($data["apellido"]),
            ":genero" => $data["genero"],
            ":documento" => $data["documento"],
            ":fecha_nacimiento" => $data["fecha_nacimiento"],
            ":id_tipo_socio" => $data["id_tipo_socio"],
            ":correo" => $data["correo"] ?? null,
            ":id_usuario" => $id
        ]);
    }

    // -------------------------------------------------
    // ELIMINAR SOCIO
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }

        $sql = "DELETE FROM socio WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>