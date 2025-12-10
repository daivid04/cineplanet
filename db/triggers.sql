USE u914095763_g1;

DELIMITER //

CREATE TRIGGER `tr_socio_before_insert`
BEFORE INSERT ON `socio`
FOR EACH ROW
BEGIN
    IF NEW.fecha_nacimiento > NOW() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Error: La fecha de nacimiento no puede ser futura';
    END IF;

    SET NEW.nombre = UPPER(NEW.nombre);
    SET NEW.apellido = UPPER(NEW.apellido);
END //

CREATE TRIGGER `tr_actualizar_stock_after_insert`
AFTER INSERT ON `compra_cliente`
FOR EACH ROW
BEGIN
    IF NEW.id_producto_sede IS NOT NULL THEN
        UPDATE `producto_sede`
        SET `stock` = `stock` - 1
        WHERE `id_producto_sede` = NEW.id_producto_sede;
    END IF;
END //

DELIMITER ;
