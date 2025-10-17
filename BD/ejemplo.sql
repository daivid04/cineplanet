USE cineplanet;

SET @id_usuario = 2;
SET @id_funcion = 3;
SET @id_sede = 1;
SET @id_sala = 2;

SET @id_asiento_1 = 13;
SET @id_asiento_2 = 14;

SET @id_combo = 1;
SET @id_producto_individual = 7;

SET @precio_boleto = 22.00;
SET @precio_combo = 35.00;
SET @precio_nachos = 18.00;

CALL descripcion_asiento_insert(@id_asiento_1);
SET @id_desc_asiento_1 = (SELECT LAST_INSERT_ID());

CALL descripcion_asiento_insert(@id_asiento_2);
SET @id_desc_asiento_2 = (SELECT LAST_INSERT_ID());

CALL compra_cliente_insert('Combo', @id_combo);
SET @id_desc_combo = (SELECT LAST_INSERT_ID());

CALL compra_cliente_insert('Individual', NULL);
SET @id_desc_individual = (SELECT LAST_INSERT_ID());

CALL compra_insert(CURDATE(), @id_usuario);
SET @id_compra = (SELECT LAST_INSERT_ID());

CALL compra_boleto_insert(@precio_boleto, @id_compra, @id_funcion, @id_desc_asiento_1, @id_sala, @id_sede);
CALL compra_boleto_insert(@precio_boleto, @id_compra, @id_funcion, @id_desc_asiento_2, @id_sala, @id_sede);

CALL compra_productos_insert(@precio_combo, @id_compra, NULL, @id_combo, @id_desc_combo);
CALL compra_productos_insert(@precio_nachos, @id_compra, @id_producto_individual, NULL, @id_desc_individual);

UPDATE producto_sede ps
JOIN producto_combo pc ON ps.id_producto = pc.id_producto
SET ps.stock = ps.stock - 1
WHERE pc.id_combo = @id_combo AND ps.id_sede = @id_sede;

UPDATE producto_sede
SET stock = stock - 1
WHERE id_producto = @id_producto_individual AND id_sede = @id_sede;

SELECT 'El stock de los productos ha sido actualizado correctamente.' AS 'Mensaje de Stock';
SELECT '======================================================================' AS '';
SELECT '¡Gracias por su compra! ¡Disfrute de la función!' AS 'Mensaje Final';