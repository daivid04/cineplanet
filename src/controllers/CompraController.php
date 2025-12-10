<?php
/**
 * CompraController - Controlador para procesar compras
 */

require_once __DIR__ . '/../models/CompraModel.php';

class CompraController {
    private $model;

    public function __construct($conn) {
        $this->model = new CompraModel($conn);
    }

    /**
     * Procesar una compra completa
     */
    public function procesarCompra($data) {
        // Validar datos requeridos
        $required = ['id_usuario', 'id_metodo', 'id_funcion', 'id_sala', 'id_sede', 'precio_total_boleto', 'asientos'];
        
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                return [
                    'success' => false,
                    'message' => "Campo requerido: $field"
                ];
            }
        }

        return $this->model->realizarCompra($data);
    }

    /**
     * Cancelar una compra
     */
    public function cancelarCompra($idCompra) {
        if (!$idCompra || !is_numeric($idCompra)) {
            return ['success' => false, 'message' => 'ID de compra inválido'];
        }

        return $this->model->cancelarCompra($idCompra);
    }

    /**
     * Verificar disponibilidad de asientos
     */
    public function verificarAsientos($asientosIds) {
        if (!is_array($asientosIds) || count($asientosIds) === 0) {
            return [
                'success' => false,
                'disponibles' => false,
                'message' => 'Lista de asientos vacía'
            ];
        }

        return $this->model->verificarAsientos($asientosIds);
    }

    /**
     * Obtener detalles de una compra
     */
    public function obtenerCompra($idCompra) {
        $compra = $this->model->getById($idCompra);
        
        if ($compra) {
            return ['success' => true, 'data' => $compra];
        }
        
        return ['success' => false, 'message' => 'Compra no encontrada'];
    }

    /**
     * Obtener historial de compras de un usuario
     */
    public function historialUsuario($idUsuario) {
        $compras = $this->model->getByUsuario($idUsuario);
        
        return [
            'success' => true,
            'data' => $compras,
            'count' => count($compras)
        ];
    }
}
?>
