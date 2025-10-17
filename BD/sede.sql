USE cineplanet;

DELIMITER $$


DROP PROCEDURE IF EXISTS sede_insert$$
CREATE PROCEDURE sede_insert(
    IN p_nombre VARCHAR(50),
    IN p_id_ciudad INT
)
BEGIN
    INSERT INTO sede(nombre, id_ciudad) 
    VALUES (p_nombre, p_id_ciudad);
    
    SELECT LAST_INSERT_ID() AS id_sede_insertada;
END$$


DROP PROCEDURE IF EXISTS sede_update$$
CREATE PROCEDURE sede_update(
    IN p_id_sede INT,
    IN p_nombre VARCHAR(50),
    IN p_id_ciudad INT
)
BEGIN
    UPDATE sede 
    SET nombre = p_nombre, 
        id_ciudad = p_id_ciudad
    WHERE id_sede = p_id_sede;
    
    SELECT 'Sede actualizada correctamente' AS mensaje;
END$$



DROP PROCEDURE IF EXISTS sede_delete$$
CREATE PROCEDURE sede_delete(IN p_id_sede INT)
BEGIN
    DELETE FROM sede WHERE id_sede = p_id_sede;
    
    SELECT 'Sede eliminada correctamente' AS mensaje;
END$$

DELIMITER ;