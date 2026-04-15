FROM php:8.2-apache

# Install MySQL PDO extension
RUN docker-php-ext-install pdo pdo_mysql

# Optional: Install other useful extensions
RUN docker-php-ext-install mysqli

# Copy project files (done via volume in compose, but can be here if needed)