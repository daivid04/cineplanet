-- =====================================================
-- STORED PROCEDURES PARA COMPRA
-- Transacciones con COMMIT/ROLLBACK
-- =====================================================

DELIMITER //

-- =====================================================
-- SP: Realizar Compra Completa
-- Inserta en: compra, compra_boleto, descripcion_asiento, compra_productos
-- =====================================================
DROP PROCEDURE IF EXISTS sp_realizar_compra//

CREATE PROCEDURE sp_realizar_compra(
    -- Datos generales
    IN p_id_usuario INT,
    IN p_id_metodo INT,
    
    -- Datos de función
    IN p_id_funcion INT,
    IN p_id_sala INT,
    IN p_id_sede INT,
    IN p_precio_total_boleto DECIMAL(10,2),
    
    -- JSON con asientos: [{"id_asiento": 1, "id_tipo_entrada": 1}, ...]
    IN p_asientos_json JSON,
    
    -- JSON con dulcería: [{"id_combo": 1, "precio": 35.00}, {"id_combo": 2, "precio": 55.00}]
    IN p_dulceria_json JSON,
    IN p_precio_total_dulceria DECIMAL(10,2),
    
    -- Outputs
    OUT p_id_compra INT,
    OUT p_resultado VARCHAR(255)
)
BEGIN
    DECLARE v_id_compra_boleto INT;
    DECLARE v_id_compra_productos INT;
    DECLARE v_asiento_id INT;
    DECLARE v_tipo_entrada_id INT;
    DECLARE v_combo_id INT;
    DECLARE v_combo_precio DECIMAL(10,2);
    DECLARE v_i INT DEFAULT 0;
    DECLARE v_count_asientos INT;
    DECLARE v_count_dulceria INT;
    DECLARE v_error_msg VARCHAR(255);
    
    -- Handler para errores
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET p_id_compra = NULL;
        SET p_resultado = CONCAT('ERROR: ', v_error_msg);
    END;
    
    -- Iniciar transacción
    START TRANSACTION;
    
    -- ========================================
    -- 1. INSERTAR COMPRA PRINCIPAL
    -- ========================================
    INSERT INTO compra (fecha, id_usuario, id_metodo)
    VALUES (CURDATE(), p_id_usuario, p_id_metodo);
    
    SET p_id_compra = LAST_INSERT_ID();
    
    -- ========================================
    -- 2. INSERTAR COMPRA_BOLETO (tickets)
    -- ========================================
    INSERT INTO compra_boleto (precio_total_boleto, id_compra, id_funcion, id_sala, id_sede)
    VALUES (p_precio_total_boleto, p_id_compra, p_id_funcion, p_id_sala, p_id_sede);
    
    SET v_id_compra_boleto = LAST_INSERT_ID();
    
    -- ========================================
    -- 3. INSERTAR DESCRIPCION_ASIENTO (butacas)
    -- ========================================
    SET v_count_asientos = JSON_LENGTH(p_asientos_json);
    SET v_i = 0;
    
    WHILE v_i < v_count_asientos DO
        SET v_asiento_id = JSON_EXTRACT(p_asientos_json, CONCAT('$[', v_i, '].id_asiento'));
        SET v_tipo_entrada_id = JSON_EXTRACT(p_asientos_json, CONCAT('$[', v_i, '].id_tipo_entrada'));
        
        -- Verificar que el asiento no esté ocupado
        IF (SELECT estado FROM asiento WHERE id_asiento = v_asiento_id) = 'ocupado' THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'Uno o más asientos ya están ocupados';
        END IF;
        
        -- Insertar descripcion_asiento
        INSERT INTO descripcion_asiento (id_asiento, id_compra_boleto, id_tipo_entrada)
        VALUES (v_asiento_id, v_id_compra_boleto, v_tipo_entrada_id);
        
        -- Actualizar estado del asiento a ocupado
        UPDATE asiento SET estado = 'ocupado' WHERE id_asiento = v_asiento_id;
        
        SET v_i = v_i + 1;
    END WHILE;
    
    -- ========================================
    -- 4. INSERTAR COMPRA_PRODUCTOS (dulcería)
    -- ========================================
    IF p_dulceria_json IS NOT NULL AND JSON_LENGTH(p_dulceria_json) > 0 THEN
        -- Crear registro de compra_productos
        INSERT INTO compra_productos (precio_compra, id_compra)
        VALUES (p_precio_total_dulceria, p_id_compra);
        
        SET v_id_compra_productos = LAST_INSERT_ID();
        
        -- Insertar cada item de dulcería en compra_cliente
        SET v_count_dulceria = JSON_LENGTH(p_dulceria_json);
        SET v_i = 0;
        
        WHILE v_i < v_count_dulceria DO
            SET v_combo_id = JSON_EXTRACT(p_dulceria_json, CONCAT('$[', v_i, '].id_combo'));
            
            -- Insertar en compra_cliente (usando id_producto_sede = 1 como default)
            INSERT INTO compra_cliente (tipo, id_combo, id_producto_sede, id_compra_productos)
            VALUES ('Combo', v_combo_id, 1, v_id_compra_productos);
            
            SET v_i = v_i + 1;
        END WHILE;
    END IF;
    
    -- ========================================
    -- COMMIT si todo va bien
    -- ========================================
    COMMIT;
    SET p_resultado = CONCAT('Compra #', p_id_compra, ' realizada exitosamente');
    
