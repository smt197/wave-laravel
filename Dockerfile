FROM php:8.1-fpm

# Installation des dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    default-mysql-client \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install \
        zip \
        pdo \
        pdo_pgsql \
        pgsql \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers du projet
COPY . /var/www/html
COPY .env.example .env

# Installation des dépendances
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage bootstrap/cache

# Génération de la clé d'application
RUN php artisan key:generate

# Installation et configuration de Passport
RUN php artisan passport:install --force

# Exposition du port
EXPOSE 8000

# Démarrage avec artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]