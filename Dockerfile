FROM php:8.2-apache

# Installation des extensions PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Activer mod_rewrite
RUN a2enmod rewrite

# Créer les dossiers
RUN mkdir -p /var/www/html/uploads/pdfs \
    /var/www/html/uploads/videos \
    /var/www/html/uploads/certificates

# Copier TOUS les fichiers
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/uploads
RUN chmod -R 755 /var/www/html

# Activer les erreurs PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Configurer Apache
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

EXPOSE 80
