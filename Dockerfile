FROM php:7.4.30-fpm

# Installer les dépendances nécessaires pour PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /var/www/symfony

# Copier le projet dans le conteneur
COPY . .

# Installer les dépendances PHP avec Composer
RUN composer install --no-interaction --optimize-autoloader

# Installer les dépendances frontend avec npm
RUN npm install

# Si vous utilisez Webpack Encore pour gérer les assets
RUN npm run dev # ou npm run build pour la production
