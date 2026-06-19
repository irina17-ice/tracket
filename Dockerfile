FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpq-dev \
    libonig-dev \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        zip \
        mbstring \
        xml \
        dom

# Activer mod_rewrite
RUN a2enmod rewrite

# Créer les dossiers uploads
RUN mkdir -p /var/www/html/uploads/pdfs \
    /var/www/html/uploads/videos \
    /var/www/html/uploads/certificates

# Copier les fichiers
COPY . /var/www/html/

# Donner les permissions
RUN chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 755 /var/www/html

# Configurer PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Configurer Apache
RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]