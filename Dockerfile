FROM php:8.2-apache

# Extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Copiar SOLO el contenido del proyecto
COPY . /var/www/html/

# Activar mod_rewrite
RUN a2enmod rewrite

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html
