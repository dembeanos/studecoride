# ───────────────────────────────────────────────────
# Dockerfile
# ───────────────────────────────────────────────────

FROM php:8.2-apache

# 1) Installer les dépendances système + Node.js + Composer
RUN apt-get update && apt-get install -y \
      libpq-dev \
      libssl-dev \
      pkg-config \
      git \
      unzip \
      curl \
      gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && curl -sS https://getcomposer.org/installer | php -- \
         --install-dir=/usr/local/bin --filename=composer \
    # Extensions PHP
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install pdo_pgsql pgsql \
    # Nettoyage
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 2) Installer ClamAV et mettre à jour la base de signatures
RUN apt-get update && apt-get install -y \
      clamav \
      clamav-daemon \
      clamav-freshclam \
    && freshclam \
    # Autoriser clamd à écouter TCP sur 127.0.0.1:3310
    && sed -i 's/^#TCPSocket 3310/TCPSocket 3310/' /etc/clamav/clamd.conf \
    && sed -i 's/^#TCPAddr 127.0.0.1/TCPAddr 127.0.0.1/' /etc/clamav/clamd.conf \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3) Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# 4) Copier les configurations PHP & Apache
COPY php.ini /usr/local/etc/php/
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

# 5) Définir le dossier de travail
WORKDIR /var/www/html

# 6) Copier uniquement les fichiers nécessaires à Composer pour installer les dépendances
COPY composer.json composer.lock* ./

# 7) Installer les dépendances PHP via Composer et générer le dossier vendor + autoload
RUN composer install --no-dev --optimize-autoloader

# 8) Copier le reste du projet (après que vendor ait été généré)
COPY . /var/www/html

# 9) Initialiser npm et installer les dépendances JS
RUN if [ ! -f package.json ]; then \
      npm init -y && \
      npm install chart.js clamav.js; \
    fi && \
    npm install

# 10) Donner les droits au répertoire
RUN chown -R www-data:www-data /var/www/html

# 11) Copier et rendre exécutable le script d’entrée
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# 12) Exposer le port Apache
EXPOSE 80

# 13) Lancer le script d’entrée
ENTRYPOINT ["entrypoint.sh"]
