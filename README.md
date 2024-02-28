# Fase 0
#Joseph, Nil, Alex
PDF/Genial.ly --> https://view.genial.ly/64fef8dfe0ea4f001294ad62
Video API VirusTotal --> https://www.youtube.com/watch?v=1FCSqV__Ot0

Ante los poblemas dados para ejecutar el script de instalacion en virtualbox se recomienda utilizar vmware para la instalacion de los servicios.

# Hemos utilizado los siguientes comandos para instalar y configurar el servicio apache:

sudo apt install apache2

sudo apt update

sudo apt install php

sudo ufw allow 'Apache'

sudo systemctl start apache2

sudo snap install curl

curl -4 icanhazip.com

#CREAR UN FICHERO EN sites-aviable y configurarlo:

<VirtualHost *:80>    

ServerAdmin webmaster@localhost

ServerName servidor

ServerAlias www.servidor

DocumentRoot /var/www/servidor

ErrorLog ${APACHE_LOG_DIR}/error.log

CustomLog ${APACHE_LOG_DIR}/access.log combined

</VirtualHost>

sudo a2ensite servidor.conf

sudo a2dissite 000-default.conf

sudo systemctl restart apache2

# Instalar MARIADB Y MONGODB

apt update

apt install mariadb-server -y

apt update

#Tras la instalación, procedemos a habilitar el servicio al arranque y lo iniciamos:

systemctl start mariadb.service
 
#Instalar gnupg2

apt install gnupg2 -y

#Descargar la clave pública de MongoDB

wget -nc https://www.mongodb.org/static/pgp/server-6.0.asc

#Agregar la clave pública al keyring de apt

cat server-6.0.asc | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/mongodb.gpg >/dev/null

#Agregar el repositorio de MongoDB al sources.list.d

echo "deb [ arch=amd64,arm64 signed-by=/etc/apt/trusted.gpg.d/mongodb.gpg] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongo.list

#Actualizar la lista de paquetes

apt update

#Instalar MongoDB

apt install mongodb-org -y

#Iniciar el servicio de MongoDB

systemctl start mongod

#CREAR BASE DE DATOS EN MARIADB

sudo mariadb

USE mysql;
CREATE DATABASE IF NOT EXISTS server;

USE server;

#CREAR TABLA Usuario

CREATE TABLE usuario ( 
    ID_USER INT AUTO_INCREMENT,
    USER_NAME VARCHAR(50) NOT NULL,  
     PASSW VARCHAR(255) NOT NULL,
     email VARCHAR(255),
     estado ENUM('autorizado','no_autorizado','espera') DEFAULT espera,
     PRIMARY KEY (ID_USER));
     
#CREAR TABLA DE COMPARTIDOS:

CREATE TABLE Compartidos (
         IS_Carpeta INT AUTO_INCREMENT PRIMARY KEY,
         Nombre_Carpeta VARCHAR(50));
    
#CREAR TABLA GRUPOS;

CREATE TABLE grupos(
  ID_GRUPO INT AUTO_INCREMENT PRIMARY KEY,  
  NOMBRE_GRUPO varchar(50) NOT NULL);

 #CREAR TABLA DE RELACION DE USUARIO POR GRUPO

 CREATE TABLE usuarioxgrupo (
    ID_REGISTRO int(11) NOT NULL AUTO_INCREMENT,
    ID_USER int(11),
    ID_GRUPO int(11),
    PRIMARY KEY (ID_REGISTRO),
    FOREIGN KEY (ID_USER) REFERENCES usuario(ID_USER),
    FOREIGN KEY (ID_GRUPO) REFERENCES grupos(ID_GRUPO));

#CREAR TABLA DE RELACION USUARIO POR CARPETA COMPARTIDA

CREATE TABLE usuarioxcarpeta (
    ID_REGISTRO int(11) NOT NULL AUTO_INCREMENT,
    ID_USER int(11),
    ID_GRUPO int(11),
    PRIMARY KEY (ID_REGISTRO),
    FOREIGN KEY (ID_USER) REFERENCES usuario(ID_USER),
    FOREIGN KEY (ID_Carpeta) REFERENCES Compartidos(ID_Carpeta));

   
