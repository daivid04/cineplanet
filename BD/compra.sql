USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS compra_insert$$
CREATE PROCEDURE compra_insert(
    IN p_fecha DATE,
    IN p_id_usuario INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 'Error al insertar compra' AS mensaje, NULL AS id_compra_insertada;
    END;
    
    START TRANSACTION;
    
    INSERT INTO compra(fecha, id_usuario) 
    VALUES (p_fecha, p_id_usuario);
    
    SELECT LAST_INSERT_ID() AS id_compra_insertada;
    
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS compra_update$$
CREATE PROCEDURE compra_update(
    IN p_id_compra INT,
    IN p_fecha DATE,
    IN p_id_usuario INT
)
BEGIN
    DECLARE v_affected_rows INT;
    
    UPDATE compra 
    SET fecha = p_fecha, 
        id_usuario = p_id_usuario
    WHERE id_compra = p_id_compra;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 'Compra actualizada correctamente' AS mensaje, v_affected_rows AS filas_afectadas;
    ELSE
        SELECT 'No se encontró la compra especificada' AS mensaje, 0 AS filas_afectadas;
    END IF;
END$$

DROP PROCEDURE IF EXISTS compra_delete$$
CREATE PROCEDURE compra_delete(IN p_id_compra INT)
BEGIN
    DECLARE v_affected_rows INT;
    
    DELETE FROM compra WHERE id_compra = p_id_compra;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 'Compra eliminada correctamente' AS mensaje, v_affected_rows AS filas_afectadas;
    ELSE
        SELECT 'No se encontró la compra especificada' AS mensaje, 0 AS filas_afectadas;
    END IF;
END$$



DROP PROCEDURE IF EXISTS compra_boleto_insert$$
CREATE PROCEDURE compra_boleto_insert(
    IN p_precio_total_boleto DECIMAL(10,2),
    IN p_id_compra INT,
    IN p_id_funcion INT,
    IN p_id_descripcion INT,
    IN p_id_sala INT,
    IN p_id_sede INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'Error al insertar boleto' AS mensaje, NULL AS id_compra_boleto_insertado;
    END;
    
    INSERT INTO compra_boleto(precio_total_boleto, id_compra, id_funcion, id_descripcion, id_sala, id_sede) 
    VALUES (p_precio_total_boleto, p_id_compra, p_id_funcion, p_id_descripcion, p_id_sala, p_id_sede);
    
    SELECT LAST_INSERT_ID() AS id_compra_boleto_insertado;
END$$

DROP PROCEDURE IF EXISTS compra_boleto_update$$
CREATE PROCEDURE compra_boleto_update(
    IN p_id_compra_boleto INT,
    IN p_precio_total_boleto DECIMAL(10,2),
    IN p_id_compra INT,
    IN p_id_funcion INT,
    IN p_id_descripcion INT,
    IN p_id_sala INT,
    IN p_id_sede INT
)
BEGIN
    DECLARE v_affected_rows INT;
    
    UPDATE compra_boleto 
    SET precio_total_boleto = p_precio_total_boleto,
        id_compra = p_id_compra,
        id_funcion = p_id_funcion,
        id_descripcion = p_id_descripcion,
        id_sala = p_id_sala,
        id_sede = p_id_sede
    WHERE id_compra_boleto = p_id_compra_boleto;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 'Compra de boleto actualizada correctamente' AS mensaje;
    ELSE
        SELECT 'No se encontró el boleto especificado' AS mensaje;
    END IF;
END$$

DROP PROCEDURE IF EXISTS compra_boleto_delete$$
CREATE PROCEDURE compra_boleto_delete(IN p_id_compra_boleto INT)
BEGIN
    DECLARE v_affected_rows INT;
    
    DELETE FROM compra_boleto WHERE id_compra_boleto = p_id_compra_boleto;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 1 AS success, 'Compra de boleto eliminada correctamente' AS mensaje;
    ELSE
        SELECT 0 AS success, 'No se encontró el boleto especificado' AS mensaje;
    END IF;
END$$



DROP PROCEDURE IF EXISTS compra_productos_insert$$
CREATE PROCEDURE compra_productos_insert(
    IN p_precio_compra DECIMAL(10,2),
    IN p_id_compra INT,
    IN p_id_producto INT,
    IN p_id_combo INT,
    IN p_id_descripcion_de_compra INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SELECT 'Error al insertar producto' AS mensaje, NULL AS id_precio_productos_insertado;
    END;
    
    INSERT INTO compra_productos(precio_compra, id_compra, id_producto, id_combo, id_descripcion_de_compra) 
    VALUES (p_precio_compra, p_id_compra, p_id_producto, p_id_combo, p_id_descripcion_de_compra);
    
    SELECT LAST_INSERT_ID() AS id_precio_productos_insertado;
END$$

DROP PROCEDURE IF EXISTS compra_productos_update$$
CREATE PROCEDURE compra_productos_update(
    IN p_id_precio_productos INT,
    IN p_precio_compra DECIMAL(10,2),
    IN p_id_compra INT,
    IN p_id_producto INT,
    IN p_id_combo INT,
    IN p_id_descripcion_de_compra INT
)
BEGIN
    DECLARE v_affected_rows INT;
    
    UPDATE compra_productos 
    SET precio_compra = p_precio_compra,
        id_compra = p_id_compra,
        id_producto = p_id_producto,
        id_combo = p_id_combo,
        id_descripcion_de_compra = p_id_descripcion_de_compra
    WHERE id_precio_productos = p_id_precio_productos;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 'Compra de productos actualizada correctamente' AS mensaje;
    ELSE
        SELECT 'No se encontró el producto especificado' AS mensaje;
    END IF;
END$$

DROP PROCEDURE IF EXISTS compra_productos_delete$$
CREATE PROCEDURE compra_productos_delete(IN p_id_precio_productos INT)
BEGIN
    DECLARE v_affected_rows INT;
    
    DELETE FROM compra_productos WHERE id_precio_productos = p_id_precio_productos;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        SELECT 1 AS success, 'Compra de productos eliminada correctamente' AS mensaje;
    ELSE
        SELECT 0 AS success, 'No se encontró el producto especificado' AS mensaje;
    END IF;
END$$



DROP PROCEDURE IF EXISTS compra_completa_insert$$
CREATE PROCEDURE compra_completa_insert(
    IN p_fecha DATE,
    IN p_id_usuario INT,
    IN p_boletos JSON,  
    IN p_productos JSON
)
BEGIN
    DECLARE v_id_compra INT;
    DECLARE v_index INT DEFAULT 0;
    DECLARE v_boletos_count INT;
    DECLARE v_productos_count INT;
    DECLARE v_precio DECIMAL(10,2);
    DECLARE v_id_funcion INT;
    DECLARE v_id_descripcion INT;
    DECLARE v_id_sala INT;
    DECLARE v_id_sede INT;
    DECLARE v_id_producto INT;
    DECLARE v_id_combo INT;
    DECLARE v_id_descripcion_compra INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS success, 'Error en la transacción de compra completa' AS mensaje, NULL AS id_compra_completa_insertada;
    END;
    
    START TRANSACTION;
    
    -- 1. Crear la compra principal
    INSERT INTO compra(fecha, id_usuario) VALUES (p_fecha, p_id_usuario);
    SET v_id_compra = LAST_INSERT_ID();
    
    -- 2. Procesar boletos si existen
    IF p_boletos IS NOT NULL AND JSON_TYPE(p_boletos) = 'ARRAY' THEN
        SET v_boletos_count = JSON_LENGTH(p_boletos);
        SET v_index = 0;
        
        WHILE v_index < v_boletos_count DO
            SET v_id_funcion = JSON_EXTRACT(p_boletos, CONCAT('$[', v_index, '].id_funcion'));
            SET v_id_descripcion = JSON_EXTRACT(p_boletos, CONCAT('$[', v_index, '].id_descripcion'));
            SET v_id_sala = JSON_EXTRACT(p_boletos, CONCAT('$[', v_index, '].id_sala'));
            SET v_id_sede = JSON_EXTRACT(p_boletos, CONCAT('$[', v_index, '].id_sede'));
            SET v_precio = JSON_EXTRACT(p_boletos, CONCAT('$[', v_index, '].precio'));
            
            INSERT INTO compra_boleto(precio_total_boleto, id_compra, id_funcion, id_descripcion, id_sala, id_sede)
            VALUES (v_precio, v_id_compra, v_id_funcion, v_id_descripcion, v_id_sala, v_id_sede);
            
            SET v_index = v_index + 1;
        END WHILE;
    END IF;
    
    -- 3. Procesar productos si existen
    IF p_productos IS NOT NULL AND JSON_TYPE(p_productos) = 'ARRAY' THEN
        SET v_productos_count = JSON_LENGTH(p_productos);
        SET v_index = 0;
        
        WHILE v_index < v_productos_count DO
            SET v_id_producto = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_index, '].id_producto')));
            SET v_id_combo = JSON_UNQUOTE(JSON_EXTRACT(p_productos, CONCAT('$[', v_index, '].id_combo')));
            SET v_id_descripcion_compra = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, '].id_descripcion_de_compra'));
            SET v_precio = JSON_EXTRACT(p_productos, CONCAT('$[', v_index, '].precio'));
            
            -- Convertir 'null' string a NULL real
            IF v_id_producto = 'null' THEN SET v_id_producto = NULL; END IF;
            IF v_id_combo = 'null' THEN SET v_id_combo = NULL; END IF;
            
            INSERT INTO compra_productos(precio_compra, id_compra, id_producto, id_combo, id_descripcion_de_compra)
            VALUES (v_precio, v_id_compra, v_id_producto, v_id_combo, v_id_descripcion_compra);
            
            SET v_index = v_index + 1;
        END WHILE;
    END IF;
    
    COMMIT;
    
    SELECT 1 AS success, 'Compra completa exitosa' AS mensaje, v_id_compra AS id_compra_completa_insertada;
END$$

DELIMITER ;