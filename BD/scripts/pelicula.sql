USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS pelicula_completa_insert$$
CREATE PROCEDURE pelicula_completa_insert(
    IN p_duracion INT,
    IN p_url_imagen VARCHAR(255),
    IN p_nombre VARCHAR(80),
    IN p_sinopsis TEXT,
    IN p_idiomas JSON,  -- Formato: [1, 2, 3] o ["1", "2", "3"]
    IN p_formatos JSON  -- Formato: [1, 2, 4] o ["1", "2", "4"]
)
BEGIN
    DECLARE v_id_pelicula INT;
    DECLARE v_index INT DEFAULT 0;
    DECLARE v_idiomas_count INT;
    DECLARE v_formatos_count INT;
    DECLARE v_id_idioma INT;
    DECLARE v_id_formato INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS success, 'Error al insertar película completa' AS mensaje, NULL AS id_pelicula_insertada;
    END;
    
    START TRANSACTION;
    
    INSERT INTO pelicula(duracion, url_imagen, nombre, sinopsis) 
    VALUES (p_duracion, p_url_imagen, p_nombre, p_sinopsis);
    
    SET v_id_pelicula = LAST_INSERT_ID();
    
    IF p_idiomas IS NOT NULL AND JSON_TYPE(p_idiomas) = 'ARRAY' THEN
        SET v_idiomas_count = JSON_LENGTH(p_idiomas);
        SET v_index = 0;
        
        WHILE v_index < v_idiomas_count DO
            SET v_id_idioma = JSON_EXTRACT(p_idiomas, CONCAT('$[', v_index, ']'));
            
            INSERT INTO idiomas_pelicula(id_pelicula, id_idioma) 
            VALUES (v_id_pelicula, v_id_idioma);
            
            SET v_index = v_index + 1;
        END WHILE;
    END IF;
    
    IF p_formatos IS NOT NULL AND JSON_TYPE(p_formatos) = 'ARRAY' THEN
        SET v_formatos_count = JSON_LENGTH(p_formatos);
        SET v_index = 0;
        
        WHILE v_index < v_formatos_count DO
            SET v_id_formato = JSON_EXTRACT(p_formatos, CONCAT('$[', v_index, ']'));
            
            INSERT INTO formato_pelicula(id_pelicula, id_formato) 
            VALUES (v_id_pelicula, v_id_formato);
            
            SET v_index = v_index + 1;
        END WHILE;
    END IF;
    
    COMMIT;
    
    SELECT 1 AS success, 'Película insertada correctamente' AS mensaje, v_id_pelicula AS id_pelicula_insertada;
END$$



DROP PROCEDURE IF EXISTS pelicula_completa_update$$
CREATE PROCEDURE pelicula_completa_update(
    IN p_id_pelicula INT,
    IN p_duracion INT,
    IN p_url_imagen VARCHAR(255),
    IN p_nombre VARCHAR(80),
    IN p_sinopsis TEXT,
    IN p_idiomas JSON,  -- Formato: [1, 2, 3]
    IN p_formatos JSON  -- Formato: [1, 2, 4]
)
BEGIN
    DECLARE v_index INT DEFAULT 0;
    DECLARE v_idiomas_count INT;
    DECLARE v_formatos_count INT;
    DECLARE v_id_idioma INT;
    DECLARE v_id_formato INT;
    DECLARE v_affected_rows INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS success, 'Error al actualizar película completa' AS mensaje;
    END;
    
    START TRANSACTION;
    
    -- 1. Actualizar la película principal
    UPDATE pelicula 
    SET duracion = p_duracion, 
        url_imagen = p_url_imagen, 
        nombre = p_nombre, 
        sinopsis = p_sinopsis
    WHERE id_pelicula = p_id_pelicula;
    
    SET v_affected_rows = ROW_COUNT();
    
    -- Validar que la película existe
    IF v_affected_rows = 0 THEN
        ROLLBACK;
        SELECT 0 AS success, 'No se encontró la película especificada' AS mensaje;
    ELSE
        -- 2. Eliminar idiomas antiguos e insertar nuevos
        DELETE FROM idiomas_pelicula WHERE id_pelicula = p_id_pelicula;
        
        IF p_idiomas IS NOT NULL AND JSON_TYPE(p_idiomas) = 'ARRAY' THEN
            SET v_idiomas_count = JSON_LENGTH(p_idiomas);
            SET v_index = 0;
            
            WHILE v_index < v_idiomas_count DO
                SET v_id_idioma = JSON_EXTRACT(p_idiomas, CONCAT('$[', v_index, ']'));
                
                INSERT INTO idiomas_pelicula(id_pelicula, id_idioma) 
                VALUES (p_id_pelicula, v_id_idioma);
                
                SET v_index = v_index + 1;
            END WHILE;
        END IF;
        
        -- 3. Eliminar formatos antiguos e insertar nuevos
        DELETE FROM formato_pelicula WHERE id_pelicula = p_id_pelicula;
        
        IF p_formatos IS NOT NULL AND JSON_TYPE(p_formatos) = 'ARRAY' THEN
            SET v_formatos_count = JSON_LENGTH(p_formatos);
            SET v_index = 0;
            
            WHILE v_index < v_formatos_count DO
                SET v_id_formato = JSON_EXTRACT(p_formatos, CONCAT('$[', v_index, ']'));
                
                INSERT INTO formato_pelicula(id_pelicula, id_formato) 
                VALUES (p_id_pelicula, v_id_formato);
                
                SET v_index = v_index + 1;
            END WHILE;
        END IF;
        
        COMMIT;
        
        SELECT 1 AS success, 'Película actualizada correctamente' AS mensaje;
    END IF;
END$$

-- ========== PROCEDIMIENTO PARA ELIMINAR PELÍCULA COMPLETA ==========
DROP PROCEDURE IF EXISTS pelicula_completa_delete$$
CREATE PROCEDURE pelicula_completa_delete(IN p_id_pelicula INT)
BEGIN
    DECLARE v_affected_rows INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SELECT 0 AS success, 'Error al eliminar película' AS mensaje;
    END;
    
    START TRANSACTION;
    DELETE FROM idiomas_pelicula WHERE id_pelicula = p_id_pelicula;
    DELETE FROM formato_pelicula WHERE id_pelicula = p_id_pelicula;
    
    -- Eliminar la película principal
    DELETE FROM pelicula WHERE id_pelicula = p_id_pelicula;
    
    SET v_affected_rows = ROW_COUNT();
    
    IF v_affected_rows > 0 THEN
        COMMIT;
        SELECT 1 AS success, 'Película eliminada correctamente' AS mensaje;
    ELSE
        ROLLBACK;
        SELECT 0 AS success, 'No se encontró la película especificada' AS mensaje;
    END IF;
END$$

DELIMITER ;