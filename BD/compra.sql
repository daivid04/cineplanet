USE cineplanet;

DELIMITER $$

-- ========== PROCEDIMIENTOS BÁSICOS PARA COMPRA ==========
DROP PROCEDURE IF EXISTS compra_insert$$
CREATE PROCEDURE compra_insert(
    IN p_fecha DATE,
    IN p_id_usuario INT
)
BEGIN
    INSERT INTO compra(fecha, id_usuario) 
    VALUES (p_fecha, p_id_usuario);
    
    SELECT LAST_INSERT_ID() AS id_compra_insertada;
END$$

DROP PROCEDURE IF EXISTS compra_update$$
CREATE PROCEDURE compra_update(
    IN p_id_compra INT,
    IN p_fecha DATE,
    IN p_id_usuario INT
)
BEGIN
    UPDATE compra 
    SET fecha = p_fecha, 
        id_usuario = p_id_usuario
    WHERE id_compra = p_id_compra;
    
    SELECT 'Compra actualizada correctamente' AS mensaje;
END$$

DROP PROCEDURE IF EXISTS compra_delete$$
CREATE PROCEDURE compra_delete(IN p_id_compra INT)
BEGIN
    DELETE FROM compra WHERE id_compra = p_id_compra;
    
    SELECT 'Compra eliminada correctamente' AS mensaje;
END$$

-- ========== PROCEDIMIENTOS PARA COMPRA_BOLETO ==========
DROP PROCEDURE IF EXISTS compra_boleto_insert$$
CREATE PROCEDURE compra_boleto_insert(
    IN p_precio_total_boleto FLOAT,
    IN p_id_compra INT,
    IN p_id_funcion INT,
    IN p_id_descripcion INT,
    IN p_id_sala INT,
    IN p_id_sede INT
)
BEGIN
    INSERT INTO compra_boleto(precio_total_boleto, id_compra, id_funcion, id_descripcion, id_sala, id_sede) 
    VALUES (p_precio_total_boleto, p_id_compra, p_id_funcion, p_id_descripcion, p_id_sala, p_id_sede);
    
    SELECT LAST_INSERT_ID() AS id_compra_boleto_insertado;
END$$

DROP PROCEDURE IF EXISTS compra_boleto_update$$
CREATE PROCEDURE compra_boleto_update(
    IN p_id_compra_boleto INT,
    IN p_precio_total_boleto FLOAT,
    IN p_id_compra INT,
    IN p_id_funcion INT,
    IN p_id_descripcion INT,
    IN p_id_sala INT,
    IN p_id_sede INT
)
BEGIN
    UPDATE compra_boleto 
    SET precio_total_boleto = p_precio_total_boleto,
        id_compra = p_id_compra,
        id_funcion = p_id_funcion,
        id_descripcion = p_id_descripcion,
        id_sala = p_id_sala,
        id_sede = p_id_sede
    WHERE id_compra_boleto = p_id_compra_boleto;
    
    SELECT 'Compra de boleto actualizada correctamente' AS mensaje;
END$$

DROP PROCEDURE IF EXISTS compra_boleto_delete$$
CREATE PROCEDURE compra_boleto_delete(IN p_id_compra_boleto INT)
BEGIN
    DELETE FROM compra_boleto WHERE id_compra_boleto = p_id_compra_boleto;
    
    SELECT 'Compra de boleto eliminada correctamente' AS mensaje;
END$$

-- ========== PROCEDIMIENTOS PARA COMPRA_PRODUCTOS ==========
DROP PROCEDURE IF EXISTS compra_productos_insert$$
CREATE PROCEDURE compra_productos_insert(
    IN p_precio_compra FLOAT,
    IN p_id_compra INT,
    IN p_id_producto INT,
    IN p_id_combo INT,
    IN p_id_descripcion_de_compra INT
)
BEGIN
    INSERT INTO compra_productos(precio_compra, id_compra, id_producto, id_combo, id_descripcion_de_compra) 
    VALUES (p_precio_compra, p_id_compra, p_id_producto, p_id_combo, p_id_descripcion_de_compra);
    
    SELECT LAST_INSERT_ID() AS id_precio_productos_insertado;
END$$

DROP PROCEDURE IF EXISTS compra_productos_update$$
CREATE PROCEDURE compra_productos_update(
    IN p_id_precio_productos INT,
    IN p_precio_compra FLOAT,
    IN p_id_compra INT,
    IN p_id_producto INT,
    IN p_id_combo INT,
    IN p_id_descripcion_de_compra INT
)
BEGIN
    UPDATE compra_productos 
    SET precio_compra = p_precio_compra,
        id_compra = p_id_compra,
        id_producto = p_id_producto,
        id_combo = p_id_combo,
        id_descripcion_de_compra = p_id_descripcion_de_compra
    WHERE id_precio_productos = p_id_precio_productos;
    
    SELECT 'Compra de productos actualizada correctamente' AS mensaje;
END$$

DROP PROCEDURE IF EXISTS compra_productos_delete$$
CREATE PROCEDURE compra_productos_delete(IN p_id_precio_productos INT)
BEGIN
    DELETE FROM compra_productos WHERE id_precio_productos = p_id_precio_productos;
    
    SELECT 'Compra de productos eliminada correctamente' AS mensaje;
END$$

-- ========== PROCEDIMIENTO COMPLETO PARA COMPRA CON BOLETOS Y PRODUCTOS ==========
DROP PROCEDURE IF EXISTS compra_completa_insert$$
CREATE PROCEDURE compra_completa_insert(
    IN p_fecha DATE,
    IN p_id_usuario INT,
    IN p_boletos TEXT,  -- Formato: 'funcion_id,descripcion_id,sala_id,sede_id,precio;...'
    IN p_productos TEXT -- Formato: 'producto_id,combo_id,descripcion_compra_id,precio;...'
)
BEGIN
    DECLARE v_id_compra INT;
    DECLARE v_pos INT DEFAULT 1;
    DECLARE v_substr VARCHAR(100);
    
    -- 1. Crear la compra principal
    INSERT INTO compra(fecha, id_usuario) VALUES (p_fecha, p_id_usuario);
    SET v_id_compra = LAST_INSERT_ID();
    
    -- 2. Procesar boletos
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_boletos, ';', v_pos), ';', -1);
        IF v_substr != '' THEN
            -- Formato: funcion_id,descripcion_id,sala_id,sede_id,precio
            CALL compra_boleto_insert(
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 5), ',', -1), -- precio
                v_id_compra,
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 1), ',', -1), -- funcion_id
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 2), ',', -1), -- descripcion_id
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 3), ',', -1), -- sala_id
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 4), ',', -1)  -- sede_id
            );
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
    -- 3. Procesar productos
    SET v_pos = 1;
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_productos, ';', v_pos), ';', -1);
        IF v_substr != '' THEN
            -- Formato: producto_id,combo_id,descripcion_compra_id,precio
            CALL compra_productos_insert(
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 4), ',', -1), -- precio
                v_id_compra,
                NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 1), ',', -1), ''), -- producto_id
                NULLIF(SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 2), ',', -1), ''), -- combo_id
                SUBSTRING_INDEX(SUBSTRING_INDEX(v_substr, ',', 3), ',', -1)  -- descripcion_compra_id
            );
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
    SELECT v_id_compra AS id_compra_completa_insertada;
END$$

DELIMITER ;