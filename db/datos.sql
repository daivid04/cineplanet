-- Usar la base de datos
USE cineplanet;

--
-- Poblando la tabla `ciudad`
--
INSERT INTO `ciudad` (`id_ciudad`, `nombre`) VALUES
(1, 'Lima'),
(2, 'Arequipa'),
(3, 'Trujillo'),
(4, 'Chiclayo'),
(5, 'Piura'),
(6, 'Cusco'),
(7, 'Huancayo'),
(8, 'Tacna'),
(9, 'Ica'),
(10, 'Chimbote');

--
-- Poblando la tabla `sede`
--
INSERT INTO `sede` (`id_sede`, `nombre`, `id_ciudad`) VALUES
(1, 'Cineplanet Real Plaza Tacna', 8),
(2, 'Cineplanet Mall Aventura Chiclayo', 4),
(3, 'Cineplanet Jockey Plaza', 1),
(4, 'Cineplanet Mall del Sur', 1),
(5, 'Cineplanet Real Plaza Arequipa', 2),
(6, 'Cineplanet Mall Plaza Trujillo', 3),
(7, 'Cineplanet Real Plaza Cusco', 6),
(8, 'Cineplanet Real Plaza Piura', 5),
(9, 'Cineplanet Real Plaza Huancayo', 7),
(10, 'Cineplanet El Quinde Ica', 9);

--
-- Poblando la tabla `sala`
--
INSERT INTO `sala` (`id_sala`, `num_sala`, `id_sede`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 1, 2),
(4, 2, 2),
(5, 1, 3),
(6, 2, 3),
(7, 3, 3),
(8, 1, 4),
(9, 1, 5),
(10, 1, 6),
(11, 4, 3); -- Sala adicional para más funciones

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
INSERT INTO `pelicula` (`id_pelicula`, `duracion`, `url_imagen`, `nombre`, `sinopsis`) VALUES
(1, 155, 'https://example.com/images/dune2.jpg', 'Dune: Parte Dos', 'Paul Atreides se une a los Fremen y comienza un viaje espiritual y marcial para convertirse en Muad''dib.'),
(2, 180, 'https://example.com/images/oppenheimer.jpg', 'Oppenheimer', 'La historia del físico J. Robert Oppenheimer y su papel en el desarrollo de la bomba atómica.'),
(3, 114, 'https://example.com/images/barbie.jpg', 'Barbie', 'Barbie sufre una crisis que la lleva a cuestionar su mundo y su existencia.'),
(4, 140, 'https://example.com/images/spiderverse.jpg', 'Spider-Man: A través del Spider-Verso', 'Miles Morales es catapultado a través del Multiverso, donde se encuentra con un equipo de Spider-People.'),
(5, 150, 'https://example.com/images/gotg3.jpg', 'Guardianes de la Galaxia Vol. 3', 'Los Guardianes se embarcan en una peligrosa misión para proteger a uno de los suyos.'),
(6, 141, 'https://example.com/images/johnwick4.jpg', 'John Wick: Capítulo 4', 'John Wick descubre un camino para derrotar a la Alta Mesa. Pero antes de poder ganar su libertad, Wick debe enfrentarse a un nuevo enemigo.'),
(7, 124, 'https://example.com/images/mariobros.jpg', 'Super Mario Bros. La Película', 'Un fontanero llamado Mario viaja por un laberinto subterráneo con su hermano, Luigi, tratando de salvar a una princesa capturada.'),
(8, 169, 'https://example.com/images/avatar2.jpg', 'Avatar: El Camino del Agua', 'Jake Sully y Ney''tiri han formado una familia y hacen todo lo posible por permanecer juntos. Sin embargo, deben abandonar su hogar y explorar las regiones de Pandora.'),
(9, 102, 'https://example.com/images/elemental.jpg', 'Elementos', 'En una ciudad donde conviven residentes de fuego, agua, tierra y aire, una joven de fuego y un chico de agua descubren que tienen mucho en común.'),
(10, 146, 'https://example.com/images/theflash.jpg', 'Flash', 'Barry Allen usa su supervelocidad para cambiar el pasado, pero su intento de salvar a su familia crea un mundo sin superhéroes.');

--
-- Poblando la tabla `idioma`
--
INSERT INTO `idioma` (`id_idioma`, `idioma`) VALUES
(1, 'Español (Doblada)'),
(2, 'Inglés (Subtitulada)'),
(3, 'Español (Subtitulada)'),
(4, 'Japonés (Subtitulada)'),
(5, 'Portugués (Subtitulada)'),
(6, 'Francés (Subtitulada)'),
(7, 'Coreano (Subtitulada)'),
(8, 'Italiano (Subtitulada)'),
(9, 'Alemán (Subtitulada)'),
(10, 'Chino (Subtitulada)');

--
-- Poblando la tabla `formato`
--
INSERT INTO `formato` (`id_formato`, `nombre`) VALUES
(1, '2D'),
(2, '3D'),
(3, 'IMAX'),
(4, '4DX'),
(5, 'XD'),
(6, 'D-BOX'),
(7, 'VIP'),
(8, 'Regular'),
(9, 'Premium'),
(10, 'Gold');

