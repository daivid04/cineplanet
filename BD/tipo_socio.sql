USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS tipo_socio_insert$$
CREATE PROCEDURE tipo_socio_insert (IN nombre VARCHAR(50), IN desc_boleto FLOAT, IN desc_dulce FLOAT)
BEGIN
    INSERT INTO tipo_socio(nombre, desc_dulces, desc_boleto) 
    VALUES (nombre, desc_dulce, desc_boleto);
END$$

DROP PROCEDURE IF EXISTS tipo_socio_update$$
CREATE PROCEDURE tipo_socio_update (IN id_tipo_socio INT, IN nombre VARCHAR(50), IN desc_boleto FLOAT, IN desc_dulce FLOAT)
BEGIN
    UPDATE tipo_socio 
    SET nombre = nombre, 
        desc_boleto = desc_boleto,
        desc_dulces = desc_dulce
    WHERE id_tipo_socio = id_tipo_socio;
END$$

DROP PROCEDURE IF EXISTS tipo_socio_delete$$
CREATE PROCEDURE tipo_socio_delete (IN id_tipo_socio INT)
BEGIN
    DELETE FROM tipo_socio WHERE id_tipo_socio = id_tipo_socio;
END$$

DELIMITER ;