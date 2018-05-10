create database maps;
use maps;

create table quadras
(
	idquadra INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	poligono VARCHAR(255) NOT NULL
) ENGINE = InnoDB;