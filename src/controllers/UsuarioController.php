        $this->socioModel = new SocioModel($conn);
        $this->invitadoModel = new InvitadoModel($conn);
    }

    // -------------------------------------------------
    // OBTENER TODOS LOS USUARIOS (SOCIOS + INVITADOS)
    // -------------------------------------------------
    public function getAll() {
        try {
            $socios = $this->socioModel->getAll();
            $invitados = $this->invitadoModel->getAll();

            // Agregar tipo de usuario para identificarlos
            foreach ($socios as &$socio) {
                $socio['tipo_usuario'] = 'socio';
            }

            foreach ($invitados as &$invitado) {
                $invitado['tipo_usuario'] = 'invitado';
            }

            return [
                'socios' => $socios,
                'invitados' => $invitados,
                'total' => count($socios) + count($invitados)
            ];

        } catch (Exception $e) {
            throw new Exception("Error al obtener usuarios: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // OBTENER USUARIO POR ID Y TIPO
    // -------------------------------------------------
    public function getById($tipo, $id) {
        try {
            switch ($tipo) {
                case 'socio':
                    $usuario = $this->socioModel->getById($id);
                    if ($usuario) {
                        $usuario['tipo_usuario'] = 'socio';
                    }
                    return $usuario;

                case 'invitado':
                    $usuario = $this->invitadoModel->getById($id);
                    if ($usuario) {
                        $usuario['tipo_usuario'] = 'invitado';
                    }
                    return $usuario;

                default:
                    throw new Exception("Tipo de usuario no válido. Use 'socio' o 'invitado'.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // CREAR USUARIO (SOCIOS O INVITADOS)
    // -------------------------------------------------
    public function create($tipo, $data) {
        try {
            switch ($tipo) {
                case 'socio':
                    return $this->socioModel->create($data);

                case 'invitado':
                    return $this->invitadoModel->create($data);

                default:
                    throw new Exception("Tipo de usuario no válido. Use 'socio' o 'invitado'.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ACTUALIZAR USUARIO (SOCIOS O INVITADOS)
    // -------------------------------------------------
    public function update($tipo, $id, $data) {
        try {
            switch ($tipo) {
                case 'socio':
                    return $this->socioModel->update($id, $data);

                case 'invitado':
                    return $this->invitadoModel->update($id, $data);

                default:
                    throw new Exception("Tipo de usuario no válido. Use 'socio' o 'invitado'.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // ELIMINAR USUARIO (SOCIOS O INVITADOS)
    // -------------------------------------------------
    public function delete($tipo, $id) {
        try {
            switch ($tipo) {
                case 'socio':
                    return $this->socioModel->delete($id);

                case 'invitado':
                    return $this->invitadoModel->delete($id);

                default:
                    throw new Exception("Tipo de usuario no válido. Use 'socio' o 'invitado'.");
            }
        } catch (Exception $e) {
            throw new Exception("Error al eliminar usuario: " . $e->getMessage());
        }
    }

    // -------------------------------------------------
    // BUSCAR USUARIO POR CORREO (EN AMBAS TABLAS)
    // -------------------------------------------------
    public function searchByEmail($correo) {
        try {
            if (empty($correo)) {
                throw new Exception("El correo es requerido para la búsqueda.");
            }

            $resultados = [];

            // Buscar en socios
            $socios = $this->socioModel->getAll();
            foreach ($socios as $socio) {
                if (isset($socio['correo']) && stripos($socio['correo'], $correo) !== false) {
                    $socio['tipo_usuario'] = 'socio';
                    $resultados[] = $socio;
                }
            }

            // Buscar en invitados
            $invitados = $this->invitadoModel->getAll();
            foreach ($invitados as $invitado) {
                if (isset($invitado['correo']) && stripos($invitado['correo'], $correo) !== false) {
                    $invitado['tipo_usuario'] = 'invitado';
                    $resultados[] = $invitado;
                }
            }

            return [
                'resultados' => $resultados,
                'total' => count($resultados)
            ];

        } catch (Exception $e) {
            throw new Exception("Error en búsqueda por correo: " . $e->getMessage());
        }
    }
}
?>