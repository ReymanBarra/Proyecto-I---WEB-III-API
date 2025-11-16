FROM php:8.2-apache

# Habilitar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Cambiar el puerto por defecto de Apache a 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf
RUN sed -i 's/80/8080/g' /etc/apache2/sites-enabled/000-default.conf

# Copiar el código al servidor
COPY . /var/www/html/

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Exponer puerto 8080
EXPOSE 8080

# Iniciar Apache
CMD ["apache2-foreground"]
