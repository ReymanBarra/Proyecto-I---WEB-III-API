# Imagen base con Apache y PHP 8.2
FROM php:8.2-apache

# Instalar extensiones necesarias
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite (importante para rutas)
RUN a2enmod rewrite

# Copiar tu proyecto a Apache
COPY . /var/www/html/

# Exponer el puerto que Railway usará
EXPOSE 8080

# Cambiar Apache para usar el puerto dinámico que ofrece Railway
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf \
 && sed -i "s/80/\${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Permisos correctos
RUN chown -R www-data:www-data /var/www/html

# Comando que arranca Apache
CMD ["apache2-foreground"]
