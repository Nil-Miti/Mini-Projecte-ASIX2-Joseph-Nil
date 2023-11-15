#!/bin/bash

 #Para instalar el paquete, como siempre primero actualizamos los repositorios y luego procedemos a instalar el siguiente paquete:

sudo apt update
sudo apt install mariadb-server -y

#Tras la instalación, procedemos a habilitar el servicio al arranque y lo iniciamos:

sudo systemctl start mariadb.service

# Instalar gnupg2
sudo apt install gnupg2 -y

# Descargar la clave pública de MongoDB
wget -nc https://www.mongodb.org/static/pgp/server-6.0.asc

# Agregar la clave pública al keyring de apt
cat server-6.0.asc | gpg --dearmor | sudo tee /etc/apt/trusted.gpg.d/mongodb.gpg >/dev/null

# Agregar el repositorio de MongoDB al sources.list.d
echo "deb [ arch=amd64,arm64 signed-by=/etc/apt/trusted.gpg.d/mongodb.gpg] https://repo.mongodb.org/apt/ubuntu jammy/mongodb-org/6.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongo.list

# Actualizar la lista de paquetes
sudo apt update

# Instalar MongoDB
sudo apt install mongodb-org -y

# Iniciar el servicio de MongoDB
sudo systemctl start mongod


