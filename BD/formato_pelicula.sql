USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS formato_pelicula_insert$$
CREATE PROCEDURE formato_pelicula_insert (IN p_id_formato INT, IN p_id_pelicula INT)
BEGIN
    INSERT INTO formato_pelicula(id_pelicula, id_formato) 
    VALUES (p_id_pelicula, p_id_formato);
END$$

DROP PROCEDURE IF EXISTS formato_pelicula_update$$
CREATE PROCEDURE formato_pelicula_update (IN p_id_formato_pelicula INT, IN p_id_formato INT, IN p_id_pelicula INT)
BEGIN
    UPDATE formato_pelicula 
    SET id_formato = p_id_formato, id_pelicula = p_id_pelicula
    WHERE id_formato_pelicula = p_id_formato_pelicula;
END$$

DROP PROCEDURE IF EXISTS formato_pelicula_delete$$
CREATE PROCEDURE formato_pelicula_delete (IN p_id_formato_pelicula INT)
BEGIN
    DELETE FROM formato_pelicula WHERE id_formato_pelicula = p_id_formato_pelicula;
END$$

DELIMITER ;