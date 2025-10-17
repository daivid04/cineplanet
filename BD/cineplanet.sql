DROP DATABASE IF EXISTS cineplanet;
CREATE DATABASE cineplanet;
USE cineplanet;

CREATE TABLE ciudad(
    id_ciudad INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE sede(
    id_sede INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    id_ciudad INT NOT NULL,
    FOREIGN KEY (id_ciudad) REFERENCES ciudad(id_ciudad) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE sala(
    id_sala INT PRIMARY KEY AUTO_INCREMENT,
    num_sala INT NOT NULL,
    id_sede INT NOT NULL,
    FOREIGN KEY (id_sede) REFERENCES sede(id_sede) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE asiento(
    id_asiento INT PRIMARY KEY AUTO_INCREMENT,
    estado BOOL NOT NULL,
    fila_asiento VARCHAR(5) NOT NULL,
    columna_asiento VARCHAR(5) NOT NULL,
    id_sala INT NOT NULL,
    FOREIGN KEY (id_sala) REFERENCES sala(id_sala) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE pelicula(
    id_pelicula INT PRIMARY KEY AUTO_INCREMENT,
    duracion INT NOT NULL,
    url_imagen VARCHAR(255) NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    sinopsis TEXT NOT NULL
);

CREATE TABLE idioma(
    id_idioma INT PRIMARY KEY AUTO_INCREMENT,
    idioma VARCHAR(50) NOT NULL
);

CREATE TABLE formato(
    id_formato INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50)
);

CREATE TABLE funcion(
    id_funcion INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    id_pelicula INT NOT NULL,
    id_sala INT NOT NULL,
    detalles_funcion JSON NULL,
    FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_sala) REFERENCES sala(id_sala) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE trabajador(
    id_trabajador INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,
    estado VARCHAR(50) NOT NULL,
    dni VARCHAR(8) NOT NULL UNIQUE,
    numero VARCHAR(9) NOT NULL,
    correo VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    id_sede INT NOT NULL,
    FOREIGN KEY (id_sede) REFERENCES sede(id_sede) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE producto(
    id_producto INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    precio_unitario FLOAT NOT NULL
);

CREATE TABLE producto_sede(
    id_producto_sede INT PRIMARY KEY AUTO_INCREMENT,
    stock INT NOT NULL,
    id_producto INT NOT NULL,
    id_sede INT NOT NULL,
    FOREIGN KEY(id_producto) REFERENCES producto(id_producto)
    ON UPDATE CASCADE
	ON DELETE RESTRICT,
    FOREIGN KEY(id_sede) REFERENCES sede(id_sede)
    ON UPDATE CASCADE
	ON DELETE RESTRICT
);


CREATE TABLE combos(
    id_combo INT PRIMARY KEY AUTO_INCREMENT,
    precio FLOAT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    contenido_combo JSON NULL
);

CREATE TABLE usuario(
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    correo varchar(50) NOT NULL
);

CREATE TABLE tipo_socio(
    id_socio INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    desc_dulces VARCHAR(10) NOT NULL,
    desc_boleto VARCHAR(10) NOT NULL
);

CREATE TABLE socio(
    id_usuario INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    genero VARCHAR(20) NOT NULL,
    fecha_nacimiento DATETIME NOT NULL,
    documento VARCHAR(8) NOT NULL,
    id_tipo_socio INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_tipo_socio) REFERENCES tipo_socio(id_socio) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE invitado(
    id_usuario INT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE compra_cliente(
    id_descripcion_de_compra INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,
    id_combo INT, -- Puede ser null si la compra no incluye un combo
    FOREIGN KEY (id_combo) REFERENCES combos(id_combo) 
    ON UPDATE CASCADE 
    ON DELETE SET NULL
);

CREATE TABLE compra(
    id_compra INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL,
    id_usuario INT NOT NULL,
    detalles_pago JSON NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE descripcion_asiento(
    id_descripcion INT PRIMARY KEY AUTO_INCREMENT,
    id_asiento INT NOT NULL,
    FOREIGN KEY (id_asiento) REFERENCES asiento(id_asiento) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE compra_boleto(
    id_compra_boleto INT AUTO_INCREMENT PRIMARY KEY,
    precio_total_boleto FLOAT NOT NULL,
    id_compra INT NOT NULL,
    id_funcion INT NOT NULL,
    id_descripcion INT NOT NULL,
    id_sala INT NOT NULL,
    id_sede INT NOT NULL,
    FOREIGN KEY (id_compra) REFERENCES compra(id_compra) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_funcion) REFERENCES funcion(id_funcion) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,
    FOREIGN KEY (id_descripcion) REFERENCES descripcion_asiento(id_descripcion) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,
    FOREIGN KEY (id_sala) REFERENCES sala(id_sala) 
    ON UPDATE CASCADE 
    ON DELETE NO ACTION,
    FOREIGN KEY (id_sede) REFERENCES sede(id_sede) 
    ON UPDATE CASCADE 
    ON DELETE NO ACTION
);

CREATE TABLE compra_productos(
    id_precio_productos INT PRIMARY KEY AUTO_INCREMENT,
    precio_compra FLOAT NOT NULL,
    id_compra INT NOT NULL,
    id_producto INT,
    id_combo INT,
    id_descripcion_de_compra INT NOT NULL,
    FOREIGN KEY (id_compra) REFERENCES compra(id_compra) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,
    FOREIGN KEY (id_combo) REFERENCES combos(id_combo) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT,
    FOREIGN KEY (id_descripcion_de_compra) REFERENCES compra_cliente(id_descripcion_de_compra) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
);

CREATE TABLE idiomas_pelicula(
    id_idiomas_pelicula INT PRIMARY KEY AUTO_INCREMENT,
    id_pelicula INT NOT NULL,
    id_idioma INT NOT NULL,
    FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_idioma) REFERENCES idioma(id_idioma) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE formato_pelicula(
    id_formato_pelicula INT PRIMARY KEY AUTO_INCREMENT,
    id_pelicula INT NOT NULL,
    id_formato INT NOT NULL,
    FOREIGN KEY (id_pelicula) REFERENCES pelicula(id_pelicula) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_formato) REFERENCES formato(id_formato) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

CREATE TABLE producto_combo(
    id_productos_combos INT PRIMARY KEY AUTO_INCREMENT,
    id_combo INT NOT NULL,
    id_producto INT NOT NULL,
    FOREIGN KEY (id_combo) REFERENCES combos(id_combo) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto) 
    ON UPDATE CASCADE 
    ON DELETE CASCADE
);

