USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS trabajador_insert$$
CREATE PROCEDURE trabajador_insert (IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_correo VARCHAR(50), IN p_numero VARCHAR(10), IN p_dni VARCHAR(8), IN p_estado VARCHAR(20), IN p_tipo VARCHAR(50), IN p_id_sede INT)
BEGIN
    INSERT INTO trabajador(nombre, apellido, correo, numero, dni, estado, tipo, id_sede) 
    VALUES (p_nombre, p_apellido, p_correo, p_numero, p_dni, p_estado, p_tipo, p_id_sede);
END$$

DROP PROCEDURE IF EXISTS trabajador_update$$
CREATE PROCEDURE trabajador_update (IN p_id_trabajador INT, IN p_nombre VARCHAR(50), IN p_apellido VARCHAR(50), IN p_correo VARCHAR(50), IN p_numero VARCHAR(10), IN p_dni VARCHAR(8), IN p_estado VARCHAR(20), IN p_tipo VARCHAR(50), IN p_id_sede INT)
BEGIN
    UPDATE trabajador 
    SET nombre = p_nombre, apellido = p_apellido, correo = p_correo, numero = p_numero, dni = p_dni, estado = p_estado, tipo = p_tipo, id_sede = p_id_sede
    WHERE id_trabajador = p_id_trabajador;
END$$

DROP PROCEDURE IF EXISTS trabajador_delete$$
CREATE PROCEDURE trabajador_delete (IN p_id_trabajador INT)
BEGIN
    DELETE FROM trabajador WHERE id_trabajador = p_id_trabajador;
END$$

DELIMITER ;