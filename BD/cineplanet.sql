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
	id_asiento INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE descripcion_aciento(
	id_descripcion INT PRIMARY KEY AUTO_INCREMENT
);

