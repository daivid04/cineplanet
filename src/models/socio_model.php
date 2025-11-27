<?php
// Actualicé este archivo para que incluya la 'contrasena'
require_once __DIR__ . "/usuario_model.php";

class SocioModel extends UsuarioModel {
    
    // -------------------------------------------------
    // VALIDAR DATOS ESPECÍFICOS DE SOCIO
    // -------------------------------------------------
    private function validarSocio($data, $modo = "insertar") {
        
        // Campos obligatorios para insertar
        $requeridos = ["nombre", "apellido", "genero", "documento", "fecha_nacimiento", "id_tipo_socio", "contrasena"];

        if ($modo === "insertar") {
            foreach ($requeridos as $campo) {
                if (!isset($data[$campo]) || trim($data[$campo]) === "") {
                    throw new Exception("El campo '$campo' es obligatorio.");
                }
            }
        }

        // Validar contraseña
        if (isset($data["contrasena"]) && strlen($data["contrasena"]) < 6) {
             throw new Exception("La contraseña debe tener al menos 6 caracteres.");
        }

        // Documento solo números
        if (isset($data["documento"]) && !preg_match("/^[0-9]{6,15}$/", $data["documento"])) {
            throw new Exception("Documento inválido. Debe tener de 6 a 15 dígitos.");
        }

        // Género permitido (Asumiendo que envías el valor completo, no solo 'M'/'F')
        if (isset($data["genero"]) && !in_array($data["genero"], ["Masculino", "Femenino", "Otro"])) {
            throw new Exception("Género inválido.");
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
    // INSERTAR SOCIO (ACTUALIZADO)
    // -------------------------------------------------
    public function create($data) {
      $this->validarSocio($data, "insertar");

      try {
          // Iniciar una transacción (ya estabas usando PDO, ¡excelente!)
          $this->conn->beginTransaction();

          // 1. Insertar en la tabla `usuario`
          $sqlUsuario = "INSERT INTO usuario (correo) VALUES (:correo)";
          $stmtUsuario = $this->conn->prepare($sqlUsuario);
          $stmtUsuario->execute([
              ":correo" => $data["correo"] ?? null
          ]);

          // Obtener el ID del usuario recién creado
          $idUsuario = $this->conn->lastInsertId();

          // 2. HASHEAR LA CONTRASEÑA (¡Importante para seguridad!)
          $contrasena_hash = password_hash($data["contrasena"], PASSWORD_DEFAULT);


          // 3. Insertar en la tabla `socio` (con la contraseña hasheada)
          $sqlSocio = "INSERT INTO socio (id_usuario, nombre, apellido, genero, documento, contrasena, fecha_nacimiento, id_tipo_socio)
                      VALUES (:id_usuario, :nombre, :apellido, :genero, :documento, :contrasena, :fecha_nacimiento, :id_tipo_socio)";
          
          $stmtSocio = $this->conn->prepare($sqlSocio);
          $stmtSocio->execute([
              ":id_usuario" => $idUsuario,
              ":nombre" => trim($data["nombre"]),
              ":apellido" => trim($data["apellido"]),
              ":genero" => $data["genero"],
              ":documento" => $data["documento"],
              ":contrasena" => $contrasena_hash, // Guardamos el hash
              ":fecha_nacimiento" => $data["fecha_nacimiento"],
              ":id_tipo_socio" => $data["id_tipo_socio"] ?? 1 // Asumimos 1 si no se provee
          ]);

          // Confirmar la transacción
          $this->conn->commit();

          return $idUsuario; // Retornar el ID del usuario creado
      } catch (Exception $e) {
          // Revertir la transacción en caso de error
          $this->conn->rollBack();
          // --- ESTA ES LA LÍNEA CORREGIDA ---
          throw new Exception("Error al crear el socio: " . $e->getMessage());
      }
  }

    // -------------------------------------------------
    // OBTENER TODOS LOS SOCIOS
    // -------------------------------------------------
    public function get() {
        $sql = "SELECT id_usuario, nombre, apellido, genero, documento, fecha_nacimiento, id_tipo_socio FROM socio ORDER BY id_usuario DESC";
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
            $sql = "SELECT id_usuario, nombre, apellido, genero, documento, fecha_nacimiento, id_tipo_socio FROM socio WHERE id_usuario = :id LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([":id" => $id]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                throw new Exception("No se encontró un socio con el ID proporcionado.");
            }
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

        // Faltaría actualizar 'usuario.correo'
        // Y opcionalmente actualizar contraseña si se provee

        $sql = "UPDATE socio 
                SET nombre = :nombre, apellido = :apellido, genero = :genero, 
                    documento = :documento, fecha_nacimiento = :fecha_nacimiento, 
                    id_tipo_socio = :id_tipo_socio
                WHERE id_usuario = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":nombre" => trim($data["nombre"]),
            ":apellido" => trim($data["apellido"]),
            ":genero" => $data["genero"],
            ":documento" => $data["documento"],
            ":fecha_nacimiento" => $data["fecha_nacimiento"],
            ":id_tipo_socio" => $data["id_tipo_socio"],
            ":id" => $id
        ]);
    }

    // -------------------------------------------------
    // ELIMINAR SOCIO
    // -------------------------------------------------
    public function delete($id) {
        if (!is_numeric($id)) {
            throw new Exception("ID inválido.");
        }
        
        // La BD está configurada con ON DELETE CASCADE,
        // por lo que borrar el 'usuario' debería borrar el 'socio'.
        $sql = "DELETE FROM usuario WHERE id_usuario = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}
?>