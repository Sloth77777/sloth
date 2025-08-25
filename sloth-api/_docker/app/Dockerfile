FROM php:8.2-fpm

LABEL maintainer="Developer Team"
LABEL version="1.0"
LABEL description="PHP 8.2 development environment with Node.js"

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
      apt-utils \
      libpq-dev \
      libpng-dev \
      libzip-dev \
      zip \
      unzip \
      git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) \
      bcmath \
      gd \
      zip \
      pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*


COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin \
    && composer --version

# Install Node.js 20.x
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get update \
    && apt-get install -y --no-install-recommends nodejs \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && npm install -g npm@latest


WORKDIR /var/www

