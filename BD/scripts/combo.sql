USE cineplanet;

DELIMITER $$


DROP PROCEDURE IF EXISTS combo_completo_insert$$
CREATE PROCEDURE combo_completo_insert( IN p_precio FLOAT, IN p_nombre VARCHAR(50), IN p_productos TEXT )
BEGIN
    DECLARE v_id_combo INT;
    DECLARE v_id_producto INT;
    DECLARE v_pos INT DEFAULT 1;
    DECLARE v_substr VARCHAR(10);
    
    INSERT INTO combos(precio, nombre) 
    VALUES (p_precio, p_nombre);
    
    SET v_id_combo = LAST_INSERT_ID();
    
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_productos, ',', v_pos), ',', -1);
        IF v_substr != '' THEN
            SET v_id_producto = CAST(v_substr AS UNSIGNED);
            CALL producto_combo_insert(v_id_producto, v_id_combo);
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
    SELECT v_id_combo AS id_combo_insertado;
END$$



DROP PROCEDURE IF EXISTS combo_completo_update$$
CREATE PROCEDURE combo_completo_update(IN p_id_combo INT, IN p_precio FLOAT, IN p_nombre VARCHAR(50), IN p_productos TEXT
)
BEGIN
    UPDATE combos 
    SET precio = p_precio, 
        nombre = p_nombre
    WHERE id_combo = p_id_combo;
    
    DELETE FROM producto_combo WHERE id_combo = p_id_combo;
    
    SET v_pos = 1;
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_productos, ',', v_pos), ',', -1);
        IF v_substr != '' THEN
            SET v_id_producto = CAST(v_substr AS UNSIGNED);
            CALL producto_combo_insert(v_id_producto, p_id_combo);
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
END$$



DROP PROCEDURE IF EXISTS combo_completo_delete$$
CREATE PROCEDURE combo_completo_delete(IN p_id_combo INT)
BEGIN
    DELETE FROM combos WHERE id_combo = p_id_combo;
    
END$$

DELIMITER ;