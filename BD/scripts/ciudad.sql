USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS ciudad_insert$$
CREATE PROCEDURE ciudad_insert (IN p_nombre VARCHAR(50))
BEGIN
    INSERT INTO ciudad(nombre) 
    VALUES (p_nombre);
END$$

DROP PROCEDURE IF EXISTS ciudad_update$$
CREATE PROCEDURE ciudad_update (IN p_id_ciudad INT, IN p_nombre VARCHAR(50))
BEGIN
    UPDATE ciudad 
    SET nombre = p_nombre
    WHERE id_ciudad = p_id_ciudad;
END$$

DROP PROCEDURE IF EXISTS ciudad_delete$$
CREATE PROCEDURE ciudad_delete (IN p_id_ciudad INT)
BEGIN
    DELETE FROM ciudad WHERE id_ciudad = p_id_ciudad;
END$$

DELIMITER ;