USE cineplanet;

DELIMITER $$

-- Procedimiento para INSERTAR un nuevo producto_sede
DROP PROCEDURE IF EXISTS producto_sede_insert$$
CREATE PROCEDURE producto_sede_insert(
    IN p_stock INT,
    IN p_id_producto INT,
    IN p_id_sede INT
)
BEGIN
    INSERT INTO producto_sede(stock, id_producto, id_sede) 
    VALUES (p_stock, p_id_producto, p_id_sede);
END$$

-- Procedimiento para ACTUALIZAR un producto_sede existente
DROP PROCEDURE IF EXISTS producto_sede_update$$
CREATE PROCEDURE producto_sede_update(
    IN p_id_producto_sede INT,
    IN p_stock INT,
    IN p_id_producto INT,
    IN p_id_sede INT
)
BEGIN
    UPDATE producto_sede 
    SET stock = p_stock,
        id_producto = p_id_producto,
        id_sede = p_id_sede
    WHERE id_producto_sede = p_id_producto_sede;
END$$

-- Procedimiento para ELIMINAR un producto_sede
DROP PROCEDURE IF EXISTS producto_sede_delete$$
CREATE PROCEDURE producto_sede_delete(
    IN p_id_producto_sede INT
)
BEGIN
    DELETE FROM producto_sede WHERE id_producto_sede = p_id_producto_sede;
END$$

DELIMITER ;