USE cineplanet;

DELIMITER $$

-- Procedimiento para insertar película completa con idiomas y formatos
DROP PROCEDURE IF EXISTS pelicula_completa_insert$$
CREATE PROCEDURE pelicula_completa_insert(
    IN p_duracion INT,
    IN p_url_imagen VARCHAR(255),
    IN p_nombre VARCHAR(80),
    IN p_sinopsis TEXT,
    IN p_idiomas TEXT,  -- Ejemplo: '1,2,3'
    IN p_formatos TEXT  -- Ejemplo: '1,2,4'
)
BEGIN
    DECLARE v_id_pelicula INT;
    DECLARE v_id_idioma INT;
    DECLARE v_id_formato INT;
    DECLARE v_pos INT DEFAULT 1;
    DECLARE v_substr VARCHAR(10);
    
    -- 1. Insertar la película principal
    INSERT INTO pelicula(duracion, url_imagen, nombre, sinopsis) 
    VALUES (p_duracion, p_url_imagen, p_nombre, p_sinopsis);
    
    SET v_id_pelicula = LAST_INSERT_ID();
    
    -- 2. Insertar idiomas de la película
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_idiomas, ',', v_pos), ',', -1);
        IF v_substr != '' THEN
            SET v_id_idioma = CAST(v_substr AS UNSIGNED);
            INSERT INTO idiomas_pelicula(id_pelicula, id_idioma) VALUES (v_id_pelicula, v_id_idioma);
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
    -- 3. Insertar formatos de la película
    SET v_pos = 1;
    WHILE v_pos > 0 DO
        SET v_substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_formatos, ',', v_pos), ',', -1);
        IF v_substr != '' THEN
            SET v_id_formato = CAST(v_substr AS UNSIGNED);
            INSERT INTO formato_pelicula(id_pelicula, id_formato) VALUES (v_id_pelicula, v_id_formato);
            SET v_pos = v_pos + 1;
        ELSE
            SET v_pos = 0;
        END IF;
    END WHILE;
    
    SELECT v_id_pelicula AS id_pelicula_insertada;
END$$


DROP PROCEDURE IF EXISTS pelicula_completa_update$$
CREATE PROCEDURE pelicula_completa_update(
    IN p_id_pelicula INT,
    IN p_duracion INT,
    IN p_url_imagen VARCHAR(255),
    IN p_nombre VARCHAR(80),
    IN p_sinopsis TEXT,
    IN p_idiomas TEXT,  -- Ejemplo: '1,2,3'
    IN p_formatos TEXT  -- Ejemplo: '1,2,4'
)
BEGIN
    -- 1. Actualizar la película principal
    UPDATE pelicula 
    SET duracion = p_duracion, 
        url_imagen = p_url_imagen, 
        nombre = p_nombre, 
        sinopsis = p_sinopsis
    WHERE id_pelicula = p_id_pelicula;
    
    -- 2. Eliminar idiomas antiguos y insertar nuevos usando TU procedimiento
    DELETE FROM idiomas_pelicula WHERE id_pelicula = p_id_pelicula;
    
    -- Insertar nuevos idiomas usando tu procedimiento idioma_pelicula_insert
    SET @pos = 1;
    WHILE @pos > 0 DO
        SET @substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_idiomas, ',', @pos), ',', -1);
        IF @substr != '' THEN
            -- Usando TU procedimiento idioma_pelicula_insert
            CALL idioma_pelicula_insert(@substr, p_id_pelicula);
            SET @pos = @pos + 1;
        ELSE
            SET @pos = 0;
        END IF;
    END WHILE;
    
    -- 3. Eliminar formatos antiguos y insertar nuevos usando TU procedimiento
    DELETE FROM formato_pelicula WHERE id_pelicula = p_id_pelicula;
    
    -- Insertar nuevos formatos usando tu procedimiento formato_pelicula_insert
    SET @pos = 1;
    WHILE @pos > 0 DO
        SET @substr = SUBSTRING_INDEX(SUBSTRING_INDEX(p_formatos, ',', @pos), ',', -1);
        IF @substr != '' THEN
            -- Usando TU procedimiento formato_pelicula_insert
            CALL formato_pelicula_insert(@substr, p_id_pelicula);
            SET @pos = @pos + 1;
        ELSE
            SET @pos = 0;
        END IF;
    END WHILE;
    
    SELECT 'Película actualizada correctamente' AS mensaje;
END$$


-- Procedimiento para ELIMINAR película completa
DROP PROCEDURE IF EXISTS pelicula_completa_delete$$
CREATE PROCEDURE pelicula_completa_delete(IN p_id_pelicula INT)
BEGIN
    DELETE FROM pelicula WHERE id_pelicula = p_id_pelicula;
    
    SELECT 'Película eliminada correctamente' AS mensaje;
END$$

DELIMITER ;