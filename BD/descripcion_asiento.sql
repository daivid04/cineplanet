USE cineplanet;

DELIMITER $$

-- Procedimiento para INSERTAR una nueva descripción de asiento
DROP PROCEDURE IF EXISTS descripcion_asiento_insert$$
CREATE PROCEDURE descripcion_asiento_insert(
    IN p_id_asiento INT
)
BEGIN
    INSERT INTO descripcion_asiento(id_asiento) 
    VALUES (p_id_asiento);
    
    SELECT LAST_INSERT_ID() AS id_descripcion_insertada;
END$$

-- Procedimiento para ACTUALIZAR una descripción de asiento existente
DROP PROCEDURE IF EXISTS descripcion_asiento_update$$
CREATE PROCEDURE descripcion_asiento_update(
    IN p_id_descripcion INT,
    IN p_id_asiento INT
)
BEGIN
    UPDATE descripcion_asiento 
    SET id_asiento = p_id_asiento
    WHERE id_descripcion = p_id_descripcion;
    
    SELECT 'Descripción de asiento actualizada correctamente' AS mensaje;
END$$

-- Procedimiento para ELIMINAR una descripción de asiento
DROP PROCEDURE IF EXISTS descripcion_asiento_delete$$
CREATE PROCEDURE descripcion_asiento_delete(
    IN p_id_descripcion INT
)
BEGIN
    DELETE FROM descripcion_asiento WHERE id_descripcion = p_id_descripcion;
    
    SELECT 'Descripción de asiento eliminada correctamente' AS mensaje;
END$$

DELIMITER ;