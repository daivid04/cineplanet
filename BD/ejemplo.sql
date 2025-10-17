SET @id_usuario = 2;
SET @fecha_compra = CURDATE();

SET @boletos_json = JSON_ARRAY(
    JSON_OBJECT('id_funcion', 3, 'id_asiento', 13, 'id_sala', 2, 'id_sede', 1, 'precio', 22.00),
    JSON_OBJECT('id_funcion', 3, 'id_asiento', 14, 'id_sala', 2, 'id_sede', 1, 'precio', 22.00)
);


SET @productos_json = JSON_ARRAY(
    JSON_OBJECT('id_producto', 7, 'id_combo', 1, 'precio', 18.00)
);


CALL compra_completa_insert(@fecha_compra, @id_usuario, @boletos_json, @productos_json);

