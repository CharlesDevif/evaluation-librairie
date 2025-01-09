FROM php:8.1-apache

# Activer mod_rewrite pour Apache
RUN a2enmod rewrite

# Installer les dépendances pour PHP et les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install zip pdo_mysql mysqli

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier la configuration personnalisée d'Apache
COPY apache2.conf /etc/apache2/apache2.conf


