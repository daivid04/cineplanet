USE cineplanet;

-- =====================================================================================
-- PASO 1: PREPARAR DESCRIPCIONES DE COMPRA (deben existir primero)
-- =====================================================================================

-- Descripción para el combo
INSERT INTO compra_cliente (tipo, id_combo) VALUES ('Combo', 2);
SET @id_desc_combo = LAST_INSERT_ID();

-- Descripción para el producto individual
INSERT INTO compra_cliente (tipo, id_combo) VALUES ('Individual', NULL);
SET @id_desc_producto = LAST_INSERT_ID();

-- =====================================================================================
-- PASO 2: PREPARAR DATOS DE LA COMPRA
-- =====================================================================================

SET @id_usuario = 1;              -- Socio: Miguel Grau
SET @fecha_compra = CURDATE();

-- JSON con los boletos a comprar
SET @boletos_json = JSON_ARRAY(
    JSON_OBJECT(
        'id_funcion', 1,
        'id_descripcion', 1,
        'id_sala', 1,
        'id_sede', 1,
        'precio', 18.00
    ),
    JSON_OBJECT(
        'id_funcion', 1,
        'id_descripcion', 2,
        'id_sala', 1,
        'id_sede', 1,
        'precio', 18.00
    )
);

-- JSON con los productos a comprar (usando las descripciones creadas)
SET @productos_json = JSON_ARRAY(
    JSON_OBJECT(
        'id_producto', NULL,
        'id_combo', 2,
        'id_descripcion_de_compra', @id_desc_combo,
        'precio', 55.00
    ),
    JSON_OBJECT(
        'id_producto', 10,
        'id_combo', NULL,
        'id_descripcion_de_compra', @id_desc_producto,
        'precio', 14.00
    )
);

-- =====================================================================================
-- PASO 3: EJECUTAR LA COMPRA COMPLETA
-- =====================================================================================

CALL compra_completa_insert(
    @fecha_compra,
    @id_usuario,
    @boletos_json,
    @productos_json
);

-- =====================================================================================
-- PASO 4: VERIFICAR RESULTADO
-- =====================================================================================

SELECT '==================== RESULTADO ====================' AS '';