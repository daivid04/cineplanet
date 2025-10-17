USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS funcion_insert$$
CREATE PROCEDURE funcion_insert (IN p_fecha DATE, IN p_hora TIME, IN p_id_pelicula INT, IN p_id_sala INT)
BEGIN
    INSERT INTO funcion(fecha, hora, id_pelicula, id_sala) 
    VALUES (p_fecha, p_hora, p_id_pelicula, p_id_sala);
END$$

DROP PROCEDURE IF EXISTS funcion_update$$
CREATE PROCEDURE funcion_update (IN p_id_funcion INT, IN p_fecha DATE, IN p_hora TIME, IN p_id_pelicula INT, IN p_id_sala INT)
BEGIN
    UPDATE funcion 
    SET fecha = p_fecha, 
        hora = p_hora,
        id_pelicula = p_id_pelicula,
        id_sala = p_id_sala
    WHERE id_funcion = p_id_funcion;
END$$

DROP PROCEDURE IF EXISTS funcion_delete$$
CREATE PROCEDURE funcion_delete (IN p_id_funcion INT)
BEGIN
    DELETE FROM funcion WHERE id_funcion = p_id_funcion;
END$$

DELIMITER ;