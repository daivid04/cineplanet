-- Limpiar tablas de horarios (orden inverso por FKs)
DELETE FROM horario_trabajador;
DELETE FROM horarios_de_trabajo;
DELETE FROM dia_laborable;

-- Reset AUTO_INCREMENT
ALTER TABLE horario_trabajador AUTO_INCREMENT = 1;
ALTER TABLE horarios_de_trabajo AUTO_INCREMENT = 1;
ALTER TABLE dia_laborable AUTO_INCREMENT = 1;

-- 1. Crear horarios de trabajo (Time slots)
-- Lunes 14 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(1, '2024-10-14 09:00:00', '2024-10-14 17:00:00'), -- Apertura
(2, '2024-10-14 12:00:00', '2024-10-14 20:00:00'), -- Intermedio
(3, '2024-10-14 15:00:00', '2024-10-14 23:00:00'); -- Cierre

-- Martes 15 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(4, '2024-10-15 09:00:00', '2024-10-15 17:00:00'),
(5, '2024-10-15 12:00:00', '2024-10-15 20:00:00'),
(6, '2024-10-15 15:00:00', '2024-10-15 23:00:00');

-- Miércoles 16 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(7, '2024-10-16 09:00:00', '2024-10-16 17:00:00'),
(8, '2024-10-16 12:00:00', '2024-10-16 20:00:00'),
(9, '2024-10-16 15:00:00', '2024-10-16 23:00:00');

-- Jueves 17 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(10, '2024-10-17 09:00:00', '2024-10-17 17:00:00'),
(11, '2024-10-17 12:00:00', '2024-10-17 20:00:00'),
(12, '2024-10-17 15:00:00', '2024-10-17 23:00:00');

-- Viernes 18 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(13, '2024-10-18 09:00:00', '2024-10-18 17:00:00'),
(14, '2024-10-18 12:00:00', '2024-10-18 20:00:00'),
(15, '2024-10-18 15:00:00', '2024-10-18 23:00:00');

-- Sábado 19 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(16, '2024-10-19 09:00:00', '2024-10-19 17:00:00'),
(17, '2024-10-19 12:00:00', '2024-10-19 20:00:00'),
(18, '2024-10-19 15:00:00', '2024-10-19 23:00:00');

-- Domingo 20 Oct 2024
INSERT INTO horarios_de_trabajo (id_h_trabajo, entrada, salida) VALUES 
(19, '2024-10-20 09:00:00', '2024-10-20 17:00:00'),
(20, '2024-10-20 12:00:00', '2024-10-20 20:00:00'),
(21, '2024-10-20 15:00:00', '2024-10-20 23:00:00');


-- 2. Crear dias laborables para empleados (Asignar turnos genéricos)
-- Juan Perez (ID 1)
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (1, 'Mañana', 1);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (2, 'Mañana', 1);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (3, 'Tarde', 1);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (4, 'Noche', 1);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (5, 'Noche', 1);

-- Maria Gomez (ID 2)
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (6, 'Mañana', 2);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (7, 'Mañana', 2);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (8, 'Tarde', 2);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (9, 'Noche', 2);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (10, 'Noche', 2);

-- Carlos Rodriguez (ID 3)
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (11, 'Tarde', 3);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (12, 'Mañana', 3);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (13, 'Mañana', 3);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (14, 'Tarde', 3);
INSERT INTO dia_laborable (id_dia_laborable, turno, id_trabajador) VALUES (15, 'Noche', 3);


-- 3. Asignar horarios específicos (Linking)
-- Juan Perez (ID 1)
-- Lunes 14 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 1, 1);
-- Martes 15 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 4, 2);
-- Jueves 17 (Intermedio)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Intermedio', 11, 3);
-- Viernes 18 (Cierre)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Cierre', 15, 4);
-- Sábado 19 (Cierre)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Cierre', 18, 5);

-- Maria Gomez (ID 2)
-- Martes 15 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 4, 6);
-- Miércoles 16 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 7, 7);
-- Jueves 17 (Intermedio)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Intermedio', 11, 8);
-- Viernes 18 (Cierre)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Cierre', 15, 9);
-- Sábado 19 (Cierre)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Cierre', 18, 10);

-- Carlos Rodriguez (ID 3)
-- Lunes 14 (Intermedio)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Intermedio', 2, 11);
-- Miércoles 16 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 7, 12);
-- Jueves 17 (Apertura)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Apertura', 10, 13);
-- Viernes 18 (Intermedio)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Intermedio', 14, 14);
-- Domingo 20 (Cierre)
INSERT INTO horario_trabajador (horario, id_h_trabajo, id_dia_laborable) VALUES ('Cierre', 21, 15);
