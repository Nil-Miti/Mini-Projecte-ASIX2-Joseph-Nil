#!/bin/bash

#Para instalar el paquete, como siempre primero actualizamos los repositorios y luego procedemos a instalar el siguiente paquete:

apt update
apt install mariadb-server -y
apt install apache2
apt update
apt install php

#Tras la instalación, procedemos a habilitar el servicio al arranque y lo iniciamos:

systemctl start mariadb.service
 
# Instalar gnupg2
apt install gnupg2 -y

# Descargar la clave pública de MongoDB
wget -nc https://www.mongodb.org/static/pgp/server-6.0.asc

# Agregar la clave pública al keyring de apt
cat server-6.0.asc | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/mongodb.gpg >/dev/null

# Agregar el repositorio de MongoDB al sources.list.d
echo "deb [ arch=amd64,arm64 signed-by=/etc/apt/trusted.gpg.d/mongodb.gpg] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongo.list

# Actualizar la lista de paquetes
apt update

# Instalar MongoDB
apt install mongodb-org -y

# Iniciar el servicio de MongoDB
systemctl start mongod

#CREAR BASE DE DATOS EN MARIADB
sudo mysql <<EOF
USE mysql;
CREATE DATABASE IF NOT EXISTS server;
USE server;
CREATE TABLE usuario (
    ID_USER INT AUTO_INCREMENT,
    USER_NAME VARCHAR(50) NOT NULL,
    PASSW VARCHAR(255) NOT NULL,
    PRIMARY KEY (ID_USER)
);
