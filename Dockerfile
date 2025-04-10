# Use official PHP with Apache
FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install mysqli and other dependencies
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Optional: Set working directory
WORKDIR /var/www/html

# Copy all files to container
COPY . .

# Expose port (optional)
EXPOSE 80
