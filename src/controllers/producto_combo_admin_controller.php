<?php
/**
 * Controlador para gestión de Productos en Combos (Admin)
 * Tabla intermedia: producto_combo
 */

require_once __DIR__ . '/../models/ProductoComboAdminModel.php';

class ProductoComboAdminController {
    private $model;

    public function __construct() {
        global $conn;
        $this->model = new ProductoComboAdminModel($conn);
    }

    /**
     * Obtener todas las relaciones producto-combo
     */
    public function getAll() {
        try {
            $resultado = $this->model->getAll();
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener las relaciones: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener una relación por ID
     */
    public function getById($id) {
        try {
            if (!$id || !is_numeric($id)) {
                return [
                    'success' => false,
                    'message' => 'ID de relación inválido'
                ];
            }

            $resultado = $this->model->getById($id);
            
            if ($resultado) {
                return [
                    'success' => true,
                    'data' => $resultado
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Relación no encontrada'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener la relación: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener productos de un combo específico
     */
    public function getByCombo($idCombo) {
        try {
            if (!$idCombo || !is_numeric($idCombo)) {
                return [
                    'success' => false,
                    'message' => 'ID de combo inválido'
                ];
            }

            $resultado = $this->model->getProductosByCombo($idCombo);
            
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener productos del combo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener todos los combos con sus productos agrupados
     */
    public function getCombosConProductos() {
        try {
            $resultado = $this->model->getAllGroupedByCombo();
            
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener combos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener productos disponibles para agregar a un combo
     */
    public function getProductosDisponibles($idCombo = null) {
        try {
            $resultado = $this->model->getProductosSede();
            
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener productos disponibles: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Agregar un producto a un combo
     */
    public function addProductoToCombo($data) {
        try {
            // Validar campos requeridos
            if (empty($data['id_combo'])) {
                return [
                    'success' => false,
                    'message' => 'El combo es requerido'
                ];
            }

            if (empty($data['id_producto_sede'])) {
                return [
                    'success' => false,
                    'message' => 'El producto es requerido'
                ];
            }

            // Validar que sean numéricos
            if (!is_numeric($data['id_combo']) || !is_numeric($data['id_producto_sede'])) {
                return [
                    'success' => false,
                    'message' => 'IDs inválidos'
                ];
            }

            $resultado = $this->model->create([
                'id_combo' => intval($data['id_combo']),
                'id_producto_sede' => intval($data['id_producto_sede'])
            ]);

            return $resultado;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al agregar producto al combo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Agregar múltiples productos a un combo
     */
    public function addMultiplesProductos($data) {
        try {
            // Validar campos requeridos
            if (empty($data['id_combo'])) {
                return [
                    'success' => false,
                    'message' => 'El combo es requerido'
                ];
            }

            if (empty($data['productos']) || !is_array($data['productos'])) {
                return [
                    'success' => false,
                    'message' => 'Debe seleccionar al menos un producto'
                ];
            }

            $resultado = $this->model->createMultiple(
                intval($data['id_combo']),
                $data['productos']
            );

            return $resultado;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al agregar productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Eliminar un producto de un combo (por ID de relación)
     */
    public function delete($id) {
        try {
            if (!$id || !is_numeric($id)) {
                return [
                    'success' => false,
                    'message' => 'ID de relación inválido'
                ];
            }

            $resultado = $this->model->delete($id);
            return $resultado;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Actualizar todos los productos de un combo (reemplazar lista completa)
     */
    public function updateProductosCombo($data) {
        try {
            if (empty($data['id_combo'])) {
                return [
                    'success' => false,
                    'message' => 'El combo es requerido'
                ];
            }

            if (!isset($data['productos']) || !is_array($data['productos'])) {
                return [
                    'success' => false,
                    'message' => 'La lista de productos es requerida'
                ];
            }

            $resultado = $this->model->replaceProductosCombo(
                intval($data['id_combo']),
                $data['productos']
            );

            return $resultado;
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al actualizar productos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener lista de combos para dropdown
     */
    public function getCombos() {
        try {
            $resultado = $this->model->getCombos(false);
            
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener combos: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener sedes para filtros
     */
    public function getSedes() {
        try {
            $resultado = $this->model->getSedes();
            
            return [
                'success' => true,
                'data' => $resultado
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener sedes: ' . $e->getMessage()
            ];
        }
    }
}
