USE cineplanet;

DELIMITER $$

-- ========== PROCEDIMIENTOS PARA USUARIO ==========
DROP PROCEDURE IF EXISTS usuario_insert$$
CREATE PROCEDURE usuario_insert(IN p_correo VARCHAR(50))
BEGIN
    INSERT INTO usuario(correo) VALUES (p_correo);
END$$

DROP PROCEDURE IF EXISTS usuario_update$$
CREATE PROCEDURE usuario_update(IN p_id_usuario INT, IN p_correo VARCHAR(50))
BEGIN
    UPDATE usuario SET correo = p_correo WHERE id_usuario = p_id_usuario;
END$$

DROP PROCEDURE IF EXISTS usuario_delete$$
CREATE PROCEDURE usuario_delete(IN p_id_usuario INT)
BEGIN
    DELETE FROM usuario WHERE id_usuario = p_id_usuario;
END$$

-- ========== PROCEDIMIENTOS PARA SOCIO ==========
DROP PROCEDURE IF EXISTS socio_insert$$
CREATE PROCEDURE socio_insert(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_genero VARCHAR(20),
    IN p_fecha_nacimiento DATETIME,
    IN p_documento VARCHAR(8),
    IN p_id_tipo_socio INT
)
BEGIN
    INSERT INTO socio(id_usuario, nombre, apellido, genero, fecha_nacimiento, documento, id_tipo_socio) 
    VALUES (p_id_usuario, p_nombre, p_apellido, p_genero, p_fecha_nacimiento, p_documento, p_id_tipo_socio);
END$$

DROP PROCEDURE IF EXISTS socio_update$$
CREATE PROCEDURE socio_update(
    IN p_id_usuario INT,
    IN p_nombre VARCHAR(50),
    IN p_apellido VARCHAR(50),
    IN p_genero VARCHAR(20),
    IN p_fecha_nacimiento DATETIME,
    IN p_documento VARCHAR(8),
    IN p_id_tipo_socio INT
)
BEGIN
    UPDATE socio 
    SET nombre = p_nombre,
        apellido = p_apellido,
        genero = p_genero,
        fecha_nacimiento = p_fecha_nacimiento,
        documento = p_documento,
        id_tipo_socio = p_id_tipo_socio
    WHERE id_usuario = p_id_usuario;
END$$

DROP PROCEDURE IF EXISTS socio_delete$$
CREATE PROCEDURE socio_delete(IN p_id_usuario INT)
BEGIN
    DELETE FROM socio WHERE id_usuario = p_id_usuario;
END$$

-- ========== PROCEDIMIENTOS PARA INVITADO ==========
DROP PROCEDURE IF EXISTS invitado_insert$$
CREATE PROCEDURE invitado_insert(IN p_id_usuario INT, IN p_nombre VARCHAR(50))
BEGIN
    INSERT INTO invitado(id_usuario, nombre) VALUES (p_id_usuario, p_nombre);
END$$

DROP PROCEDURE IF EXISTS invitado_update$$
CREATE PROCEDURE invitado_update(IN p_id_usuario INT, IN p_nombre VARCHAR(50))
BEGIN
    UPDATE invitado SET nombre = p_nombre WHERE id_usuario = p_id_usuario;
END$$

DROP PROCEDURE IF EXISTS invitado_delete$$
CREATE PROCEDURE invitado_delete(IN p_id_usuario INT)
BEGIN
    DELETE FROM invitado WHERE id_usuario = p_id_usuario;
END$$

DELIMITER ;