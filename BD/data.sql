USE cineplanet;
-- 1. Tablas sin dependencias
INSERT INTO ciudad (nombre) VALUES 
('Tacna'), ('Lima'), ('Arequipa'), ('Trujillo'), ('Cusco'), 
('Piura'), ('Chiclayo'), ('Huancayo'), ('Iquitos'), ('Puno');

INSERT INTO idioma (idioma) VALUES 
('Español'), ('Inglés'), ('Español (Subtitulado)'), ('Inglés (Subtitulado)'), ('Portugués'),
('Japonés'), ('Coreano (Subtitulado)'), ('Francés'), ('Doblado al Español'), ('Versión Original');

INSERT INTO formato (nombre) VALUES 
('2D'), ('3D'), ('IMAX'), ('4DX'), ('Sala Premium'), 
('Sala Regular'), ('DBOX'), ('Atmos'), ('ScreenX'), ('VIP');

INSERT INTO pelicula (duracion, url_imagen, nombre, sinopsis) VALUES
(148, '/img/dune2.jpg', 'Dune: Parte Dos', 'Paul Atreides se une a los Fremen y busca venganza.'),
(120, '/img/intensamente2.jpg', 'Intensamente 2', 'Riley enfrenta nuevas emociones adolescentes.'),
(115, '/img/kong.jpg', 'Godzilla y Kong: El Nuevo Imperio', 'Dos titanes se unen contra una amenaza colosal.'),
(135, '/img/furiosa.jpg', 'Furiosa: De la saga Mad Max', 'La historia de origen de la guerrera Imperator Furiosa.'),
(100, '/img/garfield.jpg', 'Garfield: Fuera de Casa', 'Garfield se reencuentra con su padre y planea un atraco.'),
(145, '/img/planeta_simios.jpg', 'El Planeta de los Simios: Nuevo Reino', 'Un joven simio emprende un viaje que cuestionará su pasado.'),
(95, '/img/amigos_imaginarios.jpg', 'Amigos Imaginarios', 'Una niña descubre que puede ver a los amigos imaginarios de todos.'),
(169, '/img/oppenheimer.jpg', 'Oppenheimer', 'La historia del físico J. Robert Oppenheimer y la creación de la bomba atómica.'),
(117, '/img/barbie.jpg', 'Barbie', 'Barbie sufre una crisis existencial y se embarca en un viaje de autodescubrimiento.'),
(150, '/img/spiderman_acv.jpg', 'Spider-Man: A través del Spider-Verso', 'Miles Morales es catapultado a través del Multiverso.');

INSERT INTO combos (precio, nombre) VALUES
(25.50, 'Combo Clásico'), (35.00, 'Combo Pareja'), (15.00, 'Combo Kids'),
(28.00, 'Combo Nachos'), (40.00, 'Combo Familiar'), (20.00, 'Combo Hot-Dog'),
(18.00, 'Combo Individual'), (50.00, 'Combo Premium'), (22.00, 'Combo Helado'), (30.00, 'Combo Mixto');

INSERT INTO usuario (correo) VALUES
('juan.perez@email.com'), ('ana.gomez@email.com'), ('carlos.lopez@email.com'), ('lucia.torres@email.com'),
('mario.benites@email.com'), ('sofia.vera@email.com'), ('pedro.salas@email.com'), ('laura.diaz@email.com'),
('invitado_1@email.com'), ('invitado_2@email.com'), ('diego.mena@email.com');

INSERT INTO tipo_socio (nombre, desc_dulces, desc_boleto) VALUES
('Socio Clásico', '10%', '15%'), ('Socio Premium', '20%', '25%'), ('Socio VIP', '25%', '30%');

-- 2. Tablas con dependencias
INSERT INTO sede (nombre, id_ciudad) VALUES 
('Cineplanet Tacna Centro', 1), ('Cineplanet Mall Aventura Lima', 2), ('Cineplanet Real Plaza Arequipa', 3),
('Cineplanet Mall Plaza Trujillo', 4), ('Cineplanet Real Plaza Cusco', 5), ('Cineplanet Piura Centro', 6),
('Cineplanet Open Plaza Chiclayo', 7), ('Cineplanet Real Plaza Huancayo', 8), ('Cineplanet Iquitos', 9),
('Cineplanet Puno Plaza', 10);

INSERT INTO sala (num_sala, id_sede) VALUES 
(1, 1), (2, 1), (3, 2), (4, 2), (5, 3), (6, 4), (7, 5), (8, 6), (9, 7), (10, 8), (11, 9), (12, 10);

-- Asientos para varias salas
INSERT INTO asiento (estado, fila_asiento, columna_asiento, id_sala) SELECT TRUE, 'A', n, 1 FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers;
INSERT INTO asiento (estado, fila_asiento, columna_asiento, id_sala) SELECT TRUE, 'B', n, 1 FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers;
INSERT INTO asiento (estado, fila_asiento, columna_asiento, id_sala) SELECT TRUE, 'A', n, 3 FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers;
INSERT INTO asiento (estado, fila_asiento, columna_asiento, id_sala) SELECT TRUE, 'B', n, 3 FROM (SELECT 1 n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10) numbers;

