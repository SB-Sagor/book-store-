# Use the official PHP image with Apache
FROM php:8.1-apache

# Copy all project files to the web root directory
COPY . /var/www/html/

# Enable Apache rewrite module (if needed for routing)
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
