FROM php:8.2-apache

# Installation des extensions PHP
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Activer mod_rewrite
RUN a2enmod rewrite

# Créer le dossier uploads AVANT de copier
RUN mkdir -p /var/www/html/uploads/pdfs \
    /var/www/html/uploads/videos \
    /var/www/html/uploads/certificates

# Copier tous les fichiers (sauf ceux exclus)
COPY . /var/www/html/

# Donner les permissions APRÈS la copie
RUN chown -R www-data:www-data /var/www/html/uploads

# Activer les erreurs PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Exposer le port 80
EXPOSE 80
