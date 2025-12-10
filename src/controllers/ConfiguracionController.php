<?php
require_once __DIR__ . "/../models/EmpleadoModel.php";

class ConfiguracionController {
    private $empleadoModel;

    public function __construct($conn) {
        $this->empleadoModel = new EmpleadoModel($conn);
    }

    public function index() {
        // Simular usuario logueado (ID 1 por defecto para este MVP)
        // En un sistema real, esto vendría de $_SESSION['user_id']
        $userId = 1; 

        try {
            $user = $this->empleadoModel->getById($userId);
            
            if (!$user) {
                throw new Exception("Usuario no encontrado.");
            }

            return [
                "success" => true,
                "data" => $user
            ];

        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function updateProfile($data) {
        // Simular usuario logueado
        $userId = 1;

        try {
            // Validar datos básicos
            if (empty($data['nombre']) || empty($data['apellido']) || empty($data['correo'])) {
                throw new Exception("Nombre, apellido y correo son obligatorios.");
            }

            // Preparar datos para el modelo
            $updateData = [
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'correo' => $data['correo'],
                'numero' => $data['numero'] ?? ''
            ];

            $success = $this->empleadoModel->update($userId, $updateData);

            if ($success) {
                return [
                    "success" => true,
                    "message" => "Perfil actualizado exitosamente."
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "No se realizaron cambios."
                ];
            }

        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }

    public function updatePassword($data) {
        // Simular usuario logueado
        $userId = 1;

        try {
            if (empty($data['password']) || empty($data['confirm_password'])) {
                throw new Exception("La contraseña es obligatoria.");
            }

            if ($data['password'] !== $data['confirm_password']) {
                throw new Exception("Las contraseñas no coinciden.");
            }

            if (strlen($data['password']) < 6) {
                throw new Exception("La contraseña debe tener al menos 6 caracteres.");
            }

            // Hashear password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $success = $this->empleadoModel->updatePassword($userId, $hashedPassword);

            if ($success) {
                return [
                    "success" => true,
                    "message" => "Contraseña actualizada exitosamente."
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Error al actualizar la contraseña."
                ];
            }

        } catch (Exception $e) {
            return [
                "success" => false,
                "message" => $e->getMessage()
            ];
        }
    }
}
?>
