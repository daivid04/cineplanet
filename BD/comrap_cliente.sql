USE cineplanet;

DELIMITER $$

-- Procedimiento para INSERTAR descripción de compra (combo)
DROP PROCEDURE IF EXISTS compra_cliente_insert$$
CREATE PROCEDURE compra_cliente_insert(
    IN p_tipo VARCHAR(50),
    IN p_id_combo INT
)
BEGIN
    INSERT INTO compra_cliente(tipo, id_combo) 
    VALUES (p_tipo, p_id_combo);
    
    SELECT LAST_INSERT_ID() AS id_descripcion_insertada;
END$$

-- Procedimiento para ACTUALIZAR descripción de compra (combo)
DROP PROCEDURE IF EXISTS compra_cliente_update$$
CREATE PROCEDURE compra_cliente_update(
    IN p_id_descripcion_de_compra INT,
    IN p_tipo VARCHAR(50),
    IN p_id_combo INT
)
BEGIN
    UPDATE compra_cliente 
    SET tipo = p_tipo, 
        id_combo = p_id_combo
    WHERE id_descripcion_de_compra = p_id_descripcion_de_compra;
    
    SELECT 'Descripción de compra actualizada correctamente' AS mensaje;
END$$

-- Procedimiento para ELIMINAR descripción de compra (combo)
DROP PROCEDURE IF EXISTS compra_cliente_delete$$
CREATE PROCEDURE compra_cliente_delete(IN p_id_descripcion_de_compra INT)
BEGIN
    DELETE FROM compra_cliente WHERE id_descripcion_de_compra = p_id_descripcion_de_compra;
    
    SELECT 'Descripción de compra eliminada correctamente' AS mensaje;
END$$

DELIMITER ;