# Usa una imagen base de PHP con Apache
FROM php:8.2-apache

# Instala extensiones necesarias (pdo_mysql para conectar con MySQL)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copia tu código fuente al contenedor
COPY . /var/www/html/

# Establece permisos (opcional)
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto 80 (para Apache)
EXPOSE 80
