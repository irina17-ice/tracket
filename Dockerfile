FROM php:8.2-apache

# Installation des extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql zip

# Activer mod_rewrite pour les URLs
RUN a2enmod rewrite

# Copier tous les fichiers du projet
COPY . /var/www/html/

# Permissions pour les uploads
RUN chown -R www-data:www-data /var/www/html/uploads

# Activer les erreurs PHP pour le débogage
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini
