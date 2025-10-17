USE cineplanet;

DELIMITER $$

-- INSERT
DROP PROCEDURE IF EXISTS producto_combo_insert$$
CREATE PROCEDURE producto_combo_insert (IN p_id_producto INT, IN p_id_combo INT)
BEGIN
    INSERT INTO producto_combo(id_combo, id_producto) 
    VALUES (p_id_combo, p_id_producto);
END$$

-- UPDATE
DROP PROCEDURE IF EXISTS producto_combo_update$$
CREATE PROCEDURE producto_combo_update (IN p_id_productos_combos INT, IN p_id_producto INT, IN p_id_combo INT)
BEGIN
    UPDATE producto_combo 
    SET id_combo = p_id_combo
        id_producto = p_id_producto, 
    WHERE id_productos_combos = p_id_productos_combos;
END$$

-- DELETE
DROP PROCEDURE IF EXISTS producto_combo_delete$$
CREATE PROCEDURE producto_combo_delete (IN p_id_productos_combos INT)
BEGIN
    DELETE FROM producto_combo WHERE id_productos_combos = p_id_productos_combos;
END$$

DELIMITER ;