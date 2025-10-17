USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS idioma_pelicula_insert$$
CREATE PROCEDURE idioma_pelicula_insert (IN p_id_idioma INT, IN p_id_pelicula INT)
BEGIN
    INSERT INTO idiomas_pelicula(id_pelicula, id_idioma) 
    VALUES (p_id_pelicula, p_id_idioma);
END$$

DROP PROCEDURE IF EXISTS idioma_pelicula_update$$
CREATE PROCEDURE idioma_pelicula_update (IN p_id_idiomas_pelicula INT, IN p_id_idioma INT, IN p_id_pelicula INT)
BEGIN
    UPDATE idiomas_pelicula 
    SET id_idioma = p_id_idioma, id_pelicula = p_id_pelicula
    WHERE id_idiomas_pelicula = p_id_idiomas_pelicula;
END$$

DROP PROCEDURE IF EXISTS idioma_pelicula_delete$$
CREATE PROCEDURE idioma_pelicula_delete (IN p_id_idiomas_pelicula INT)
BEGIN
    DELETE FROM idiomas_pelicula WHERE id_idiomas_pelicula = p_id_idiomas_pelicula;
END$$

DELIMITER ;