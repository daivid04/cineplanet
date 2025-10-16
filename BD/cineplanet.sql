CREATE DATABASE cineplanet;

CREATE TABLE idioma(
	id_idioma INT PRIMARY KEY AUTO_INCREMENT,
    idioma VARCHAR(50) NOT NULL
);

CREATE TABLE idiomas_pelicula(
	id_idiomas_pelicula INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE formato(
	id_formato INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50)
);

CREATE TABLE formato_pelicula(
	id_formato_pelicula INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE pelicula(
	id_pelicula INT primary KEY AUTO_INCREMENT,
    duracion INT NOT NULL,
    url_imagen VARCHAR(50) NOT NULL,
    nombre VARCHAR(80) NOT NULL,
    sinopsis TEXT NOT NULL
);

CREATE TABLE funcion(
	id_funcion INT PRIMARY KEY AUTO_INCREMENT,
    fecha 		DATE NOT NULL,
    hora		TIME NOT NULL
);

CREATE TABLE sala(
	id_sala INT PRIMARY KEY AUTO_INCREMENT,
    num_sala INT NOT NULL
);

CREATE TABLE asiento(
	id_asiento INT PRIMARY KEY AUTO_INCREMENT,
    estado BOOL NOT NULL,
    fila_asiento VARCHAR(5) NOT NULL,
    columna_asiento VARCHAR(5) NOT NULL
);

CREATE TABLE descripcion_asiento(
	id_descripcion INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE sede(
	id_sede INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE compra_boleto(
	id_compra_boleto INT AUTO_INCREMENT PRIMARY KEY,
	precio_total_boleto FLOAT NOT NULL
);

CREATE TABLE compra(
	id_compra INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL
);

CREATE TABLE compra_productos(
	id_precio_productos INT PRIMARY KEY AUTO_INCREMENT,
    precio_compra FLOAT NOT NULL
);

CREATE TABLE compra_cliente(
	id_descripcion_de_compra INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL
);

CREATE TABLE combos(
	id_combo INT PRIMARY KEY AUTO_INCREMENT,
    precio FLOAT NOT NULL,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE producto_combo(
	id_productos_combos INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE producto(
	id_producto INT PRIMARY KEY AUTO_INCREMENT,
    stock INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    precio_unitario FLOAT NOT NULL
);

CREATE TABLE sede(
	id_sede INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE ciudad(
	id_ciudad INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE trabajador(
	id_trabajador INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL,
    esado VARCHAR(50) NOT NULL,
    dni VARCHAR(8) NOT NULL UNIQUE,
    numero VARCHAR(9) NOT NULL,
    correo VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL
);

CREATE TABLE usuario(
	id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    correo varchar(50) NOT NULL
);

-- Esta parte viene a ser heredaro de usuario falta modificar
CREATE TABLE invitado(
	
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE socio(
	nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    genero VARCHAR(20) NOT NULL,
    fecha_nacimiento datetime NOT NULL,
    documento VARCHAR(8) NOT NULL
);

CREATE TABLE tipo_socio(
	id_socio INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    desc_dulces VARCHAR(10) NOT NULL,
    desc_boleto VARCHAR(10) NOT NULL
);