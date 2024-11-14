# Utiliser une image PHP officielle avec FPM
FROM php:8.1-fpm

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    libonig-dev \
    pkg-config \
    libssl-dev \
    libpq-dev  # Ajout de la bibliothèque PostgreSQL

# Installer les extensions PHP requises pour Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_pgsql  # Installation du driver pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier le projet Laravel dans le conteneur
WORKDIR /var/www
COPY . .

# Installer les dépendances PHP
RUN composer install --optimize-autoloader --no-dev

# Générer les clés pour Laravel Passport
RUN php artisan passport:keys

# Changer les permissions pour les fichiers Laravel (storage et cache)
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Installer Nginx
RUN apt-get install -y nginx

# Copier la configuration Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Exposer le port 80 pour Nginx
EXPOSE 80

# Lancer Nginx et PHP-FPM
CMD ["sh", "-c", "service nginx start && php-fpm"]