--
-- Poblando la tabla `funcion`
--
INSERT INTO `funcion` (`id_funcion`, `fecha`, `hora`, `id_pelicula`, `id_sala`) VALUES
(1, '2025-10-17', '18:00:00', 1, 1),
(2, '2025-10-17', '21:00:00', 1, 1),
(3, '2025-10-17', '19:00:00', 2, 2),
(4, '2025-10-18', '20:00:00', 3, 3),
(5, '2025-10-18', '22:00:00', 4, 4),
(6, '2025-10-19', '17:00:00', 5, 5),
(7, '2025-10-19', '19:30:00', 6, 6),
(8, '2025-10-20', '16:00:00', 7, 7),
(9, '2025-10-20', '18:30:00', 8, 8),
(10, '2025-10-21', '20:30:00', 9, 9);

--
-- Poblando la tabla `trabajador`
--
INSERT INTO `trabajador` (`id_trabajador`, `tipo`, `esado`, `dni`, `numero`, `correo`, `nombre`, `apellido`, `id_sede`) VALUES
(1, 'Gerente', 'Activo', '71234567', '987654321', 'juan.perez@cineplanet.com', 'Juan', 'Perez', 1),
(2, 'Boletería', 'Activo', '72345678', '987654322', 'maria.gomez@cineplanet.com', 'Maria', 'Gomez', 1),
(3, 'Dulcería', 'Activo', '73456789', '987654323', 'carlos.rodriguez@cineplanet.com', 'Carlos', 'Rodriguez', 2),
(4, 'Limpieza', 'Activo', '74567890', '987654324', 'ana.lopez@cineplanet.com', 'Ana', 'Lopez', 3),
(5, 'Proyeccionista', 'Activo', '75678901', '987654325', 'luis.martinez@cineplanet.com', 'Luis', 'Martinez', 4),
(6, 'Boletería', 'Activo', '76789012', '987654326', 'elena.fernandez@cineplanet.com', 'Elena', 'Fernandez', 5),
(7, 'Gerente', 'Activo', '77890123', '987654327', 'pedro.sanchez@cineplanet.com', 'Pedro', 'Sanchez', 6),
(8, 'Dulcería', 'Inactivo', '78901234', '987654328', 'sofia.diaz@cineplanet.com', 'Sofia', 'Diaz', 7),
(9, 'Boletería', 'Activo', '79012345', '987654329', 'javier.morales@cineplanet.com', 'Javier', 'Morales', 8),
(10, 'Dulcería', 'Activo', '70123456', '987654320', 'laura.castillo@cineplanet.com', 'Laura', 'Castillo', 9);

--
-- Poblando la tabla `producto`
--
INSERT INTO `producto` (`id_producto`, `nombre`, `precio_unitario`) VALUES
(1, 'Canchita Gigante Salada', 25.50),
(2, 'Canchita Grande Salada', 20.00),
(3, 'Canchita Mediana Salada', 15.00),
(4, 'Gaseosa Grande', 12.00),
(5, 'Gaseosa Mediana', 10.00),
(6, 'Hot-Dog Clásico', 8.50),
(7, 'Nachos con Queso', 18.00),
(8, 'Agua Mineral', 6.00),
(9, 'Chocolate Sublime', 5.00),
(10, 'Tequeños', 14.00);

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
INSERT INTO `combos` (`id_combo`, `precio`, `nombre`) VALUES
(1, 35.00, 'Combo Clásico'),
(2, 55.00, 'Combo Pareja'),
(3, 28.00, 'Combo Nachos'),
(4, 25.00, 'Combo Hot-Dog'),
(5, 75.00, 'Combo Familiar'),
(6, 30.00, 'Combo Mediano'),
(7, 22.00, 'Combo Kids'),
(8, 40.00, 'Combo Gigante'),
(9, 60.00, 'Combo Amigos'),
(10, 32.00, 'Combo Tequeños');

--
-- Poblando la tabla `usuario`
--
INSERT INTO `usuario` (`id_usuario`, `correo`) VALUES
(1, 'cliente1@example.com'),
(2, 'cliente2@example.com'),
(3, 'cliente3@example.com'),
(4, 'cliente4@example.com'),
(5, 'cliente5@example.com'),
(6, 'cliente6@example.com'),
(7, 'cliente7@example.com'),
(8, 'cliente8@example.com'),
(9, 'cliente9@example.com'),
(10, 'cliente10@example.com');

--
-- Poblando la tabla `tipo_socio` (3 tipos tradicionales)
--
INSERT INTO `tipo_socio` (`id_socio`, `nombre`, `desc_dulces`, `desc_boleto`) VALUES
(1, 'Socio Clásico', '10%', '15%'),
(2, 'Socio Oro', '15%', '25%'),
(3, 'Socio Premium', '20%', '35%');


