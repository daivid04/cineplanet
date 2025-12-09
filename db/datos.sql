-- Usar la base de datos
USE u914095763_g1;

-- Primero poblar la tabla metodo
INSERT INTO `metodo` (`id_metodo`, `nombre_metodo`, `descripcion`, `icono`, `estado`) VALUES
(1, 'Tarjeta de Credito', 'Pago con tarjeta de credito Visa/Mastercard', 'credit-card', 1),
(2, 'Tarjeta de Debito', 'Pago con tarjeta de debito', 'debit-card', 1),
(3, 'Efectivo', 'Pago en efectivo en caja', 'cash', 1),
(4, 'Yape', 'Pago movil con Yape BCP', 'yape', 1),
(5, 'Plin', 'Pago movil con Plin', 'plin', 1),
(6, 'Transferencia Bancaria', 'Transferencia desde cuenta bancaria', 'bank-transfer', 1);

--
-- Poblando la tabla `ciudad`
--
INSERT INTO `ciudad` (`id_ciudad`, `nombre`, `estado`) VALUES
(1, 'Lima', 1),
(2, 'Arequipa', 1),
(3, 'Trujillo', 1),
(4, 'Chiclayo', 1),
(5, 'Piura', 1),
(6, 'Cusco', 1),
(7, 'Huancayo', 1),
(8, 'Tacna', 1),
(9, 'Ica', 1),
(10, 'Chimbote', 1);

--
-- Poblando la tabla `sede`

INSERT INTO `sede` (`id_sede`, `nombre`, `id_ciudad`, `estado`) VALUES
(1, 'Cineplanet Real Plaza Tacna', 8, 1),
(2, 'Cineplanet Mall Aventura Chiclayo', 4, 1),
(3, 'Cineplanet Jockey Plaza', 1, 1),
(4, 'Cineplanet Mall del Sur', 1, 1),
(5, 'Cineplanet Real Plaza Arequipa', 2, 1),
(6, 'Cineplanet Mall Plaza Trujillo', 3, 1),
(7, 'Cineplanet Real Plaza Cusco', 6, 1),
(8, 'Cineplanet Real Plaza Piura', 5, 1),
(9, 'Cineplanet Real Plaza Huancayo', 7, 1),
(10, 'Cineplanet El Quinde Ica', 9, 1);

--
-- Poblando la tabla `sala`
--
INSERT INTO `sala` (`id_sala`, `num_sala`, `id_sede`, `estado`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 1),
(3, 1, 2, 1),
(4, 2, 2, 1),
(5, 1, 3, 1),
(6, 2, 3, 1),
(7, 3, 3, 1),
(8, 1, 4, 1),
(9, 1, 5, 1),
(10, 1, 6, 1),
(11, 4, 3, 1); -- Sala adicional para mas funciones

--
-- Poblando la tabla `asiento` (20 asientos para 2 salas)
--
INSERT INTO `asiento` (`id_asiento`, `estado`, `fila_asiento`, `columna_asiento`, `id_sala`) VALUES
(1, 1, 'A', '1', 1),
(2, 1, 'A', '2', 1),
(3, 1, 'A', '3', 1),
(4, 1, 'A', '4', 1),
(5, 1, 'A', '5', 1),
(6, 1, 'B', '1', 1),
(7, 1, 'B', '2', 1),
(8, 1, 'B', '3', 1),
(9, 1, 'B', '4', 1),
(10, 1, 'B', '5', 1),
(11, 1, 'C', '1', 2),
(12, 1, 'C', '2', 2),
(13, 1, 'C', '3', 2),
(14, 1, 'C', '4', 2),
(15, 1, 'C', '5', 2),
(16, 1, 'D', '1', 2),
(17, 1, 'D', '2', 2),
(18, 1, 'D', '3', 2),
(19, 1, 'D', '4', 2),
(20, 1, 'D', '5', 2);

