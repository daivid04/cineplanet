USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS producto_insert$$
CREATE PROCEDURE producto_insert (IN p_nombre VARCHAR(50), IN p_precio_unitario FLOAT)
BEGIN
    INSERT INTO producto(nombre, precio_unitario) 
    VALUES (p_nombre, p_precio_unitario);
END$$

DROP PROCEDURE IF EXISTS producto_update$$
CREATE PROCEDURE producto_update (IN p_id_producto INT, IN p_nombre VARCHAR(50), IN p_precio_unitario FLOAT)
BEGIN
    UPDATE producto SET nombre = p_nombre, precio_unitario = p_precio_unitario
    WHERE id_producto = p_id_producto;
END$$

DROP PROCEDURE IF EXISTS producto_delete$$
CREATE PROCEDURE producto_delete (IN p_id_producto INT)
BEGIN
    DELETE FROM producto WHERE id_producto = p_id_producto;
END$$

DELIMITER ;