INSERT INTO trabajador (tipo, esado, dni, numero, correo, nombre, apellido, id_sede) VALUES
('Gerente', 'Activo', '71234567', '987654321', 'ger.tacna@cineplanet.com', 'Maria', 'Quispe', 1),
('Cajero', 'Activo', '77654321', '912345678', 'caj.lima@cineplanet.com', 'Pedro', 'Castillo', 2),
('Staff', 'Activo', '79876543', '923456789', 'stf.aqp@cineplanet.com', 'Luisa', 'Flores', 3),
('Supervisor', 'Activo', '71122334', '934567890', 'sup.tru@cineplanet.com', 'Jorge', 'Mendoza', 4),
('Gerente', 'Activo', '72233445', '945678901', 'ger.cus@cineplanet.com', 'Carmen', 'Soto', 5),
('Cajero', 'Activo', '73344556', '956789012', 'caj.piu@cineplanet.com', 'Ricardo', 'Paredes', 6),
('Staff', 'Activo', '74455667', '967890123', 'stf.chi@cineplanet.com', 'Valeria', 'Rojas', 7),
('Staff', 'Inactivo', '75566778', '978901234', 'stf.huan@cineplanet.com', 'David', 'Luna', 8),
('Cajero', 'Activo', '76677889', '989012345', 'caj.iqu@cineplanet.com', 'Jimena', 'Campos', 9),
('Supervisor', 'Activo', '77788990', '990123456', 'sup.puno@cineplanet.com', 'Marcos', 'Blanco', 10);

INSERT INTO producto (stock, nombre, precio_unitario, id_sede) VALUES
(100, 'Cancha Grande', 15.00, 1), (150, 'Gaseosa Mediana', 8.50, 1), (80, 'Hot-Dog Clásico', 10.00, 2),
(120, 'Nachos con Queso', 18.00, 3), (200, 'Agua Mineral', 5.00, 4), (90, 'Chocolate Sublime', 4.50, 5),
(70, 'Helado de Vainilla', 9.00, 6), (100, 'Café Americano', 7.00, 7), (130, 'Cancha Dulce Pequeña', 12.00, 8),
(60, 'M&Ms', 6.00, 9);

INSERT INTO funcion (fecha, hora, id_pelicula, id_sala) VALUES
('2025-10-17', '18:30:00', 1, 1), ('2025-10-17', '20:00:00', 2, 2), ('2025-10-18', '19:00:00', 3, 3),
('2025-10-18', '21:30:00', 4, 4), ('2025-10-19', '16:00:00', 5, 5), ('2025-10-19', '17:45:00', 6, 6),
('2025-10-20', '18:00:00', 7, 7), ('2025-10-20', '20:30:00', 8, 8), ('2025-10-21', '19:15:00', 9, 9),
('2025-10-21', '22:00:00', 10, 10);

-- 3. Tablas de Relaciones y Herencia
INSERT INTO formato_pelicula (id_pelicula, id_formato) VALUES
(1, 1), (1, 3), (2, 1), (2, 2), (3, 1), (4, 1), (5, 2), (6, 3), (7, 1), (8, 1), (8, 3), (9, 1), (10, 1);
INSERT INTO idiomas_pelicula (id_pelicula, id_idioma) VALUES
(1, 1), (1, 3), (2, 1), (2, 2), (3, 1), (4, 3), (5, 9), (6, 1), (7, 1), (8, 4), (9, 9), (10, 1);
INSERT INTO producto_combo (id_combo, id_producto) VALUES
(1, 1), (1, 2), (2, 1), (2, 2), (4, 4), (6, 3);

INSERT INTO socio (id_usuario, nombre, apellido, genero, fecha_nacimiento, documento, id_tipo_socio) VALUES
(1, 'Juan', 'Perez', 'Masculino', '1990-05-15', '45123123', 2),
(2, 'Ana', 'Gomez', 'Femenino', '1995-03-10', '48234234', 1),
(3, 'Carlos', 'Lopez', 'Masculino', '1985-11-20', '29876543', 3),
(4, 'Lucia', 'Torres', 'Femenino', '2000-01-30', '71234567', 1),
(5, 'Mario', 'Benites', 'Masculino', '1998-07-22', '72345678', 2);
INSERT INTO invitado (id_usuario, nombre) VALUES (9, 'Invitado 1'), (10, 'Invitado 2');

-- 4. Transacciones de Compra
-- Compras
INSERT INTO compra (fecha, id_usuario) VALUES 
('2025-10-16', 1), ('2025-10-16', 9), ('2025-10-17', 2), ('2025-10-17', 3), ('2025-10-18', 4),
('2025-10-18', 10),('2025-10-19', 5), ('2025-10-19', 1), ('2025-10-20', 2), ('2025-10-21', 3);

-- Asientos para las compras
INSERT INTO descripcion_asiento (id_asiento) VALUES (1),(2),(3),(4),(5),(21),(22),(23),(24),(25);

-- Descripciones de compra (cliente)
INSERT INTO compra_cliente (tipo, id_combo) VALUES
('Solo Boletos', NULL), ('Boletos y Dulcería', 1), ('Boletos y Dulcería', 2), ('Solo Boletos', NULL),
('Boletos y Dulcería', 4), ('Solo Boletos', NULL), ('Boletos y Dulcería', 5), ('Solo Dulcería', 1),
('Solo Boletos', NULL), ('Boletos y Dulcería', 6);

-- Boletos
INSERT INTO compra_boleto (precio_total_boleto, id_compra, id_funcion, id_descripcion, id_sala, id_sede) VALUES
(20.00, 1, 1, 1, 1, 1), (22.00, 2, 1, 2, 1, 1), (18.50, 3, 2, 3, 2, 1), (25.00, 4, 3, 4, 3, 2),
(19.00, 5, 4, 5, 4, 2), (21.00, 6, 5, 6, 5, 3), (23.00, 7, 6, 7, 6, 4);

-- Productos comprados
INSERT INTO compra_productos (precio_compra, id_compra, id_producto, id_combo, id_descripcion_de_compra) VALUES
(25.50, 2, NULL, 1, 2), (35.00, 3, NULL, 2, 3), (18.00, 5, NULL, 4, 5), 
(40.00, 7, NULL, 5, 7), (15.00, 8, 1, NULL, 8), (10.00, 10, 3, NULL, 10);