--
-- Poblando la tabla `pelicula`
--
INSERT INTO `pelicula` (`id_pelicula`, `duracion`, `url_imagen`, `nombre`, `sinopsis`, `estado`) VALUES
(1, 155, 'https://es.web.img3.acsta.net/pictures/24/02/20/17/42/2385575.jpg', 'Dune: Parte Dos', 'Paul Atreides se une a los Fremen y comienza un viaje espiritual y marcial para convertirse en Muad''dib.', 1),
(2, 180, 'https://m.media-amazon.com/images/M/MV5BNTFlZDI1YWQtMTVjNy00YWU1LTg2YjktMTlhYmRiYzQ3NTVhXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg', 'Oppenheimer', 'La historia del fisico J. Robert Oppenheimer y su papel en el desarrollo de la bomba atomica.', 1),
(3, 114, 'https://images.justwatch.com/poster/306421131/s718/barbie-2023.jpg', 'Barbie', 'Barbie sufre una crisis que la lleva a cuestionar su mundo y su existencia.', 1),
(4, 140, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQttE0dKwLuMVnfkcEplhr6Hf-0lnBDsx6NSg&s', 'Spider-Man: A traves del Spider-Verso', 'Miles Morales es catapultado a traves del Multiverso, donde se encuentra con un equipo de Spider-People.', 1),
(5, 150, 'https://lumiere-a.akamaihd.net/v1/images/lat_2ae5e247.jpeg', 'Guardianes de la Galaxia Vol. 3', 'Los Guardianes se embarcan en una peligrosa mision para proteger a uno de los suyos.', 1),
(6, 141, 'https://images.justwatch.com/poster/304471076/s718/john-wick-4.jpg', 'John Wick: Capitulo 4', 'John Wick descubre un camino para derrotar a la Alta Mesa. Pero antes de poder ganar su libertad, Wick debe enfrentarse a un nuevo enemigo.', 1),
(7, 124, 'https://m.media-amazon.com/images/M/MV5BZDkyMDBiN2EtYTM2Ni00ZjRjLWFmZWQtZDdkZDBhNTQzYTU4XkEyXkFqcGc@._V1_.jpg', 'Super Mario Bros. La Pelicula', 'Un fontanero llamado Mario viaja por un laberinto subterraneo con su hermano, Luigi, tratando de salvar a una princesa capturada.', 1),
(8, 169, 'https://lumiere-a.akamaihd.net/v1/images/b162385cffbbe656f1e654b80098ac57_3276x4096_380354b0.jpeg?region=0,0,3276,4096', 'Avatar: El Camino del Agua', 'Jake Sully y Ney''tiri han formado una familia y hacen todo lo posible por permanecer juntos. Sin embargo, deben abandonar su hogar y explorar las regiones de Pandora.', 1),
(9, 102, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRK_1c9FZFFRQoSq61ZjCqg15wFmNgX99rSFA&s', 'Elementos', 'En una ciudad donde conviven residentes de fuego, agua, tierra y aire, una joven de fuego y un chico de agua descubren que tienen mucho en comun.', 1),
(10, 146, 'https://pics.filmaffinity.com/Flash-570628784-large.jpg', 'Flash', 'Barry Allen usa su supervelocidad para cambiar el pasado, pero su intento de salvar a su familia crea un mundo sin superheroes.', 0);

--
-- Poblando la tabla `idioma`
--
INSERT INTO `idioma` (`id_idioma`, `idioma`, `estado`) VALUES
(1, 'Espanol (Doblada)', 1),
(2, 'Ingles (Subtitulada)', 1),
(3, 'Espanol (Subtitulada)', 1),
(4, 'Japones (Subtitulada)', 1),
(5, 'Portugues (Subtitulada)', 1),
(6, 'Frances (Subtitulada)', 1),
(7, 'Coreano (Subtitulada)', 1),
(8, 'Italiano (Subtitulada)', 1),
(9, 'Aleman (Subtitulada)', 1),
(10, 'Chino (Subtitulada)', 1);

--
-- Poblando la tabla `formato`
--
INSERT INTO `formato` (`id_formato`, `nombre`, `estado`) VALUES
(1, '2D', 1),
(2, '3D', 1),
(3, 'IMAX', 1),
(4, '4DX', 1),
(5, 'XD', 1),
(6, 'D-BOX', 1),
(7, 'VIP', 1),
(8, 'Regular', 1),
(9, 'Premium', 1),
(10, 'Gold', 1);

--
-- Poblando la tabla `funcion`
--
INSERT INTO `funcion` (`id_funcion`, `fecha`, `hora`, `id_pelicula`, `id_sala`, `estado`) VALUES
(1, '2025-12-15', '18:00:00', 1, 1, 1),
(2, '2025-12-15', '21:00:00', 1, 1, 1),
(3, '2025-12-15', '19:00:00', 2, 2, 1),
(4, '2025-12-16', '20:00:00', 3, 3, 1),
(5, '2025-12-16', '22:00:00', 4, 4, 1),
(6, '2025-12-17', '17:00:00', 5, 5, 1),
(7, '2025-12-17', '19:30:00', 6, 6, 1),
(8, '2025-12-18', '16:00:00', 7, 7, 1),
(9, '2025-12-18', '18:30:00', 8, 8, 1),
(10, '2025-12-19', '20:30:00', 9, 9, 1),
(11, '2025-12-20', '14:00:00', 1, 3, 1),
(12, '2025-12-20', '16:30:00', 2, 4, 1),
(13, '2025-12-21', '19:00:00', 3, 5, 1),
(14, '2025-12-21', '21:30:00', 4, 6, 1),
(15, '2025-12-22', '15:00:00', 5, 7, 1),
(16, '2025-12-22', '18:00:00', 6, 8, 1),
(17, '2025-12-23', '20:00:00', 7, 9, 1),
(18, '2025-12-23', '22:30:00', 8, 10, 1),
(19, '2025-12-24', '17:00:00', 9, 1, 1),
(20, '2025-12-24', '20:00:00', 1, 2, 1),
(21, '2025-12-25', '16:00:00', 2, 3, 1),
(22, '2025-12-25', '19:00:00', 3, 4, 1),
(23, '2025-12-26', '18:30:00', 4, 5, 1),
(24, '2025-12-26', '21:00:00', 5, 6, 1),
(25, '2025-12-27', '15:30:00', 6, 7, 1),
(26, '2025-12-27', '18:00:00', 7, 8, 1),
(27, '2025-12-28', '20:30:00', 8, 9, 1),
(28, '2025-12-28', '22:00:00', 9, 10, 1),
(29, '2025-12-29', '14:00:00', 1, 1, 1),
(30, '2025-12-29', '17:00:00', 2, 2, 1),
(31, '2025-12-30', '19:30:00', 3, 3, 1),
(32, '2025-12-30', '21:30:00', 4, 4, 1),
(33, '2025-12-31', '16:00:00', 5, 5, 1),
(34, '2025-12-31', '20:00:00', 6, 6, 1),
(35, '2026-01-01', '15:00:00', 7, 7, 1),
(36, '2026-01-01', '18:00:00', 8, 8, 1),
(37, '2026-01-02', '17:00:00', 9, 9, 1),
(38, '2026-01-02', '20:30:00', 1, 10, 1),
(39, '2026-01-03', '16:30:00', 2, 1, 1),
(40, '2026-01-03', '19:00:00', 3, 2, 1);

--
-- Poblando la tabla `trabajador`
--
INSERT INTO `trabajador` (`id_trabajador`, `tipo`, `estado`, `dni`, `numero`, `correo`, `nombre`, `apellido`, `id_sede`, `fecha_ingreso`) VALUES
(1, 'Gerente', 1, '71234567', '987654321', 'juan.perez@cineplanet.com', 'Juan', 'Perez', 1, '2020-01-15'),
(2, 'Boleteria', 1, '72345678', '987654322', 'maria.gomez@cineplanet.com', 'Maria', 'Gomez', 1, '2021-03-20'),
(3, 'Dulceria', 1, '73456789', '987654323', 'carlos.rodriguez@cineplanet.com', 'Carlos', 'Rodriguez', 2, '2022-05-10'),
(4, 'Limpieza', 1, '74567890', '987654324', 'ana.lopez@cineplanet.com', 'Ana', 'Lopez', 3, '2019-11-05'),
(5, 'Proyeccionista', 1, '75678901', '987654325', 'luis.martinez@cineplanet.com', 'Luis', 'Martinez', 4, '2023-02-28'),
(6, 'Boleteria', 1, '76789012', '987654326', 'elena.fernandez@cineplanet.com', 'Elena', 'Fernandez', 5, '2021-07-15'),
(7, 'Gerente', 1, '77890123', '987654327', 'pedro.sanchez@cineplanet.com', 'Pedro', 'Sanchez', 6, '2018-09-01'),
(8, 'Dulceria', 0, '78901234', '987654328', 'sofia.diaz@cineplanet.com', 'Sofia', 'Diaz', 7, '2022-12-10'),
(9, 'Boleteria', 1, '79012345', '987654329', 'javier.morales@cineplanet.com', 'Javier', 'Morales', 8, '2023-06-20'),
(10, 'Dulceria', 1, '70123456', '987654320', 'laura.castillo@cineplanet.com', 'Laura', 'Castillo', 9, '2021-10-30');

--
-- Poblando la tabla `producto`
--
INSERT INTO `producto` (`id_producto`, `nombre`, `precio_unitario`, `estado`) VALUES
(1, 'Canchita Gigante Salada', 25.50, 1),
(2, 'Canchita Grande Salada', 20.00, 1),
(3, 'Canchita Mediana Salada', 15.00, 1),
(4, 'Gaseosa Grande', 12.00, 1),
(5, 'Gaseosa Mediana', 10.00, 1),
(6, 'Hot-Dog Clasico', 8.50, 1),
(7, 'Nachos con Queso', 18.00, 1),
(8, 'Agua Mineral', 6.00, 1),
(9, 'Chocolate Sublime', 5.00, 1),
(10, 'Tequenos', 14.00, 0);

--
-- Poblando la tabla `producto_sede`
--
INSERT INTO `producto_sede` (`id_producto_sede`, `stock`, `id_producto`, `id_sede`) VALUES
(1, 100, 1, 1),
(2, 150, 2, 1),
(3, 200, 4, 1),
(4, 80, 7, 1),
(5, 100, 1, 2),
(6, 150, 2, 2),
(7, 200, 4, 2),
(8, 90, 6, 3),
(9, 120, 8, 4),
(10, 300, 9, 5);

--
-- Poblando la tabla `combos`
--
INSERT INTO `combos` (`id_combo`, `precio`, `nombre`, `estado`) VALUES
(1, 35.00, 'Combo Clasico', 1),
(2, 55.00, 'Combo Pareja', 1),
(3, 28.00, 'Combo Nachos', 1),
(4, 25.00, 'Combo Hot-Dog', 1),
(5, 75.00, 'Combo Familiar', 1),
(6, 30.00, 'Combo Mediano', 1),
(7, 22.00, 'Combo Kids', 1),
(8, 40.00, 'Combo Gigante', 1),
(9, 60.00, 'Combo Amigos', 1),
(10, 32.00, 'Combo Tequenos', 0);

--
-- Poblando la tabla `usuario`
--
INSERT INTO `usuario` (`id_usuario`, `correo`, `estado`) VALUES
(1, 'cliente1@example.com', 1),
(2, 'cliente2@example.com', 1),
(3, 'cliente3@example.com', 1),
(4, 'cliente4@example.com', 1),
(5, 'cliente5@example.com', 1),
(6, 'cliente6@example.com', 1),
(7, 'cliente7@example.com', 1),
(8, 'cliente8@example.com', 1),
(9, 'cliente9@example.com', 1),
(10, 'cliente10@example.com', 1),
(11, 'cliente10@example.com', 1),
(12, 'cliente10@example.com', 1),
(13, 'cliente10@example.com', 1),
(14, 'cliente10@example.com', 1),
(15, 'cliente10@example.com', 1),
(16, 'cliente10@example.com', 1),
(17, 'cliente10@example.com', 1),
(18, 'cliente10@example.com', 1),
(19, 'cliente10@example.com', 1),
(20, 'cliente10@example.com', 1);

--
-- Poblando la tabla `tipo_socio` (3 tipos tradicionales)
--
INSERT INTO `tipo_socio` (`id_socio`, `nombre`, `desc_dulces`, `desc_boleto`, `estado`) VALUES
(1, 'Socio Clasico', 10.00, 15.00, 1),
(2, 'Socio Oro', 15.00, 25.00, 1),
(3, 'Socio Premium', 20.00, 35.00, 1);


INSERT INTO `socio` (`id_usuario`, `nombre`, `apellido`, `genero`, `fecha_nacimiento`, `documento`, `id_tipo_socio`, `contrasena`) VALUES
(11, 'Miguel', 'Grau', 'Masculino', '1990-07-27 10:00:00', '12345678', 1, '123'),
(12, 'Francisco', 'Bolognesi', 'Masculino', '1985-11-04 15:30:00', '23456789', 2, '123'),
(13, 'Andres', 'Caceres', 'Masculino', '2000-02-10 08:45:00', '34567890', 3, '123'),
(14, 'Tupac', 'Amaru', 'Masculino', '1995-05-19 12:00:00', '45678901', 1, '123'),
(15, 'Micaela', 'Bastidas', 'Femenino', '1998-09-23 18:20:00', '56789012', 2, '123'),
(16, 'Jose', 'Olaya', 'Masculino', '1988-01-15 09:00:00', '67890123', 1, '123'),
(17, 'Jorge', 'Basadre', 'Masculino', '1992-03-12 20:00:00', '78901234', 3, '123'),
(18, 'Ricardo', 'Palma', 'Masculino', '1999-08-07 14:10:00', '89012345', 2, '123'),
(19, 'Cesar', 'Vallejo', 'Masculino', '2001-04-16 11:35:00', '90123456', 1, '123'),
(20, 'Mario', 'Vargas', 'Masculino', '1986-12-28 22:05:00', '01234567', 3, '123');
--
-- Poblando la tabla `invitado`
--
INSERT INTO `invitado` (`id_usuario`, `nombre`) VALUES
(1, 'Miguel Grau'),
(2, 'Francisco Bolognesi'),
(3, 'Andres Avelino Caceres'),
(4, 'Tupac Amaru II'),
(5, 'Micaela Bastidas'),
(6, 'Jose Olaya'),
(7, 'Jorge Basadre'),
(8, 'Ricardo Palma'),
(9, 'Cesar Vallejo'),
(10, 'Mario Vargas Llosa');



--
-- Poblando la tabla `compra`
--
INSERT INTO `compra` (`id_compra`, `fecha`, `id_usuario`, `id_metodo`) VALUES
(1, '2025-10-16', 1, 1),
(2, '2025-10-16', 2, 2),
(3, '2025-10-16', 3, 3),
(4, '2025-10-15', 4, 1),
(5, '2025-10-15', 5, 4),
(6, '2025-10-14', 6, 3),
(7, '2025-10-14', 7, 2),
(8, '2025-10-13', 8, 1),
(9, '2025-10-13', 9, 5),
(10, '2025-10-13', 10, 3);



-- Poblando la tabla `compra_boleto`
--
INSERT INTO `compra_boleto` (`id_compra_boleto`, `precio_total_boleto`, `id_compra`, `id_funcion`, `id_sala`, `id_sede`) VALUES
(1, 30.00, 1, 1, 1, 1),
(2, 30.00, 1, 1, 1, 1),
(3, 15.00, 2, 2, 1, 1),
(4, 25.00, 3, 3, 2, 1),
(5, 25.00, 3, 3, 2, 1),
(6, 20.00, 4, 4, 1, 1),
(7, 20.00, 5, 5, 1, 1),
(8, 22.00, 6, 6, 2, 1),
(9, 18.00, 7, 7, 2, 1),
(10, 40.00, 8, 8, 2, 1);

--

--
-- Poblando la tabla `descripcion_asiento`
--
INSERT INTO `descripcion_asiento` (`id_descripcion`, `id_asiento`, `id_compra_boleto`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 6, 4),
(5, 7, 5),
(6, 11, 6),
(7, 12, 7),
(8, 15, 8),
(9, 16, 9),
(10, 20, 10);


-- Poblando la tabla `compra_productos`
--
-- Poblando la tabla `compra_productos` (CORREGIDO - solo campos existentes)
INSERT INTO `compra_productos` (`id_precio_productos`, `precio_compra`, `id_compra`) VALUES
(1, 35.00, 1),
(2, 55.00, 2),
(3, 18.00, 3),
(4, 12.00, 4),
(5, 25.50, 5),
(6, 25.00, 6),
(7, 14.00, 7),
(8, 75.00, 8),
(9, 30.00, 9),
(10, 6.00, 10);


--
-- Poblando la tabla `compra_cliente`
--
INSERT INTO `compra_cliente` (`id_descripcion_de_compra`, `tipo`, `id_combo`, `id_producto_sede`, `id_compra_productos`) VALUES
(1, 'Combo', 1, 1, 1),
(2, 'Combo', 2, 2, 2),
(3, 'Combo', 3, 3, 3),
(4, 'Combo', 4, 4, 4),
(5, 'Individual', NULL, 5, 5),
(6, 'Individual', NULL, 6, 6),
(7, 'Individual', NULL, 7, 7),
(8, 'Combo', 5, 8, 8),
(9, 'Combo', 6, 9, 9),
(10, 'Individual', NULL, 10, 10);

--
-- Poblando la tabla `idiomas_pelicula`
--
INSERT INTO `idiomas_pelicula` (`id_idiomas_pelicula`, `id_pelicula`, `id_idioma`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 1),
(4, 2, 2),
(5, 3, 1),
(6, 4, 1),
(7, 4, 2),
(8, 5, 1),
(9, 6, 2),
(10, 7, 1);

--
-- Poblando la tabla `formato_pelicula`
--
INSERT INTO `formato_pelicula` (`id_formato_pelicula`, `id_pelicula`, `id_formato`) VALUES
(1, 1, 1),
(2, 1, 3),
(3, 2, 1),
(4, 2, 5),
(5, 3, 1),
(6, 4, 1),
(7, 4, 2),
(8, 5, 1),
(9, 7, 4),
(10, 8, 3);

--
-- Poblando la tabla `producto_combo`
--
INSERT INTO `producto_combo` (`id_productos_combos`, `id_combo`, `id_producto_sede`) VALUES
(1, 1, 2), -- Combo Clasico: Canchita Grande
(2, 1, 5), -- Combo Clasico: Gaseosa Mediana
(3, 2, 1), -- Combo Pareja: Canchita Gigante
(4, 2, 4), -- Combo Pareja: Gaseosa Grande
(5, 2, 4), -- Combo Pareja: Gaseosa Grande
(6, 3, 7), -- Combo Nachos: Nachos
(7, 3, 5), -- Combo Nachos: Gaseosa Mediana
(8, 4, 6), -- Combo Hot-Dog: Hot-Dog
(9, 4, 5), -- Combo Hot-Dog: Gaseosa Mediana
(10, 10, 10), -- Combo Tequenos: Tequenos
(11, 10, 5); -- Combo Tequenos: Gaseosa Mediana

