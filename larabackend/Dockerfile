# Use the official PHP 8.4 image with FPM
FROM php:8.4-fpm

ARG APP_ENV=local
# Set environment variables
ENV COMPOSER_HOME app/composer
ENV COMPOSER_CACHE_DIR app/composer/cache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip \
    && docker-php-ext-install zip pdo_mysql
 
# Xdebug Installation and Conditional Configuration 
# Install Xdebug via pecl
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug only if APP_ENV is NOT production
# We create a script that checks the environment variable before loading the config.
# If APP_ENV is 'production', the xdebug.ini file will be empty or removed,
# effectively disabling Xdebug loading at runtime.
RUN if [ "$APP_ENV" != "production" ]; then \
    { \
    echo "xdebug.mode=develop,debug"; \
    echo "xdebug.start_with_request=yes"; \
    echo "xdebug.client_host=host.docker.internal"; \
    echo "xdebug.client_port=9003"; \
    echo "xdebug.idekey=VSCODE"; \
    echo "xdebug.log=/var/log/xdebug.log"; \
    } >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    else \
    # If production, disable the extension entirely to save resources
    rm /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi
    

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . /var/www/html

# Install Composer dependencies
RUN composer install --no-scripts --no-autoloader

# Generate autoloader
RUN composer dump-autoload --optimize

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Command to run PHP-FPM
CMD ["php-fpm"]
