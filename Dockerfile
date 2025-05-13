FROM php:8.2-apache

# Installation des dépendances nécessaires pour MongoDB + PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libssl-dev \
    pkg-config \
    git \
    unzip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_pgsql pgsql

# Copie de la config PHP
COPY php.ini /usr/local/etc/php/

# Activation de mod_rewrite
RUN a2enmod rewrite

# Copie des fichiers de config Apache et du projet
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

# Attribution des droits
RUN chown -R www-data:www-data /var/www/html

# Exposition du port
EXPOSE 80
