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

#CREAR UN FICHERO EN /etc/apache/sites-aviable y configurarlo con el nombre de servidor.conf:

      <VirtualHost *:80>    
      
      ServerAdmin webmaster@localhost
      
      ServerName servidor
      
      ServerAlias www.servidor
      
      DocumentRoot /var/www/servidor
      
      ErrorLog ${APACHE_LOG_DIR}/error.log
      
      CustomLog ${APACHE_LOG_DIR}/access.log combined
      
      </VirtualHost>

#Comandos para arrancar la configuración anterior.

     sudo a2ensite servidor.conf
     
     sudo a2dissite 000-default.conf
     
     sudo systemctl restart apache2

# Instalar MARIADB 

      apt update
      
      apt install mariadb-server -y
      
      apt update
      
      #Tras la instalación, procedemos a habilitar el servicio al arranque y lo iniciamos:
      
      systemctl start mariadb.service
      
      sudo apt install php-mysqli
     
#Instalar MONGODB en DOCKER

       sudo apt update
       sudo apt install apt-transport-https ca-certificates curl software-properties-common

      curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg

      echo "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

      sudo apt update
      sudo apt install docker-ce docker-ce-cli containerd.io

      sudo systemctl status docker

      docker pull composer
      docker run --name php-composer -v $(pwd):/app composer
      docker pull mongo
      docker run --name mongodb -d -p 27017:27017 -v mongo-data:/data/db mongo
      //para ingresar al mongodb se tendra que ingresar:sudo docker exec -it mongodb mongo
      //en la carpeta del servidor hay un docker-compose, si os interesa habra que ingresar:docker-compose up -d
      

#CREAR BASE DE DATOS EN MARIADB

    sudo mariadb

    USE mysql;
    CREATE DATABASE IF NOT EXISTS server;

    USE server;
#CREAR USUARIO ADMIN PARA LA BASE DE DATOS

      CREATE USER 'admin'@'localhost' IDENTIFIED BY '1234';
      GRANT ALL PRIVILEGES ON server.* TO 'admin'@'localhost';
      FLUSH PRIVILEGES;

#CREAR TABLA Usuario

    CREATE TABLE usuario ( 

    ID_USER INT AUTO_INCREMENT,
    
    USER_NAME VARCHAR(50) NOT NULL,  
    
     PASSW VARCHAR(255) NOT NULL,
     
     email VARCHAR(255),
     
     estado ENUM('autorizado','no_autorizado','espera') DEFAULT 'espera',
     
     PRIMARY KEY (ID_USER));
     
#CREAR TABLA DE COMPARTIDOS:

        CREATE TABLE Compartidos (

         ID_Carpeta INT AUTO_INCREMENT PRIMARY KEY,
         
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
    
    ID_Carpeta int(11),
    
    PRIMARY KEY (ID_REGISTRO),
    
    FOREIGN KEY (ID_USER) REFERENCES usuario(ID_USER),
    
    FOREIGN KEY (ID_Carpeta) REFERENCES Compartidos(ID_Carpeta));

   