END//


-- =====================================================
-- SP: Cancelar Compra (liberar asientos)
-- =====================================================
DROP PROCEDURE IF EXISTS sp_cancelar_compra//

CREATE PROCEDURE sp_cancelar_compra(
    IN p_id_compra INT,
    OUT p_resultado VARCHAR(255)
)
BEGIN
    DECLARE v_count INT;
    DECLARE v_error_msg VARCHAR(255);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_error_msg = MESSAGE_TEXT;
        ROLLBACK;
        SET p_resultado = CONCAT('ERROR: ', v_error_msg);
    END;
    
    START TRANSACTION;
    
    -- Verificar que existe la compra
    SELECT COUNT(*) INTO v_count FROM compra WHERE id_compra = p_id_compra;
    IF v_count = 0 THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Compra no encontrada';
    END IF;
    
    -- Liberar asientos
    UPDATE asiento a
    JOIN descripcion_asiento da ON a.id_asiento = da.id_asiento
    JOIN compra_boleto cb ON da.id_compra_boleto = cb.id_compra_boleto
    SET a.estado = 'libre'
    WHERE cb.id_compra = p_id_compra;
    
    -- Eliminar registros (cascada manejará descripcion_asiento y compra_productos)
    DELETE FROM compra WHERE id_compra = p_id_compra;
    
    COMMIT;
    SET p_resultado = CONCAT('Compra #', p_id_compra, ' cancelada exitosamente');
    
END//


-- =====================================================
-- SP: Verificar disponibilidad de asientos
-- =====================================================
DROP PROCEDURE IF EXISTS sp_verificar_asientos//

CREATE PROCEDURE sp_verificar_asientos(
    IN p_asientos_json JSON,
    OUT p_disponibles BOOLEAN,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_i INT DEFAULT 0;
    DECLARE v_count INT;
    DECLARE v_asiento_id INT;
    DECLARE v_estado VARCHAR(20);
    
    SET p_disponibles = TRUE;
    SET p_mensaje = 'Todos los asientos están disponibles';
    
    SET v_count = JSON_LENGTH(p_asientos_json);
    
    WHILE v_i < v_count AND p_disponibles = TRUE DO
        SET v_asiento_id = JSON_EXTRACT(p_asientos_json, CONCAT('$[', v_i, ']'));
        
        SELECT estado INTO v_estado FROM asiento WHERE id_asiento = v_asiento_id;
        
        IF v_estado != 'libre' THEN
            SET p_disponibles = FALSE;
            SET p_mensaje = CONCAT('Asiento ID ', v_asiento_id, ' no está disponible (', v_estado, ')');
        END IF;
        
        SET v_i = v_i + 1;
    END WHILE;
    
END//

DELIMITER ;

-- =====================================================
-- EJEMPLO DE USO:
-- =====================================================
-- CALL sp_realizar_compra(
--     1,  -- id_usuario
--     1,  -- id_metodo (ej: Tarjeta de crédito)
--     5,  -- id_funcion
--     1,  -- id_sala
--     1,  -- id_sede
--     45.00, -- precio_total_boleto
--     '[{"id_asiento": 10, "id_tipo_entrada": 1}, {"id_asiento": 11, "id_tipo_entrada": 3}]',  -- asientos
--     '[{"id_combo": 1, "precio": 35.00}]',  -- dulcería
--     35.00,  -- precio_total_dulceria
--     @id_compra,
--     @resultado
-- );
-- SELECT @id_compra, @resultado;
