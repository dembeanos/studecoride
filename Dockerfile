FROM php:8.2-apache

# 1) Installer dépendances système, Node.js, Composer, extensions PHP
RUN apt-get update && apt-get install -y \
      libpq-dev libssl-dev pkg-config git unzip curl gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://getcomposer.org/installer | php -- \
         --install-dir=/usr/local/bin --filename=composer \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Installer ClamAV et le configurer
RUN apt-get update && apt-get install -y \
      clamav clamav-daemon clamav-freshclam \
    && freshclam \
    && sed -i 's/^#TCPSocket 3310/TCPSocket 3310/' /etc/clamav/clamd.conf \
    && sed -i 's/^#TCPAddr 127.0.0.1/TCPAddr 127.0.0.1/' /etc/clamav/clamd.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3) Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# 4) Définir le dossier de travail
WORKDIR /var/www/html

# 5) Copier tout le code du projet
COPY . .

# 6) Installer dépendances PHP (si composer.json existe)
RUN if [ ! -f composer.json ]; then \
      composer init \
        --name=ecoride/app \
        --require="mongodb/mongodb:^2.0" \
        --no-interaction && \
      composer install --no-dev --optimize-autoloader; \
    else \
      composer install --no-dev --optimize-autoloader; \
    fi


# 7) Installer dépendances JS (si package.json existe)
RUN if [ ! -f package.json ]; then \
      npm init -y && \
      npm install chart.js clamav.js; \
    else \
      npm install; \
    fi

# 8) Droits
RUN chown -R www-data:www-data /var/www/html

# 9) Copier les configs
COPY php.ini /usr/local/etc/php/
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 10) Exposer le port
EXPOSE 80

# 11) Entrypoint
ENTRYPOINT ["entrypoint.sh"]
