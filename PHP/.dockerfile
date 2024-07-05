# Usa una imagen base oficial de PHP con Apache
FROM php:7.4-apache

# Copia el contenido de tu proyecto en el directorio de Apache
COPY . /var/www/html/

# Establece permisos adecuados para el directorio de Apache
RUN chown -R www-data:www-data /var/www/html

# Instala las extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Expone el puerto 80 para el servidor web
EXPOSE 80