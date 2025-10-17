USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS idioma_insert$$
CREATE PROCEDURE idioma_insert (IN nombreIdioma VARCHAR(39))
BEGIN
    INSERT INTO idioma(idioma) VALUES (nombreIdioma);
END$$

DROP PROCEDURE IF EXISTS idioma_update$$
CREATE PROCEDURE idioma_update (IN idIdioma INT, IN nombreIdioma VARCHAR(39))
BEGIN
    UPDATE idioma SET idioma = nombreIdioma WHERE id_idioma = idIdioma;
END$$

DROP PROCEDURE IF EXISTS idioma_delete$$
CREATE PROCEDURE idioma_delete (IN idIdioma INT)
BEGIN
    DELETE FROM idioma WHERE id_idioma = idIdioma;
END$$

DELIMITER ;