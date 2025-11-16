FROM php:8.2-apache

# Habilitar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar todo el código al servidor
COPY . /var/www/html/

# Dar permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer el puerto
EXPOSE 80
