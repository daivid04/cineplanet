USE cineplanet;

DELIMITER $$


DROP PROCEDURE IF EXISTS sala_insert$$
CREATE PROCEDURE sala_insert(
    IN p_num_sala INT,
    IN p_id_sede INT
)
BEGIN
    INSERT INTO sala(num_sala, id_sede) 
    VALUES (p_num_sala, p_id_sede);
END$$



DROP PROCEDURE IF EXISTS sala_update$$
CREATE PROCEDURE sala_update(
    IN p_id_sala INT,
    IN p_num_sala INT,
    IN p_id_sede INT
)
BEGIN
    UPDATE sala 
    SET num_sala = p_num_sala,
        id_sede = p_id_sede
    WHERE id_sala = p_id_sala;
END$$


DROP PROCEDURE IF EXISTS sala_delete$$
CREATE PROCEDURE sala_delete(
    IN p_id_sala INT
)
BEGIN
    DELETE FROM sala WHERE id_sala = p_id_sala;
END$$

DELIMITER ;