# Usamos Ubuntu 14.04 como base
FROM ubuntu:14.04

# Actualizamos el sistema y agregamos paquetes necesarios
RUN apt-get update && apt-get install -y \
    software-properties-common \
    python-software-properties \
    wget curl nano apache2 mysql-client \
    libapache2-mod-php5 php5 php5-mysql php5-cli php5-mcrypt \
    php5-curl php5-gd php5-json php5-xmlrpc php5-xsl php5-mbstring unzip zip

# Habilitar mod_rewrite en Apache
RUN a2enmod rewrite

# Configurar Apache para usar prehidrantes.eu
RUN echo "<VirtualHost *:80>
    ServerName prehidrantes.eu
    DocumentRoot /var/www/html
    <Directory /var/www/html>
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog /var/log/apache2/error.log
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Exponer el puerto 80
EXPOSE 80

# Iniciar Apache en foreground
CMD ["apachectl", "-D", "FOREGROUND"]
