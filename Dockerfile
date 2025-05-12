FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql

COPY php.ini /usr/local/etc/php/

RUN a2enmod rewrite

COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
