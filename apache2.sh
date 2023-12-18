#!/bin/bash

sudo apt install apache2
sudo apt update
sudo apt install php
sudo ufw allow 'Apache'
sudo systemctl start apache2
sudo snap install curl
curl -4 icanhazip.com
#CREAR UN FICHERO EN sites-aviable y configurarlo:
#<VirtualHost *:80>      
#    ServerAdmin webmaster@localhost
#    ServerName servidor
#    ServerAlias www.servidor
#    DocumentRoot /var/www/servidor
#    ErrorLog ${APACHE_LOG_DIR}/error.log
#    CustomLog ${APACHE_LOG_DIR}/access.log combined
#</VirtualHost>
sudo a2ensite servidor.conf
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
