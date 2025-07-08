FROM php:8.1-apache

# Copiar los archivos al contenedor
COPY . /var/www/html/

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar configuración personalizada de Apache
COPY .htaccess /var/www/html/

# Permitir que Apache lea .htaccess
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

EXPOSE 80