INSERT INTO `socio` (`id_usuario`, `nombre`, `apellido`, `genero`, `fecha_nacimiento`, `documento`, `id_tipo_socio`) VALUES
(1, 'Miguel', 'Grau', 'Masculino', '1990-07-27 10:00:00', '12345678', 1),
(2, 'Francisco', 'Bolognesi', 'Masculino', '1985-11-04 15:30:00', '23456789', 2),
(3, 'Andres', 'Caceres', 'Masculino', '2000-02-10 08:45:00', '34567890', 3),
(4, 'Tupac', 'Amaru', 'Masculino', '1995-05-19 12:00:00', '45678901', 1),
(5, 'Micaela', 'Bastidas', 'Femenino', '1998-09-23 18:20:00', '56789012', 2),
(6, 'Jose', 'Olaya', 'Masculino', '1988-01-15 09:00:00', '67890123', 1),
(7, 'Jorge', 'Basadre', 'Masculino', '1992-03-12 20:00:00', '78901234', 3),
(8, 'Ricardo', 'Palma', 'Masculino', '1999-08-07 14:10:00', '89012345', 2),
(9, 'Cesar', 'Vallejo', 'Masculino', '2001-04-16 11:35:00', '90123456', 1),
(10, 'Mario', 'Vargas', 'Masculino', '1986-12-28 22:05:00', '01234567', 3);
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
-- Poblando la tabla `compra_cliente`
--
INSERT INTO `compra_cliente` (`id_descripcion_de_compra`, `tipo`, `id_combo`) VALUES
(1, 'Combo', 1),
(2, 'Combo', 2),
(3, 'Combo', 3),
(4, 'Combo', 4),
(5, 'Individual', NULL),
(6, 'Individual', NULL),
(7, 'Individual', NULL),
(8, 'Combo', 5),
(9, 'Combo', 6),
(10, 'Individual', NULL);

--
-- Poblando la tabla `compra`
--
INSERT INTO `compra` (`id_compra`, `fecha`, `id_usuario`) VALUES
(1, '2025-10-16', 1),
(2, '2025-10-16', 2),
(3, '2025-10-16', 3),
(4, '2025-10-15', 4),
(5, '2025-10-15', 5),
(6, '2025-10-14', 6),
(7, '2025-10-14', 7),
(8, '2025-10-13', 8),
(9, '2025-10-13', 9),
(10, '2025-10-13', 10);

--
-- Poblando la tabla `descripcion_asiento`
--
INSERT INTO `descripcion_asiento` (`id_descripcion`, `id_asiento`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 6),
(5, 7),
(6, 11),
(7, 12),
(8, 15),
(9, 16),
(10, 20);

--
-- Poblando la tabla `compra_boleto`
--
INSERT INTO `compra_boleto` (`id_compra_boleto`, `precio_total_boleto`, `id_compra`, `id_funcion`, `id_descripcion`, `id_sala`, `id_sede`) VALUES
(1, 30.00, 1, 1, 1, 1, 1),
(2, 30.00, 1, 1, 2, 1, 1),
(3, 15.00, 2, 2, 3, 1, 1),
(4, 25.00, 3, 3, 6, 2, 1),
(5, 25.00, 3, 3, 7, 2, 1),
(6, 20.00, 4, 4, 4, 1, 1),
(7, 20.00, 5, 5, 5, 1, 1),
(8, 22.00, 6, 6, 8, 2, 1),
(9, 18.00, 7, 7, 9, 2, 1),
(10, 40.00, 8, 8, 10, 2, 1);

--
-- Poblando la tabla `compra_productos`
--
INSERT INTO `compra_productos` (`id_precio_productos`, `precio_compra`, `id_compra`, `id_producto`, `id_combo`, `id_descripcion_de_compra`) VALUES
(1, 35.00, 1, NULL, 1, 1),
(2, 55.00, 2, NULL, 2, 2),
(3, 18.00, 3, 7, NULL, 5),
(4, 12.00, 4, 4, NULL, 6),
(5, 25.50, 5, 1, NULL, 7),
(6, 25.00, 6, NULL, 4, 4),
(7, 14.00, 7, 10, NULL, 10),
(8, 75.00, 8, NULL, 5, 8),
(9, 30.00, 9, NULL, 6, 9),
(10, 6.00, 10, 8, NULL, 5);

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
INSERT INTO `producto_combo` (`id_productos_combos`, `id_combo`, `id_producto`) VALUES
(1, 1, 2), -- Combo Clásico: Canchita Grande
(2, 1, 5), -- Combo Clásico: Gaseosa Mediana
(3, 2, 1), -- Combo Pareja: Canchita Gigante
(4, 2, 4), -- Combo Pareja: Gaseosa Grande
(5, 2, 4), -- Combo Pareja: Gaseosa Grande
(6, 3, 7), -- Combo Nachos: Nachos
(7, 3, 5), -- Combo Nachos: Gaseosa Mediana
(8, 4, 6), -- Combo Hot-Dog: Hot-Dog
(9, 4, 5), -- Combo Hot-Dog: Gaseosa Mediana
(10, 10, 10), -- Combo Tequeños: Tequeños
(11, 10, 5); -- Combo Tequeños: Gaseosa Mediana