USE cineplanet;

DELIMITER $$

DROP PROCEDURE IF EXISTS asiento_insert$$
CREATE PROCEDURE asiento_insert(
    IN p_estado BOOL,
    IN p_fila_asiento VARCHAR(5),
    IN p_columna_asiento VARCHAR(5),
    IN p_id_sala INT
)
BEGIN
    INSERT INTO asiento(estado, fila_asiento, columna_asiento, id_sala) 
    VALUES (p_estado, p_fila_asiento, p_columna_asiento, p_id_sala);
    SELECT LAST_INSERT_ID() AS id_asiento_insertado;
END$$

DROP PROCEDURE IF EXISTS asiento_update$$
CREATE PROCEDURE asiento_update(
    IN p_id_asiento INT,
    IN p_estado BOOL,
    IN p_fila_asiento VARCHAR(5),
    IN p_columna_asiento VARCHAR(5),
    IN p_id_sala INT
)
BEGIN
    UPDATE asiento 
    SET estado = p_estado,
        fila_asiento = p_fila_asiento,
        columna_asiento = p_columna_asiento,
        id_sala = p_id_sala
    WHERE id_asiento = p_id_asiento;
END$$

DROP PROCEDURE IF EXISTS asiento_delete$$
CREATE PROCEDURE asiento_delete(
    IN p_id_asiento INT
)
BEGIN
    DELETE FROM asiento WHERE id_asiento = p_id_asiento;
END$$

DELIMITER ;